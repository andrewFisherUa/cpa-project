<?php

$response = array();
$action = $_POST['action'];

// Возвратить форму параметров со значениями по умолчанию
if ( $action == "get-defaults" ) {
	$options = new Options( "offer_option" );

    $stmt = $GLOBALS["DB"]->query( "SELECT * FROM country");
    $countries = $stmt->fetchAll( PDO::FETCH_ASSOC );

    $data = array();

    foreach ( $countries as $country ) {
        $country_code =  $country["code"];
        $data[$country_code]["code"] = $country["code"];
        $data[$country_code]["name"] =  $country["name"];
        $data[$country_code]["phone"] = $options->get_option( "phone_{$country_code}" );
        $data[$country_code]["address"] = $options->get_option( "address_{$country_code}" );
        $data[$country_code]["delivery_time"] = $options->get_option( "delivery_time_{$country_code}" );
    }

    $smarty->assign( "options", $data );
    $response["rows"] = $smarty->fetch( 'admin' . DS . 'content' . DS . 'ajax' . DS . 'options-form.tpl' );
}

echo json_encode( $response );

?>