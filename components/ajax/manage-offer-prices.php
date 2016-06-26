<?php

$response = array();

$action = $_POST['action'];
$offer_id = $_POST["offer_id"];
$data = unserialize($_SESSION["offer"][$offer_id]);

if ( $action == "save-target" ) {
    $target = $_POST["target"];

    // Удаляем все данные по цели
    foreach ($data as $code=>$item) {
        unset($data[$code]["targets"][$target]);
    }

    if (!empty($_POST["targets"])) {
        foreach ($_POST["targets"] as $item) {
            $code = $item["code"];
            $data[$code]["targets"][$target] = array( "id" => $target,
                                                       "max_price" => (int) $item["max_price"],
                                                       "commission" => (int) $item["commission"],
                                                       "webmaster_commission" => (int) $item["webmaster_commission"]);
        }
    }


    $_SESSION["offer"][$offer_id] = serialize($data);
    $_SESSION["offer"]["available_to"] = array();
    foreach ($_POST['webmasters'] as $item) {
       $_SESSION["offer"]["available_to"][$item["id"]] = $item;
    }
    $response["rows"] = getTargetsTableRows($data);
}

if ($action == "remove-webmaster-from-target") {
   unset($_SESSION["offer"]["available_to"][$_POST["id"]]);
}

if ( $action == "edit-target" ) {
    $target = $_POST['target'];
    foreach ($data as $code=>$item) {
        $template_data[$code] = array(
            "country_name" => Country::getName($code),
            "currency" => Country::getCurrencyCode($code),
            "max_price" => $item["targets"][$target]["max_price"],
            "price" => $item["price"]["value"],
            "target" => $item["targets"][$target],
            "target_id" => $target
        );
    }
    $smarty->assign("data", $template_data);
    $smarty->assign('selected_webmasters', $_SESSION["offer"]["available_to"]);
    $smarty->assign('webmasters', User::get_by_role_name("webmaster"));
    $response["rows"] = $smarty->fetch('admin' . DS . 'offers' . DS . 'ajax' . DS . 'edit-target.tpl');
}

if ( $action == "remove-target" ) {
    $target_id = $_POST['target'];
    unset($data["targets"][$target_id]);
    $_SESSION["offer"][$offer_id] = serialize($data);
}

if ( $action == "edit-price" ) {
    $code = $_POST["code"];
    $response = array(
        "price" => $data[$code]["price"]["value"],
        "price_id" => $data[$code]["price"]["id"],
        "qty" => $data[$code]["qty"]);
}

if ( $action == "remove-price" ) {
    $code = $_POST['country_code'];
    unset($data[$code]);
    $_SESSION["offer"][$offer_id] = serialize($data);

    $response["rows"] = getTargetsTableRows($data);
    $response["list"] = getCountriesList($data);
}

if ( $action == "save-price" ) {
    $code = $_POST["country_code"];

    $data[$code]["price"]["value"] = $_POST["price"];
    $data[$code]["price"]["id"] = $_POST["price_id"];
    $data[$code]["qty"] = $_POST["qty"];
    $_SESSION["offer"][$offer_id] = serialize($data);

    $response["rows"] = getTargetsTableRows($data);
    $response["list"] = getCountriesList($data);
}

function getTargetsTableRows($data){
    $rows = "<table class='table table-bordered table-hover table-condensed offers-table'>
               <thead>
                <tr role='row' class='heading'>
                  <th width='22%'>Страна</th>
                  <th>Базовая цена</th>
                  <th width='28%'>Цель</th>
                  <th>Комиссия вебмастера</th>
                  <th>Комиссия UM</th>
                  </tr>
                </thead>
                <tbody>";
    $targets = Target::getAll();
    // Формируем таблицу цен и целей
    foreach ($data as $code=>$item) {
        $targetsCount = count($item["targets"]) + 1;
        $country_name = Country::getName($code);
        $currency = Country::getCurrency($code);

        $rows .= "<tr>
                   <td rowspan='{$targetsCount}'><span class='flag flag-{$code}'></span>&nbsp;{$country_name}</td>
                   <td rowspan='{$targetsCount}' class='text-right'>{$item['price']['value']}&nbsp;{$currency}</td>";
        if ($targetsCount == 1) {
            $rows .= "<td colspan='3'></td>";
        } else {
            $rows .= "</tr>";
        }

        if ($targetsCount > 1) {
            foreach ($item["targets"] as $target) {
                $targetName = $targets[$target["id"]]["name"];
                $rows .= "<tr>
                           <td>{$targetName}</td>
                           <td class='text-right'><span class='highlight'>{$target['webmaster_commission']}&nbsp;{$currency}</span></td>
                           <td class='text-right'><span class='highlight'>{$target['commission']}&nbsp;{$currency}</span></td>
                          </tr>";
            }
        }
    }
    return $rows . "</tbody></table>";
}

function getCountriesList($data){
    $rows = "";
    foreach (array_keys($data) as $code) {
        $country_name = Country::getName($code);
        $rows .= "<div class='clearfix margin-bottom-5'>
                <span class='flag flag-{$code}'></span> {$country_name}
                <a data-toggle='tooltip' data-code='{$code}' data-placement='left' title='Удалить цены по стране' class='btn btn-sm btn-circle btn-icon-only btn-default btn-remove pull-right' href='javascript:;'>
                    <i class='icon-trash'></i>
                </a>
                <a data-toggle='tooltip' data-code='{$code}' data-placement='left' title='Редактировать цену' class='btn btn-sm btn-circle btn-icon-only btn-default btn-edit pull-right' href='javascript:;'>
                    <i class='icon-pencil'></i>
                </a>
            </div>";
    }
    return $rows;
}

echo json_encode( $response );

?>