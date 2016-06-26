<?php

$action = $_POST["action"];
$response = array();

// Зайти в кабинет пользователя
if ( $action == "save-defaults" ) {
	$hold = new Hold();
	$hold->saveValue($_POST);
	$response["table"] = $hold->getTable();
}

if ( $action == "save-to-all" ) {
	$query = "DELETE FROM webmaster_hold WHERE target_id = :target_id AND country_code = :country_code";
	$stmt = $GLOBALS['DB']->prepare($query);
	$stmt->bindParam(":target_id", $_POST['target_id'], PDO::PARAM_INT);
	$stmt->bindParam(":country_code", $_POST['country_code'], PDO::PARAM_STR);
	$stmt->execute();

	$hold = new Hold();
	$hold->saveValue($_POST);
	$response["table"] = $hold->getTable();
}

if ($action == "get-webmaster-hold-values") {
	$hold = new Hold($_POST["user_id"]);
	$response["table"] = $hold->getTable();
}

if ($action == "save-webmaster-hold"){
	$hold = new Hold($_POST["user_id"]);
	$hold->saveValue($_POST);
	$response["table"] = $hold->getTable();
}

echo json_encode( $response );

?>