<?php

if (in_array(User::get_current_user_id(), [69, 20, 21])) {

	if ($_REQUEST['k'] == "user") {
		require_once 'part/audit/user.php';
	} else {
		require_once 'part/audit/general.php';
	}

} else {
	echo "<div class='alert alert-danger'>Отказано в доступе</div>";
}

?>