<?php

$action = $_POST['action'];
$response = array();

if ( $action == "edit" ) {
	$id = $_POST['id'];
	$rubric = ( $id > 0 ) ?  Article::get_rubric( $id ) : array( "rubric_id" => 0 );
	$smarty->assign('rubric', $rubric);
	$smarty->assign('weight', range(0,10));
	$response['form'] = $smarty->fetch( 'admin' . DS . 'faq' . DS . "ajax" . DS . "rubric-form.tpl" );
}

if ( $action == "save" ) {
	Article::save_rubric( $_POST );
}

if ( $action == "remove" ) {
	Article::remove_rubric( $_POST['id'] );
}

echo json_encode( $response );

?>