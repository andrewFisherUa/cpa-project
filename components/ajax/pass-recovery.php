<?php
    require_once( PATH_ROOT . DS . 'misc'. DS . 'php' . DS . 'functions.php' );
    require_once( PATH_ROOT .'/misc/plugins/php/mail/PHPMailer/PHPMailerAutoload.php');
    require_once( PATH_ROOT .'/misc/plugins/php/mail/u_mail.php');

    $error = '';
    $success = '';

    // Gather the post data
    $filter = new Filter;
    $email = $filter->sanitize($_POST["email"], ["string", "striptags"]);
    $password = $filter->sanitize($_POST["password"], ["string", "striptags"]);
    $confirmpassword = $filter->sanitize($_POST["confirmpassword"], ["string", "striptags"]);
    $hash = $filter->sanitize($_POST["key"], ["string", "striptags"]);

    $salt = "498#2D83B631%3800EBD!801600D*7E3CC13";

    // Generate the reset key
    $token = hash('sha512', $salt.$email);

    if ( $token != $hash ) {
        $error = "Неверный ключ для сброса пароля";
    } else {
        $query = "SELECT expired FROM reset_tokens WHERE token = '{$token}'";
        $stmt = $GLOBALS['DB']->query($query);
        $expired = $stmt->fetchColumn();

        if ( $token != $hash && ( !$expired || $expired > time() ) ) {
            $error = "Неверный ключ для сброса пароля.";

        } else if ( $expired < time() ) {
            $error = "Срок действия ключа для сброса пароля истек.";
        }
    }

    if ($error) {
        Audit::addRecord([
            "group" => "pass_recovery",
            "subgroup" => "invalid_key",
            "action" => "Восстановление пароля. {$error}",
            "details" => [
                "email" => $email
            ]
        ]);
    } else {
        if ($password == $confirmpassword) {
            //has and secure the password
            $password = crypt( $password, blowfishSalt() );

            // Update the user's password
            $GLOBALS['DB']->exec("UPDATE users SET password = '{$password}' WHERE email = '{$email}'");
            $GLOBALS['DB']->exec("UPDATE reset_tokens SET expired = " . time() . " WHERE token = '{$token}'");
            $success = "Ваш пароль был успешно изменен.";

            Audit::addRecord([
                "group" => "pass_recovery",
                "subgroup" => "success",
                "action" => "Восстановление пароля. Успешно.",
                "details" => [
                    "email" => $email
                ]
            ]);
        } else {
            $error = "Пароли не совпадают";
        }
    }

$result = compact('error', 'success');
echo json_encode($result);

?>
