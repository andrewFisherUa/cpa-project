<?php

    $output = '';
    $products = Goods::getShortList($_POST['catID']);

    foreach ($products as $item) {
        $output .= "<option value='{$item['id']}'>{$item['name']}</option>";
    }
    echo $output;

?>