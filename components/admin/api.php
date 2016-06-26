<?php

// Для вебмастера - страница со списком потоков
// Для админа - список потоков + запросы на api-доступ

// проверяем доступ к API
$access = FALSE;
$hash = "";

if (User::isAdmin()) {
	$access = TRUE;
} else {

	$query = "SELECT hash FROM api_requests WHERE user_id = ? AND status = 'accepted'";
	$stmt = $GLOBALS['DB']->prepare($query);
	$stmt->execute([
		User::get_current_user_id()
	]);


	if ($stmt->rowCount()) {
		$hash = $stmt->fetchColumn();
		$api_link = get_api_url() . "/help?hash=" . $hash;
		$access = TRUE;
	} 
}

if ($access) {
	if (empty($_REQUEST['k'])) {

		$filters = [
			"offers" => []
		];

		if (User::isAdmin()) {

			// Список вебмастеров для фильтра по вебмастерам
			$filters["partners"] = [];

			$query = "SELECT u.user_id AS id, u.login 
				  	  FROM users AS u NATURAL JOIN user_role AS ur
				 	  WHERE u.status = 2 AND ur.role_id = 2";
			$stmt = $GLOBALS['DB']->query($query);
			while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$filters["partners"][$a['id']] = $a['login'];
			}

			// Список офферов
			$stmt = $GLOBALS['DB']->query("SELECT id, name FROM goods");
			while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$filters["offers"][$a['id']] = $a['name'];
			}

			require_once $_SERVER['DOCUMENT_ROOT'] . "/templates/admin/api/admin.php";
		} else {

			// Список офферов
			$query = "SELECT g.name, g.id
			        FROM goods AS g INNER JOIN users2goods AS ug ON ug.g_id = g.id
			        WHERE g.available_in_offers = 1 AND g.offer_status = '" . Offer::STATUS_ACTIVE . "' AND ug.u_id = ?";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->execute([
				User::get_current_user_id()
			]);

			while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$filters["offers"][$a['id']] = $a['name'];
			}

			require_once $_SERVER['DOCUMENT_ROOT'] . "/templates/admin/api/partner.php";
		}

		enqueue_scripts([
			    "/assets/global/plugins/datatables/datatables.min.js",
			    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
			    "/assets/global/scripts/datatable.js",
			    "/misc/plugins/jquery-zclip-master/jquery.zclip.js",
			    "/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
			    "/assets/global/plugins/select2/js/select2.min.js",
			    "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
			    "/misc/js/page-level/api.js"]);
	}

	if ($_REQUEST['k'] == "streams" && !empty($_REQUEST['b'])) {
		require_once "part/api/streams.php";
	}
} else {
	echo "<div class='alert alert-danger'>У Вас нет доступа к API. Для получения доступа, отправьте запрос на странице <a href='/admin/profile'>профиля</a>.</div>";
}





?>