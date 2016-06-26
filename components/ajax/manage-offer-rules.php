<?php

$filter = new Filter;

$response = [];
$action = $filter->sanitize($_POST['action'], ["string", "striptags"]);

if ($action == "get-rules") {
	$id = $filter->sanitize($_POST['id'], "int");
	$response['text'] = Offer::getRules($id);

	if ($id != 0) {
		$response['special'] = Offer::getRules(0) != $response['text'];
	} else {
		$response['special'] = false;
	}

	$o = new GoodsOptions($id);
	$response["show_rules"] = $o->get("show_rules");
}

if ($action == "recovery-text") {
	$id = $filter->sanitize($_POST['id'], "int");
	$response['text'] = Offer::getRules();
}

if ($action == "recovery") {
	$id = $filter->sanitize($_POST['id'], "int");

	$stmt = $GLOBALS['DB']->prepare("DELETE FROM offer_connection_rules WHERE offer_id = ?");
	$response['success'] = $stmt->execute([
		$id
	]);

	Audit::addRecord([
		"group" => "offer",
		"subgroup" => "recovery_rules",
		"Восстановление стандартных условий подключения оффера для ID `{$id}`"
	]);
}

if ($action == "save") {
    $id = $filter->sanitize($_POST['id'], "int");
    $text = $filter->sanitize($_POST['text'], ["string", "striptags"]);

    $stmt = $GLOBALS['DB']->prepare("DELETE FROM offer_connection_rules WHERE offer_id = ?");
    $stmt->execute([$id]);

    $stmt = $GLOBALS['DB']->prepare("INSERT INTO offer_connection_rules (offer_id, `text`, created) VALUES (:id, :text, " . time() . ")");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->bindParam(":text", $text, PDO::PARAM_INT);
    $response['success'] = $stmt->execute();

    Audit::addRecord([
		"group" => "offer",
		"subgroup" => "save_rules",
		"action" => "Сохранение условий подключения оффера для ID `{$id}`"
	]);
}

if ($action == "save" || $action == "recovery") {
	$id = $filter->sanitize($_POST['id'], "int");
	$show_rules = $filter->sanitize($_POST['show_rules'], "int");

	$o = new GoodsOptions($id);
	$o->set("show_rules", $show_rules);
	$o->save();

	$a = $show_rules ? "Включение показа условий подключения оффера `{$id}`" : "Отключение показа условий подключения оффера `{$id}`";
	Audit::addRecord([
		"group" => "offer",
		"subgroup" => "show_rules",
		"action" => $a
	]);
}

if ($action == "reset") {

	$text = $filter->sanitize($_POST['text'], ["string", "striptags"]);
    $stmt = $GLOBALS['DB']->exec("DELETE FROM offer_connection_rules");
    $stmt = $GLOBALS['DB']->prepare("INSERT INTO offer_connection_rules (offer_id, `text`, created) VALUES (0, :text, " . time() . ")");
    $stmt->bindParam(":text", $text, PDO::PARAM_INT);
    $response['success'] = $stmt->execute();

    Audit::addRecord([
    	"group" => "offer",
		"subgroup" => "reset_rules",
		"action" => "Восстановление условий подключения для всех офферов"
    ]);
}


echo json_encode($response);

?>