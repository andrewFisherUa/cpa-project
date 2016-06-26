<?php

$filter = new Filter;

$options = new Options("offer_option");

if (isset($_POST["save"])) {
    
    $audit_details = [];

    foreach ( $_POST['option'] AS $option_name=>$option_value ) {
        $options->set_option(
            $filter->sanitize($option_name, ["string", "striptags"]),
            $filter->sanitize($option_value, ["string", "striptags"]) 
        );

        $audit_details[$option_name] = $option_value;
    }

    Audit::addRecord([
        "group" => "content",
        "subgroup" => "save_options",
        "action" => "Изменение настроек контента",
        "details" => $audit_details
    ]);

    $options->save();
    $smarty->assign("message", ["text" => "Настройки контента сохранены", "class_name" => "success"]);
    unset($_POST);
}

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
$options_list = $smarty->fetch( 'admin' . DS . 'content' . DS . 'ajax' . DS . 'options-form.tpl' );
$smarty->assign( 'options_list', $options_list );
$smarty->display( 'admin' . DS . 'content' . DS . 'content-options.tpl' );

?>