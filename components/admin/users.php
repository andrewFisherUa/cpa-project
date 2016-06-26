<?php

if (User::isAdmin()) {
	require_once 'part/users/admin.php';
}

if (User::isSupport()) {
	require_once 'part/users/support.php';
}

?>