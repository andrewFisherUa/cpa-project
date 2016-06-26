<?php

$isAdmin = User::isAdmin();
$uid = User::get_current_user_id();
$filter = new Filter;

$error = false;

if ($_REQUEST['b'] == "new") {
	if ($isAdmin) {
		$error = true;
	} else {
		$stream = new Api_Stream($GLOBALS['DB'], ["user_id" => $uid]);

		$sql = "SELECT DISTINCT g.name, g.id
		        FROM goods AS g INNER JOIN users2goods AS ug ON ug.g_id = g.id
		        WHERE g.available_in_offers = 1 AND g.offer_status = '" . Offer::STATUS_ACTIVE . "' AND ug.u_id = ?";

		$stmt = $GLOBALS['DB']->prepare($sql);
		$stmt->execute([$uid]);
		$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
} else {
	$stream_id = $filter->sanitize($_REQUEST['b'], "int!");
	$stream = Api_Stream::getById($GLOBALS["DB"], $stream_id);
	
	// Если поток не существует либо поток принадлежит другому пользователю - запретить редактирование
	if ($stream === false || (!$isAdmin && $uid != $stream->getUserId())) {
		$error = true;
	} else {

		$query = "SELECT t1.country_code, t1.price, t2.comission_webmaster as profit, t2.max_price, t2.t_id as target_id, t3.name AS target_name, t4.name AS country_name, t4.currency_code as currency
				  FROM goods2countries AS t1, goods2targets AS t2, targets AS t3, country as t4
				  WHERE t1.country_code = t2.country_code AND 
					     t1.g_id = t2.g_id AND 
				         t2.t_id = t3.target_id AND
				         t1.country_code = t4.code AND
				         t1.g_id = ?";

		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([
			$stream->getOfferId()
		]);

		$prices = [];

		while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if (!array_key_exists($a['country_code'], $prices)) {
				$prices[$a['country_code']] = [
					"price" => (int) $a['price'],
					"recommended" => (int) $a['price'],
					"currency" => $a['currency'],
					"country_name" => $a['country_name'],
					"profit" => $a['profit'],
					"targets" => [],
				];
			}

			$prices[$a['country_code']]['targets'][$a['target_id']] = [
				"id" => $a['target_id'],
				"name" => $a['target_name'],
				"profit" => $a['profit'],
				"max" => $a['max_price'] - $a['price'] + $a['profit'],
				"selected" => false,
			];
		}

		foreach ($stream->getPrices() as $country_code => $values) {
			$prices[$country_code]["price"] = $values["price"];
			$prices[$country_code]["recommended"] = $values["recommended"];
			$prices[$country_code]["profit"] = $values["webmaster_profit"];
		}

		$query = "SELECT * FROM user_target WHERE offer_id = :offer_id AND user_id = :user_id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":offer_id", $stream->getOfferId(), PDO::PARAM_INT);
		$stmt->bindParam(":user_id", $stream->getUserId(), PDO::PARAM_INT);
		$stmt->execute();

		$can_edit_prices = $stmt->rowCount() > 0;
		$smarty->assign("editable", $can_edit_prices);
		
		$smarty->assign('prices', $prices);
		$prices_html = $smarty->fetch('admin/api/ajax/prices.tpl'); 
	}
}

if ($error) {
	echo "<div class='alert alert-danger'>Отказано в доступе</div>";
} else {
	require_once $_SERVER['DOCUMENT_ROOT'] . "/templates/admin/api/edit_stream.php";

	enqueue_scripts([
		"/assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js",
	    "/assets/global/plugins/select2/js/select2.min.js",
	    "/misc/plugins/jquery-zclip-master/jquery.zclip.js",
	    "/misc/js/page-level/api.js"]);
}

?>