<?php

$filter = new Filter;

$response = [];

$action = $filter->sanitize($_POST['action'], ["striptags", "string"]);

if ($action == "save") {

	$post = [
		"id" => $filter->sanitize($_POST["id"], "int"),
		"title" => str_replace(["<script>", "</script>"], "", $_POST["title"]),
		"content" => str_replace(["<script>", "</script>"], "", $_POST["content"]),
		"type" => $filter->sanitize($_POST["type"], "int!"),
		"status" => $filter->sanitize($_POST["status"], "int"),
		"good_id" => $filter->sanitize($_POST["good_id"], "int!"),
		"activate_time" => $filter->sanitize($_POST["activate_time"], "int!"),
	];	

	$id = News::save($GLOBALS['DB'], $post);

	Audit::addRecord([
		"group" => "news",
		"subgroup" => "edit",
		"action" => "Создание / редактирование новости `{$id}` : `" . $post["title"] . "`"
	]);
}

if ($action == "delete") {
	$id = $filter->sanitize($_POST['id'], "int");
	News::delete($GLOBALS['DB'], $id);

	Audit::addRecord([
		"group" => "news",
		"subgroup" => "delete",
		"action" => "Удаление новости `{$id}`"
	]);
}

if ($action == "get-form") {
	$id = $filter->sanitize($_POST['id'], "int");
	$c = News::getById($GLOBALS['DB'], $id);

	$types = array(
	  'Новый оффер',
	  'Приостановка оффера',
	  'Изменение оффера',
	  'Новые лендинги',
	  'Новости системы',
	  'Важное'
	);

	$status = array(
	  'Модерация',
	  'Активно',
	  'Архив'
	);

	$stmt = $GLOBALS['DB']->query("SELECT id,name FROM goods WHERE offer_status = 'active'");
    $goods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($c) {
    	$activate_time = ($c->getActivateTime() > 0) ? $c->getActivateTime() : time();
    } else {
    	$activate_time = time();
    }

    $smarty->assign('activate_time', date('d F Y - H:i', $activate_time));
	$smarty->assign('news', $c);
	$smarty->assign('goods', $goods);
	$smarty->assign('type', $types);
	$smarty->assign('status', $status);
	$response["form"] = $smarty->fetch('admin' . DS . 'news' . DS . 'ajax' . DS . 'news_form.tpl');
}

echo json_encode($response);


?>