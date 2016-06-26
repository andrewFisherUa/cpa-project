<?php

session_start();

$session_id = session_id();

if (!$_SESSION['was_admin'])  {
	$user_id = User::get_current_user_id();

	$time = time();
	$time_check = $time - 300;

	$query = "DELETE FROM users_online 
			  WHERE ( time < ? ) OR (session = ? AND user_id = ?)";
	$stmt = $GLOBALS['DB']->prepare($query);
	$stmt->execute([
		$time_check,
		$session_id,
		$user_id
	]);
	
	$query = "INSERT INTO users_online (user_id, session, time) VALUES ( ?, ?, ?)";
	$stmt = $GLOBALS['DB']->prepare($query);
	$stmt->execute([
		$user_id,
		$session_id,
		$time
	]);
}