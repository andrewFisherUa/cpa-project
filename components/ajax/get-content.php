<?php

$action = $_POST["action"];
$respond = array();

if ( $action == "get-blogs" ) {
	$base_url = Content::BLOGS_URL;
	$respond["rows"] = "<option value='0'>Выберите блог</option>";
} else {
	$base_url = Content::LANDINGS_URL;
	$respond["rows"] = "<option value='0'>Выберите лендинг</option>";
}

// Получить блоги подключенные к лендингу
if ( $action == "get-blogs" ) {
	$rows = Blog::get_by_landing( $_POST["landing_id"] );
}

// Получить лендинги подключенные к офферу
if ( $action == "get-landings" ) {
	$rows = Offer::getContent($_POST["offer_id"], "landing" );
	$flow = new Flow(array("offer_id"=>$_POST["offer_id"]));
	$respond['prices'] = $flow->getPrices()->getTable();
}

if ( $rows ) {
	foreach ( $rows as $row ) {
		$link = "/flows/preview/" . $row['c_id'];
		$respond["rows"] .= "<option value='" . $row['c_id'] . "' data-link='" . $link . "'>" . $row["name"] . "</option>";
	}
}

echo json_encode( $respond );