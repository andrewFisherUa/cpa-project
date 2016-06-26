<?php

    $filter = new Filter();

    $email = $filter->sanitize($_POST['alogin'], ["string", "striptags"]);
    $password = $filter->sanitize($_POST['apassword'], ["string", "striptags"]);

    $d = User::check($email, $password);

    if ($d === false) {
    	echo $error = "Неверное имя пользователя или пароль. Проверьте правильность введенных данных.";
    } else {
    	switch ($d['status']) {
    		case User::STATUS_NEW : echo "Email не подтвержден"; break;
    		case User::STATUS_BLOCKED : echo "Аккаунт заблокирован"; break;
    		case User::STATUS_MODERATION : echo "Аккаунт на модерации"; break;
    	}
    }

    die();
?>