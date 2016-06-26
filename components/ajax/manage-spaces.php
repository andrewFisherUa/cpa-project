<?php

$filter = new Filter;

$action = $filter->sanitize($_POST['action'], ["string", "striptags"]);
$response = [];

if ($action == "remove") {
	$id = $filter->sanitize($_POST["id"], "int!");

	$stmt = $GLOBALS['DB']->prepare("SELECT name FROM flows WHERE space = ?");
	$stmt->execute([$id]);
	if ($stmt->rowCount() > 0){
		$response['error'] = "Ошибка при удалении источника трафика. Источник связан с потоками: " . implode(", ", $stmt->fetchAll(PDO::FETCH_COLUMN));
	} else {
		$response['error'] = "";
		Space::remove($id);

		Audit::addRecord([
			"group" => "space",
			"subgroup" => "delete",
			"action" => "Удаление источника трафика `{$id}`",
		]);
	}
}

if ($action == "get-view") {
	$id = $filter->sanitize($_POST["id"], "int!");

	$s = Space::getInstance($id);
	$comment = $s->getComment() == "" ? " - " : $s->getComment();

	$r = "<table class='table table-striped'><tbody>";
	$r .= "<tr><th width='20%'>Название:</th><td>" . $s->getName() . "</td></tr>";
	$r .= "<tr><th width='20%'>Тип:</th><td>" . $s->getTypeAlias() . "</td></tr>";
	if ($s->getSourceName() != "") {
		$r .= "<tr><th width='20%'>Источник трафика:</th><td>" . $s->getSourceName() . "</td></tr>";
	}
	if ($s->getUrl() != "") {
		$r .= "<tr><th width='20%'>Url:</th><td><a href='".$s->getUrl()."' target='_blank'>" . $s->getUrl() . "</a></td></tr>";
	}
	$r .= "<tr><th width='20%'>Комментарий:</th><td>" . $comment . "</td></tr>";
	$r .= "<tr><th width='20%'>Описание:</th><td>" . $s->getDescription() . "</td></tr>";
	$r .= "<tr><th width='20%'>Пользователь:</th><td>" . $s->getUserLogin() . "</td></tr>";
	$r .= "<tr><th width='20%'>Статус:</th><td><span class='label label-" . $s->getStatusClassName() . "'>" . $s->getStatusLabel() . "</span></td></tr>";
	$r .= "<tr><th width='20%'>Дата&nbsp;создания:</th><td>" . date("d/m/Y", $s->getCreated()) . "</td></tr>";
	$r .= "<tr><th width='20%'>Дата&nbsp;редактирования:</th><td>" . date("d/m/Y", $s->getChanged()) . "</td></tr>";
	$r .= "<tr><td colspan='2'>
			<div class='form-group'>
			<label class='control-label'>Комментарий админа:</label>
			<div><textarea class='form-control' rows='3' id='note'>" . $s->getNote() . "</textarea></div>
			</div>
			<button class='btn blue' id='save-note' data-id='{$s->getId()}'>Сохранить</button>
		   </td></tr>";
	$response["rows"] = $r . "</tbody></table>";
	$response["name"] = $s->getName();
}

if ($action == "save-note"){
	$id = $filter->sanitize($_POST["id"], "int!");
	$comment = $filter->sanitize($_POST["text"], ["string", "striptags"]);

	$response['success'] = Space::addNote($id, $comment);

	Audit::addRecord([
		"group" => "space",
		"subgroup" => "add_comment",
		"action" => "Добавление комментария к источнику трафика `{$id}`: {$comment}"
	]);
}

if ($action == "confirm"){
	$id = $filter->sanitize($_POST["id"], "int!");

	if ( Space::confirmUrl($id) ) {
		$response["success"] = true;

		Audit::addRecord([
			"group" => "space",
			"subgroup" => "confirm",
			"action" => "Подтверждение источника трафика `{$id}`"
		]);

	} else {
		$response["success"] = false;
	}
}

echo json_encode($response);

?>