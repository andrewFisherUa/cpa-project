<?php
    require_once( PATH_ROOT .'/misc/plugins/php/mail/PHPMailer/PHPMailerAutoload.php');
    require_once( PATH_ROOT .'/misc/plugins/php/mail/u_mail.php');

    $filter = new Filter;

    $data = [];

    $data['skype'] = $filter->sanitize($_POST['fskype'], ["striptags", "string"]);
    $data['name'] = $filter->sanitize($_POST['fname'], ["striptags", "string"]);
    $data['email'] = $filter->sanitize($_POST['femail'], ["striptags", "string"]);
    $data['password'] = $filter->sanitize($_POST['fpass'], ["striptags", "string"]);
    $data['role'] = $filter->sanitize($_POST['frole'], ["striptags", "string"]);
    $data['sub'] = $filter->sanitize($_POST['fref'], "int!");
    $data['phone'] = $filter->sanitize($_POST['fphone'], ["striptags", "string"]);
    $activation = md5($data['email']) . time();

    $ip = $_SERVER['REMOTE_ADDR'];
    $data['country_code'] = strtolower(geoip_country_code_by_name($ip));
    $data['country_name'] = geoip_country_name_by_name($ip);

    // Определяем страну пользователя для установки валюты по умолчанию
    /*
    $country_code = strtolower(geoip_country_code_by_name($_SERVER['REMOTE_ADDR']));
    $stmt = $GLOBALS['DB']->prepare("SELECT code FROM country WHERE code = ?");
    $stmt->execute([$country_code]);
    if ($stmt->rowCount() == 0) {
        $country_code = "ru";
    } */

    $country_code = "ru";

    $errors = [];

    // Проверка существования рефера
    if ($data['sub'] != 0) {
        $query = "SELECT user_id FROM users WHERE user_id = ?";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute([
            $data['sub']
        ]);

        if ($stmt->rowCount() == 0) {
            $data['sub'] = 0;
        }
    }

    // Check if email exists
    $stmt = $GLOBALS['DB']->prepare("SELECT user_id FROM users WHERE email = :email");
    $stmt->bindParam(":email", $data["email"], PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $errors[] = ["name" => "femail", "text" => "Email занят"];
    }

    // Check if skype exists
    if ($data['role'] == "advertiser") {
        $stmt = $GLOBALS['DB']->prepare("SELECT skype FROM partners WHERE skype = :skype");
        $stmt->bindParam(":skype", $data["skype"], PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount()) {
            $errors[] = ["name" => "fskype", "text" => "Skype занят"];
        }
    }

    if (empty($errors)) {
        $temp = explode("@", $data['email']);
        $data['login'] = $temp[0];

        $partner = new Partner($data);
        $partner->activation = $activation;
        $partner->save();
        $partner->set_role($partner->id, Role::get_role_id($data['role']));
        // Добавляем валюту по умолчанию
        Balance::createAccount($partner->id, $country_code, true);
        Notify::set_user_options($partner->id);
        $mail = new u_mail(true);
        $msg = '<h1>Регистрация на сайте <a href="http://'.$_SERVER['HTTP_HOST'].'">http://'.$_SERVER['HTTP_HOST'].'</a></h1>
                <p>Ваш логин: <strong>'.$data['email'].'</strong></p>
                <p>Для активации аккаунта перейдите по этой ссылке: <a href="http://'.$_SERVER['HTTP_HOST'].'/email_activation/'.$activation.'">http://'.$_SERVER['HTTP_HOST'].'/email_activation/'.$activation.'</a></p>';

        $mail->sendmail($_SERVER['HTTP_HOST'], 'robot@'.$_SERVER['HTTP_HOST'], $data['email'], "Регистрация на сайте", $msg);

        unset($data['password']);

        $data['country'] = geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);

        Audit::addRecord([
            "group" => "registration",
            "action" => "Регистрация",
            "details" => $data
        ]);
    }

echo json_encode(["errors" => $errors]);
?>