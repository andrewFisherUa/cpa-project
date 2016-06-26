<?php

$filter = new Filter;

$response = [ 
	"errors" => [], 
	"success" => []
];

$action = $filter->sanitize($_POST["action"], ["string", "striptags"]);

if ($action == "request-api") {

	require_once( PATH_ROOT .'/misc/plugins/php/mail/PHPMailer/PHPMailerAutoload.php');
	require_once( PATH_ROOT .'/misc/plugins/php/mail/u_mail.php');

	$uid = User::get_current_user_id();

	$stmt = $GLOBALS['DB']->prepare("SELECT hash FROM api_requests WHERE user_id = ?");
	$stmt->execute([$uid]);
    if ($stmt->rowCount() > 0) {
    	$response["errors"][] = "Ваш запрос находится в обработке!";
    } else {

    	$ticket = new Ticket([
	        "subject" => "Запрос API-ключа",
	        "user_id" => $uid,
	        "urgent" => 1,
	    ]);

	    $ticket->addMessage(
	    	new TicketMessage([
		        "from_uid" => $uid,
		        "to" => 0,
		        "message" => "Здравствуйте. Прошу выдать ключ для доступа к API.",
		    ])
		);

	    $result = $ticket->save();

	    if ($result === true) {
	    	$response["success"][] = "По Вашему запросу был создан <a href='" . get_site_url() . "/admin/tickets/{$ticket->getId()}'>тикет</a>. Ожидайте ответа от службы поддержки.";

	    	// Добавляем запрос
	    	$stmt = $DB->prepare("SELECT user_id, email FROM users WHERE user_id = ?");
		    $stmt->execute([$uid]);
		    $h = $stmt->fetch(PDO::FETCH_ASSOC);
		    $hash = md5($h['user_id'] . $h['email']);

		    $query = "INSERT INTO api_requests (user_id, hash, ticket_id, created) VALUES (:uid, :hash, :tid," . time() . ")";
		    $stmt = $DB->prepare($query);
		    $stmt->bindParam(":hash", $hash, PDO::PARAM_STR);
		    $stmt->bindParam(":uid", $uid, PDO::PARAM_INT);
		    $stmt->bindParam(":tid", $ticket->getId(), PDO::PARAM_INT);
		    $stmt->execute();

			// отправка сообщенния в саппорт
			$options = new Options();
		    $support_email = $options->get_option("support_email");

		    $stmt = $GLOBALS['DB']->prepare("SELECT login FROM users WHERE user_id = ?");
		    $stmt->execute([$uid]);
		    $login = $stmt->fetchColumn();

		    $message = "<p>Запрос API-ключа от {$uid}: {$login}</p>";
		    $message .= "<p><a href='" . get_site_url() . "/admin/api_request?status=accepted&uid={$uid}'>Выдать ключ</a></p>";
		    $message .= "<p><a href='" . get_site_url() . "/admin/api_request?status=refused&uid={$uid}'>Отказать</a></p>";
		    $message .= "<p>С уважением,</p><p>Служба поддержки Univer-Mag</p>";
		    
		    $mail = new u_mail(true);
		    $mail->sendmail($_SERVER['HTTP_HOST'], 'support@' . $_SERVER['HTTP_HOST'], $support_email, "Запрос API-ключа", $message);
		    $mail->sendmail($_SERVER['HTTP_HOST'], 'support@' . $_SERVER['HTTP_HOST'], "sorochan.e.a@gmail.com", "Запрос API-ключа", $message);

	    } else {
	    	$response["errors"][] = "Возникла ошибка";
	    }
    }
}

if ($action == "set-main-wallet") {
	$wallet = $filter->sanitize($_POST["wallet"], ["string", "striptags"]);
	$user_id = User::get_current_user_id();

	// Удаляем старый кошелек по умолчанию
	$query = "UPDATE user_wallet SET main = 0 WHERE user_id = ?";
	$stmt = $GLOBALS["DB"]->prepare($query);
	$stmt->execute([
		$user_id
	]);

	// Сохраняем новый кошелек
	$query = "UPDATE user_wallet SET main = 1 WHERE wallet = ? AND user_id = ?";
	$stmt = $GLOBALS["DB"]->prepare($query);
	$r = $stmt->execute([
		$wallet,
		$user_id
	]);

	$response["success"] = $r;
}

if ($action == "save-notify") {
	$acceptable_options = [1, 2, 3, 4, 5, 6];

	$query = "UPDATE user_option SET value = ? WHERE user_id = ? AND uoption = ?";
	$stmt = $GLOBALS["DB"]->prepare($query);

	$audit_record = [
	    "group" => "news",
	    "subgroup" => "save_settings",
	    "action" => "Изменение настроек уведомлений",
	    "details" => []
	];

	$uid = User::get_current_user_id();

	foreach ($_POST["options"] as $k) {
	    if (in_array($k["id"], $acceptable_options)) {

	        $option_id = $k["id"];
	        $value = $filter->sanitize($k["value"], "int!");

	        $stmt->execute([
	            $value,
	            $uid,
	            $option_id
	        ]);

	        $audit_record["details"][$option_id] = $value;
	    }
	}

	Audit::addRecord($audit_record);
}

if ($action == "change-password") {

	$uid = User::get_current_user_id();
	$pass = $filter->sanitize($_POST['password'], ["string", "striptags"]);

	$r = User::changePassword($uid, $pass);

	if ($r) {
		Audit::addRecord([
			"group" => "user",
			"subgroup" => "change_password",
			"action" => "Изменение пароля на странице профиля",
		]);
	}
}

if ($action == "save-info") {

	$user_id = User::get_current_user_id();

	$acceptable = ["phone", "last_name", "name"];
	$data = [];

	foreach ($acceptable as $a) {
		if (array_key_exists($a, $_POST["fields"])) {
			$data[$a] = $filter->sanitize($_POST["fields"][$a], ["string", "striptags"]);
		}
	}	

	if ( isset($data["phone"]) ) {

		$query = "SELECT id FROM partners WHERE phone = ? AND id != ?";
		$stmt = $GLOBALS["DB"]->prepare($query);
		$stmt->execute([
			$data["phone"],
			$user_id
		]);

        if ( $stmt->rowCount() ) {
            $response['errors'][] = "Такой телефон уже есть в базе данных";
        }
	}

    if (empty($response['errors'])) {
        $query = "UPDATE partners ";
        $set = []; $params = [];

        foreach ( $data as $a=>$b ) {
        	if (!empty($b)) {
        		$set[] = "{$a} = ?";
        		$params[] = $b;
        	}
        }	

        if (!empty($set)) {
            $query .= " SET " . implode(",", $set) . " WHERE id = ?";
            $params[] = $user_id;

            $stmt = $GLOBALS['DB']->prepare($query);
            $r = $stmt->execute($params);
            if ($r) {
            	Audit::addRecord([
	               "group" => "user",
	               "subgroup" => "save_profile",
	               "action" => "Сохранение профиля на странице `Профиль`",
	               "details" => $data
	            ]);
            } else {
            	$response['errors'][] = "Ошибка при сохранении";
            }
        }
    }
}

echo json_encode($response);


?>