<?php
    require_once( PATH_ROOT . '/misc/plugins/php/mail/PHPMailer/PHPMailerAutoload.php');
    require_once( PATH_ROOT . '/misc/plugins/php/mail/u_mail.php');

    $filter = new Filter;

    $email = $filter->sanitize($_POST["email"], ["string", "striptags"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "email is not valid";

        $record = new Audit([
            "group" => "pass_recovery_request",
            "subgroup" => "fail",
            "action" => "Неверный email",
        ]);

        $record->addDetails([
            "email" => $email,
        ]);

        $record->save();

        exit;
    }

    $stmt = $GLOBALS["DB"]->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->execute([
        $email
    ]);

    $userExists = $stmt->fetch(PDO::FETCH_ASSOC);

    if ( $userExists["email"] ) {
        $salt = "498#2D83B631%3800EBD!801600D*7E3CC13";
        $token = hash('sha512', $salt.$userExists["email"]);
        $expired = time() + 86400;

        $stmt = $GLOBALS["DB"]->prepare("DELETE FROM reset_tokens WHERE token = ?");
        $stmt->execute([
            $token
        ]);

        $stmt = $GLOBALS["DB"]->prepare("INSERT INTO reset_tokens (token, expired) VALUES (?, ?)");
        $stmt->execute([
            $token,
            $expired
        ]);

        $site_url = get_site_url();

        $pwrurl = $site_url . "/recovery/" . $token;

        $msg = "Уважаемый пользователь, <br /><br />
                Вы запросили восстановление пароля на сайте <a href='{$site_url}'>{$site_url}</a> <br />
                Чтобы сброить пароль, пожалуйста перейдите по ссылке {$pwrurl} <br />
                Ссылка будет действительна 24 часа <br /><br />
                Администрация Univer-Мag";
        $mail = new u_mail(true);
        $r = $mail->sendmail($_SERVER['HTTP_HOST'], 'robot@'.$_SERVER['HTTP_HOST'], $userExists["email"], $_SERVER['HTTP_HOST'], $msg);

        if ($r) {
            $record = new Audit([
                "group" => "pass_recovery_request",
                "subgroup" => "success",
                "action" => "Отправка письма для восстановления пароля",
            ]);

            $record->addDetails([
                "email" => $email,
            ]);

            $record->save();
        }
    } else {

        echo "Email не найден";

        $record = new Audit([
            "group" => "pass_recovery_request",
            "subgroup" => "fail",
            "action" => "Email не найден",
        ]);

        $record->addDetails([
            "email" => $email,
        ]);

        $record->save();
    }

    exit();
?>