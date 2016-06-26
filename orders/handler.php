<?php

require_once '../objects/Filter.php';
require_once '../config.php';

$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

if (!empty($_POST['name_first']) &&
    !empty($_POST['name_last']) &&
    !empty($_POST['phone']) &&
    !empty($_POST['key']) &&
    !empty($_POST['country'])) {

    $filter = new Filter;

    $first_name = $filter->sanitize($_POST['name_first'], ["string", "striptags"]);
    $last_name = $filter->sanitize($_POST['name_last'], ["string", "striptags"]);
    $country_code = $filter->sanitize($_POST['country'], ["string", "striptags"]);
    $phone = $filter->sanitize($_POST['phone'], ["string", "striptags"]);
    $key = $filter->sanitize($_POST['key'], ["string", "striptags"]);;
    $email = isset($_POST['email']) ? $filter->sanitize($_POST['email'], ["string", "striptags"]) : "";
    $ip = $_SERVER['REMOTE_ADDR'];
    $created = time();

    $stmt = $db->prepare("INSERT INTO pre_orders(`key`, country_code, first_name, last_name, phone, email, ip, created)
                          VALUES (:key, :country_code, :first_name, :last_name, :phone, :email, :ip, :created)");
    $stmt->bindParam(":key", $key, PDO::PARAM_STR);
    $stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
    $stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
    $stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
    $stmt->bindParam(":country_code", $country_code, PDO::PARAM_STR);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->bindParam(":ip", $ip, PDO::PARAM_STR);
    $stmt->bindParam(":created", $created, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $order_id = $db->lastInsertId();
        $stmt = $db->prepare("SELECT link FROM flows WHERE `key` = :key");
        $stmt->bindParam(":key", $key, PDO::PARAM_STR);
        $stmt->execute();
        $link = $stmt->fetchColumn();
        unset($_POST);
        header("Location: " . STREAMS_URL . "/{$link}/complete.html?order=" . $order_id);
    } else {
        die("Не удалось сохранить заказ");
    }
} else {
    die("Недостаточно данных");
}

?>