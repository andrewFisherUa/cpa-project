<?php

$response = array();

if ( isset($_POST['email']) ) {
	$options = new Options();
	$old_email = $options->get_option("support_email");
	$new_email = $_POST['email'];
	if ( $old_email != $new_email ) {
		$options->set_option("support_email", $_POST['email']);
		$options->save();
		$response['success'] = 'Email службы поддержки успешно изменен.';
	}

}

echo json_encode( $response );
?>