<?php

$filter = new Filter;

$action = $filter->sanitize($_POST["action"], ["string", "striptags"]);
$response = array();

// Зайти в кабинет пользователя
if ( $action == "login-as" ) {

	$role = $filter->sanitize($_POST["role"], ["string", "striptags"]);

	if (isset($role)) {
		$user_id = ($role == "webmaster") ? 208 : 202;
	} else {
		$user_id = $filter->sanitize($_POST["user_id"], "int");
	}

	$query = "SELECT u.*, r.*
	          FROM users as u, user_role as ur, roles as r
		      WHERE u.user_id = ur.user_id AND ur.role_id = r.role_id AND u.user_id = :user_id";

	$stmt = $GLOBALS['DB']->prepare( $query );
	$stmt->execute(array(":user_id" => $user_id));
	$data = $stmt->fetch(PDO::FETCH_ASSOC);

	$_SESSION['was_admin'] = User::get_current_user_id();
	$_SESSION['adlog'] = 1;
    $_SESSION['user'] = $data;

	$isAdmin = User::isAdmin();
	$_SESSION['admin'] = $isAdmin;
	$_SESSION['partner'] = !$isAdmin;

	$response['url'] = get_admin_url();

	// create audit record
	Audit::addRecord([
		"group" => "login_as",
		"action" => "Вход в кабинет пользователя с ID `{$user_id}`",
	]);
}

// Выйти из кабинета пользователя
if ( $action == "logout" ) {

	Audit::addRecord([
    	"group" => "logout_as",
    	"action" => "Выход из кабинета пользователя с ID `" . User::get_current_user_id() . "`"
    ]);

	$query = "SELECT u.*, r.*
	          FROM users as u, user_role as ur, roles as r
		      WHERE u.user_id = ur.user_id AND ur.role_id = r.role_id AND u.user_id = :user_id";

	$stmt = $GLOBALS['DB']->prepare( $query );
	$stmt->execute( array( ":user_id" => $_SESSION['was_admin']) );
	$data = $stmt->fetch( PDO::FETCH_ASSOC );

	$_SESSION['adlog'] = 1;
    $_SESSION['user'] = $data;
    $_SESSION['admin'] = true;
    $_SESSION['partner'] = false;
    $_SESSION['was_admin'] = false;

    $response['url'] = get_admin_url() . "/users";

}

// Загрузить профиль пользователя
if ( $action == "get-profile" ) {

	$uid = $filter->sanitize($_POST["user_id"], "int");

	$isWebmaster = Privileged_User::has_role($uid, "webmaster");
	$isPartner = User::isPartner($uid);

	if ($isPartner) {
		$query = "SELECT p.last_name, p.name AS first_name, u.email, p.phone, u.login, u.created, p.skype, u.user_id
	          FROM users AS u INNER JOIN partners AS p ON u.user_id = p.id
	          WHERE u.user_id = :user_id";
		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->execute([
			":user_id" => $uid
		]);

		$data = $stmt->fetch( PDO::FETCH_ASSOC );
		$data['created'] = date("Y-m-d H:i:s", $data['created']);
	}
	
	$options = new UserOptions($GLOBALS["DB"], $uid);
	$data["options"] = $options->getAll();
	
	$smarty->assign('data', $data);
	$smarty->assign('isPartner', $isPartner);
	$smarty->assign('isWebmaster', $isWebmaster);

	// options
	$response['rows'] = $smarty->fetch('admin' . DS . 'users' . DS . 'ajax' . DS . 'profile.tpl');

	// Сохранение записи о просмотре профиля
	Audit::addRecord([
		"group" => "user",
		"subgroup" => "view_profile",
		"action" => "Просмотр профиля пользователя с ID `{$uid}`",
	]);
}

if ( $action == "save-profile" ) {

	$response['errors'] = array();

	$email = $filter->sanitize($_POST["email"], ["string", "striptags"]);
	$phone = $filter->sanitize($_POST["phone"], ["string", "striptags"]);
	$password = $filter->sanitize($_POST["password"], ["string", "striptags"]);
	$uid = $filter->sanitize($_POST["id"], "int");

	if ( User::get_by_field('email', $email, $uid) ) {
       $response['errors'][] = "Email " . $email . " занят";
    }

	$is_partner = User::is_partner($_POST['id']);
	$isWebmaster = Privileged_User::has_role($_POST['id'], "webmaster");

	if ($phone != '') {
		if ( $is_partner && Partner::getByField('phone', $phone, $uid) ) {
	       $response['errors'][] = "Номер телефона " . $phone . " занят";
	    }
	}

    if ( count($response['errors']) == 0) {
    	if (!empty($password)) {

    		User::changePassword($uid, $password);

    		// Сохранение записи о смене пароля
    		Audit::addRecord([
    			"group" => "user",
    			"subgroup" => "change_password",
    			"priority" => Audit::HIGH_PRIORITY,
    			"action" => "Изменение пароля для пользователя `" . $uid . "`",
    		]);
    	}

		if ( $is_partner ) {

			$name = $filter->sanitize($_POST["name"], ["string", "striptags"]);
			$last_name = $filter->sanitize($_POST["last_name"], ["string", "striptags"]);
			$phone = $filter->sanitize($_POST["phone"], ["string", "striptags"]);

			// Сохраняем профиль пользователя
			$stmt = $GLOBALS['DB']->prepare("UPDATE partners SET name = :name, last_name = :last_name, phone = :phone WHERE id = :id");
			$stmt->bindParam(":name", $name, PDO::PARAM_STR);
			$stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
			$stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
			$stmt->bindParam(":id", $uid, PDO::PARAM_INT);
			$stmt->execute();

			// Сохраняем запись об изменении профиля
			Audit::addRecord([
				"group" => "user",
				"subgroup" => "save_profile",
				"priority" => Audit::MEDIUM_PRIORITY,
				"action" => "Сохранение профиля пользователя с ID `" . $uid . "`",
				"details" => [
					"name_first" => $name,
					"name_last" => $last_name,
					"phone" => $phone,
					"user_id" => $uid
				] 
			]);

			// Сохраняем настройки пользователя
			$options = new UserOptions($GLOBALS["DB"], $uid);
			foreach ($_POST['options'] as $name => $value) {
				$name = $filter->sanitize($name, ["string", "striptags"]);
				$value = $filter->sanitize($value, "int!");

				$options->set($name, $value);
			}

			$options->save();
		}
    }
}

if ( $action == "get-emails" ) {
	$query = "SELECT u.email 
			  FROM users AS u INNER JOIN user_role AS r ON u.user_id = r.user_id
			  WHERE r.role_id = 2";
	$stmt = $GLOBALS['DB']->query($query);
	$response['list'] = $stmt->fetchAll(PDO::FETCH_NUM);
}

echo json_encode( $response );

?>