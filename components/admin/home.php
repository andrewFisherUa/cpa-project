<?php

$query = "SELECT t2.role_name 
		  FROM user_role AS t1 INNER JOIN roles AS t2 ON t1.role_id = t2.role_id
		  WHERE t1.user_id = ?";

$stmt = $GLOBALS["DB"]->prepare($query);
$stmt->execute([
	User::get_current_user_id()
]);

if ($stmt->rowCount()) {
	$role_name = $stmt->fetchColumn();
	require_once "part/home/{$role_name}.php";
}

?>
