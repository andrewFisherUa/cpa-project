<?php

$action = $_POST["action"];
$response = array();

// Получить форму редактирования комментария
if ($action == "get-form") {

	$stmt = $GLOBALS['DB']->query("SELECT shop_id as id, domen FROM shops ORDER BY domen");
	$shops = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$stmt = $GLOBALS['DB']->query("SELECT id, name FROM goods WHERE visible_in_shop = 1 ORDER BY id DESC");
	$goods = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$comment = Comment::getInstance($_POST['id']);
	if (!$comment->isViewed()) {
		Comment::setViewed($comment->getId());
	}
	$smarty->assign('shops', $shops);
	$smarty->assign('goods', $goods);
	$smarty->assign('countries', Country::getAll());
	$smarty->assign('statusList', Comment::getStatusList());
	$smarty->assign('comment', $comment);
	$response['form'] = $smarty->fetch('admin' . DS . 'shop' . DS . 'comments' . DS . 'form.tpl');
}

// Сохранение комментария
if ($action == "save-comment") {
	$id = (int) $_POST['id'];

	if ($id == 0) {
		// Добавление комментария
		$post = $_POST;
		$items = array();

		foreach ($_POST['country_code'] as $code) {

			if ($post['shop_id'] == -1) {
				$stmt = $GLOBALS['DB']->query("SELECT shop_id as id FROM shops ORDER BY domen");
				while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$comment = new Comment($post);
					$comment->setShopId($data['id']);
					$comment->setCountryCode($code);
					$comment->save();
				}
			} else {
				$comment = new Comment($post);
				$comment->setCountryCode($code);
				$comment->save();
			}
		}

	} else {
		// Редактирование
		$comment = new Comment($_POST);
		$comment->save();
	}
}

// Удаление комментария
if ($action == "delete-comment") {
	Comment::delete($_POST['id']);
}

// Изменение статуса
if ($action == "change-status") {
	Comment::updStatus($_POST['id'], $_POST['value']);
}

if ($action == "add-comment") {
	$comment = new Comment($_POST);
	$comment->save();
}

if ($action == "paginate") {
	$length = $_POST['length'];
	$page = $_POST['page'];
	$country_code = $_POST['country_code'];
	$shop_id = $_POST['shop_id'];
	$product = StoreProduct::getInstance($shop_id, $country_code, $_POST['product']);
	$response["rows"] = $product->getCommentsHTML($page, $length);
}

echo json_encode( $response );

?>