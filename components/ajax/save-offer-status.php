<?php

Offer::updStatus($_POST['id'], $_POST['value']);


$record = new Audit([
	"group" => "offer",
	"subgroup" => "change_status",
	"action" => "Смена статуса оффера `" . $_POST['id'] . "`",
]);

$record->addDetails([
	"offer_id" => $_POST['id'],
	"status" => $_POST['value']
]);

$record->save();


switch ($_POST['value']) {
	case 'moderation' : echo "info"; break;
	case 'active' : echo "success"; break;
	case 'disabled' : echo "danger"; break;
	case 'archive' : echo "warning"; break;
}

?>