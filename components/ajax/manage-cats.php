<?php

$filter = new Filter;

$action = $filter->sanitize($_POST["action"], ["string", "striptags"]);

$response = array( "errors" => array() );

// Удалить категорию
if ( $action == "remove" ) {
	$id = $filter->sanitize($_POST['id'], "int");
	Categories::delete($id);

	Audit::addRecord([
		"group" => "category",
		"subgroup" => "delete",
		"action" => "Удаление категории `{$id}`"
	]);
}

// Показать категорию
if ( $action == "show" ) {
	$id = $filter->sanitize($_POST['id'], "int");
	$stmt = $GLOBALS['DB']->prepare("UPDATE categories SET hidden = 0 WHERE id = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->execute();

	Audit::addRecord([
		"group" => "category",
		"subgroup" => "delete",
		"action" => "Включить отображение категории `{$id}`"
	]);
}

// Скрыть категорию
if ( $action == "hide" ) {
	$id = $filter->sanitize($_POST['id'], "int");
	$stmt = $GLOBALS['DB']->prepare("UPDATE categories SET hidden = 1 WHERE id = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->execute();

	Audit::addRecord([
		"group" => "category",
		"subgroup" => "delete",
		"action" => "Выключить отображение категории `{$id}`"
	]);
}

if ( $action == "check-form" ) {
	$name = $filter->sanitize($_POST["name"], ["string", "striptags"]);
	$link = $filter->sanitize($_POST["link"], ["string", "striptags"]);
	$id = $filter->sanitize($_POST['id'], "int");

	$cat = Categories::getInstance($id);
	$cat->setName($name);
	$cat->setAlias($link);

	if ($cat->nameIsAvailable() == false) {
		$response['errors'][] = "Имя `{$name}` занято.";
	}

	if ($cat->aliasIsAvailable() == false) {
		$response['errors'][] = "Ссылка `{$alias}` уже существует.";
	}
}

echo json_encode( $response );

?>