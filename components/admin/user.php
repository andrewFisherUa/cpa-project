<?php

$user_id = $_REQUEST['k'];
$error = false;

if (!empty($user_id)) {
	$stmt = $GLOBALS['DB']->prepare("SELECT role_id FROM user_role WHERE user_id = ?");
	$stmt->execute([$user_id]);
	if ($stmt->rowCount()) {
		$role_id = $stmt->fetchColumn();
		require_once "part" . DS . "user" . DS . "role-" . $role_id . ".php";
	} else {
		$error = true;
	}
} else {
	$error = true;
}

if ($error) {
	echo "<div class='alert alert-danger'>Пользователь не найден</div>";
}

?>