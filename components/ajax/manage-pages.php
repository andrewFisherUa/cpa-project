<?php

$action = $_POST["action"];
$response = array();

if ( $action == "check-link" ) {
	$response["linkIsAvailable"] = ShopPage::checkLink($_POST["link"], $_POST["id"]);
}


echo json_encode( $response );

?>