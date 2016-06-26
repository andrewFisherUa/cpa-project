<?php

$template = "index";

// Добавление пользователя
if ( $_REQUEST['k'] == 'add' ) {
	$smarty->assign('roles', Role::get_all() );
	$template = "add-user";
}

// Добавляем пользователя
if ( isset($_POST['add_user']) ) {
	$user = new User( $_POST );

	$parts = explode("@", $user->email);
	$user->login = $parts[0];
    $r = $user->save();

    if ( $r === false ) {
    	$message = array( 'text' => "Email занят", 'class_name' => "danger" );
    } else {
    	User::upd_status( $user->id, 2 );

    	if (in_array(1, $_POST['roles']) || in_array(2, $_POST['roles'])) {
		    $stmt = $GLOBALS['DB']->prepare( "INSERT INTO partners (id) VALUES (:id)");
		    $p = $stmt->execute(array('id'=>$user->id));
    	}

    	foreach ($_POST['roles'] as $role_id) {
	    	//$user->set_role($user->id, $role_id);
	    }
	    echo "<script>window.location = '/admin/users' </script>";
    }
}

if ($_REQUEST['k'] == "balance") {
	//
	$smarty->assign('countries', Country::getAll());
	$template = "balance";
}

if ( $_REQUEST['k'] == "" ) {
	$status = array(
		array( "id" => 0, "name" =>  "Не подтвержден"),
		array( "id" => 1, "name" =>  "На модерации"),
		array( "id" => 2, "name" =>  "Активирован"),
		array( "id" => 3, "name" =>  "Заблокирован"));

	$smarty->assign("status", $status);

	// Роли
	$roles = Role::get_all();
	foreach ($roles as &$a){
		$a['alias'] = Role::getAlias($a['role_name']);
	}
	$smarty->assign("roles", $roles);

	// страны
	$query = "SELECT DISTINCT country_name FROM users WHERE country_name != '' ORDER BY country_name";
	$countries = $GLOBALS['DB']->query($query)->fetchAll(PDO::FETCH_COLUMN);
	$smarty->assign('countries', $countries);
}

$smarty->assign('message', $message);
$smarty->display( 'admin' . DS . 'users' . DS . $template . '.tpl' );

enqueue_scripts( array(
    	"/assets/global/plugins/datatables/datatables.min.js",
	    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
	    "/assets/global/scripts/datatable.js",
	    "/assets/global/plugins/uniform/jquery.uniform.min.js",
	    "/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
		"/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"));

if ($_REQUEST['k'] == 'add' || $_REQUEST['k'] == "") {
	enqueue_scripts([
		"/misc/js/page-level/users.js",
	]);
}

if ($_REQUEST['k'] == 'balance') {
	enqueue_scripts(["/misc/js/page-level/balance.js"]);
}

?>