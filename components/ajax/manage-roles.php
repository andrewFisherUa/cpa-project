<?php

$action = $_POST['action'];
$response = array();

if ( $action == "edit" ) {
	$id = $_POST['id'];
	$smarty->assign( "role_id", $id );
	$smarty->assign( "role_name", Role::get_role_name( $id ) );
	$response['form'] = $smarty->fetch( 'admin' . DS . 'permissions' . DS . 'ajax' . DS . 'edit-role.tpl' );
}

if ( $action == "save" ) {
	$id = $_POST['id'];
	$name = $_POST['name'];

	$test_role = Role::get_role_id($name);

	if ( ($id == 0 && $test_role) || ( $id!= 0 &&  $test_role != $id) ) {
		$response['error'] = "Роль `{$name}` уже существует";
	} else {
		if ( $id == 0 ) {
			Role::add( $name );
		} else {
			Role::upd( $id, $name );
		}
	}
}

if ( $action == "remove" ) {
	Role::delete( $_POST['id'] );
}

echo json_encode( $response );
?>