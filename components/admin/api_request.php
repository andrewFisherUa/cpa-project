<?php

$errors = [];

if (User::isAdmin()) {
	$filter = new Filter;
	$status = $filter->sanitize($_REQUEST['status'], ["string", "striptags"]);
	$uid = $filter->sanitize($_REQUEST['uid'], "int!");

	$v = change_api_key_status($uid, $status);

	if ($v === true) {
		$message = "";
		if ($status == "accepted") {
	        $message = "Запрос API-ключа одобрен.";
	    } else {
	        $message = "Запрос API-ключа отклонен.";
	    }

	    echo "<div class='alert alert-success'>{$message}</div>";
	} else {
		$errors = $v;
	}
} else {
	$errors[] = "Отказано в доступе.";
}

if (count($errors)) {
	echo "<div class='alert alert-danger'>" . implode("<br/>", $errors) . "</div>";
}

?>