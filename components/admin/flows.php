<?php

$filter = new Filter;

if (isset($_POST["update_all"])) {
	foreach(Flow::getAll() as $item){
		$landing = new Landing_Generator($item, $item->getLandingAlias());
		$url = $landing->generate();
		if ($item->getBlogId()) {
			$blog = new Blog_Generator($item, $url, $item->getBlogAlias());
			$blog->generate();
		}
	}
	unset($_POST);
}

if ($_REQUEST['k'] == "") {
	if (User::isAdmin()){
		// Офферы
		$stmt1 = $GLOBALS['DB']->query("SELECT id, name FROM goods WHERE available_in_offers = 1 ORDER BY name");
		$stmt2 = $GLOBALS['DB']->query("SELECT f_id AS id, name FROM flows ORDER BY name");
		$stmt3 = $GLOBALS['DB']->query("SELECT user_id AS id, login FROM users ORDER BY login");
		$filters = array("offers" => $stmt1->fetchAll(PDO::FETCH_ASSOC),
			             "streams" => $stmt2->fetchAll(PDO::FETCH_ASSOC),
			             "users" => $stmt3->fetchAll(PDO::FETCH_ASSOC));
		$smarty->assign('filters', $filters);
		$smarty->display('admin' . DS . 'flows' . DS . 'admin.tpl' );
	} else {
		$user_id = User::get_current_user_id();
		$smarty->assign('offers', Offer::getByUID($user_id));
		$smarty->assign('flows', Flow::getByUID($user_id));
		$smarty->assign('user_id', $user_id );
		$smarty->display('admin' . DS . 'flows' . DS . 'index.tpl' );
	}
} else if ($_REQUEST['k'] != 'postback'){
	$user_id = User::get_current_user_id();

	if ($_REQUEST['k'] == 'new') {
		// Создание потока
		$f = new Flow(array("user_id" => $user_id));
		$sql = "SELECT DISTINCT g.name, g.id
		        FROM goods AS g INNER JOIN offer_content AS o ON o.offer_id = g.id
		                        INNER JOIN users2goods AS ug ON ug.g_id = g.id
		        WHERE g.available_in_offers = 1 AND offer_status = '".Offer::STATUS_ACTIVE."' AND ug.u_id = ?";

		$stmt = $GLOBALS['DB']->prepare($sql);
		$stmt->execute([$user_id]);
		$content['offers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$subaccounts = Flow::getUserSubaccounts($user_id);

		$smarty->assign('spaces', Space::getAll([
			"user_id" => $user_id,
			"status" => Space::STATUS_APPROVED
		]));

		$smarty->assign('content', $content);
	    $smarty->assign('subaccounts', $subaccounts);
		$smarty->assign('flow',  $f);
		$smarty->assign('postback', Postback::getInstance($user_id));
		$form = $smarty->fetch('admin' . DS . 'flows' . DS . 'ajax' . DS . 'edit-flow-form.tpl');
		$smarty->assign('form', $form);
		$smarty->display('admin' . DS . 'flows' . DS . 'single.tpl' );
	} else {
		// Редактирование
		$id = $filter->sanitize($_REQUEST['k'], "int!");
		$f = Flow::getInstance($id);
		if (!is_null($f)) {
			if (User::isAdmin() || $f->getUserId() == $user_id) {
				$f->getFullLink();
				$content['landings'] = Offer::getContent($f->getOfferId(), "landing");

				if ($f->getLandingId()) {
					$content['blogs'] = Blog::get_by_landing($f->getLandingId());
				} else {
					$content['blogs'] = Blog::get_by_landing($content['landings'][0]['c_id']);
				}

				foreach ($content as &$a){
					foreach ($a as &$b) {
						$b['preview'] = Content::get_preview_link($b['c_id'], $f->getOfferId());
					}
				}

				$smarty->assign('prices', $f->getPrices()->getTable());
				$subaccounts = Flow::getUserSubaccounts($user_id);

				$smarty->assign('spaces', Space::getAll([
					"user_id" => User::isAdmin() ? $f->getUserId() : $user_id,
					"status" => Space::STATUS_APPROVED
				]));

				$smarty->assign('blogs_url', Content::BLOGS_URL);
				$smarty->assign('landings_url', Content::LANDINGS_URL);
				$smarty->assign('content', $content);
			    $smarty->assign('subaccounts', $subaccounts);
				$smarty->assign('flow',  $f);
				$smarty->assign('postback', $f->getPostback());
				$form = $smarty->fetch('admin' . DS . 'flows' . DS . 'ajax' . DS . 'edit-flow-form.tpl');
				$smarty->assign('form', $form);
				$smarty->display('admin' . DS . 'flows' . DS . 'single.tpl' );
			} else {
				echo "<div class='alert alert-danger'>Отказано в доступе</div>";
			}
		} else {
			echo "<div class='alert alert-danger'>Поток не найден</div>";
		}
	}
} else {

	// postback
	$user_id = User::get_current_user_id();
	$postback = Postback::getInstance($user_id);

	require_once(PATH_ROOT . DS . 'templates' . DS . 'admin' . DS . 'flows' . DS . 'postback.php');
}




enqueue_scripts( array(
	"/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/uniform/jquery.uniform.min.js",
    "/assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js",
    "/assets/global/plugins/select2/js/select2.min.js",
    "/misc/plugins/jquery-zclip-master/jquery.zclip.js",
    "/misc/js/page-level/flows.js"));

?>