<?php

  $action = $_POST['action'];
  $g_id = $_POST['g_id'];
  $u_id = User::get_current_user_id();

  if ( $action == 'add' ) {
    $query = "INSERT INTO users2goods (u_id, g_id) VALUES ( :u_id, :g_id )";
    $subgroup = "connect";
  }

  if ( $action == 'remove' ) {
    $query = "DELETE FROM users2goods WHERE u_id = :u_id AND g_id = :g_id; DELETE FROM flows WHERE user_id = :u_id AND offer_id = :g_id;";
    $subgroup = "disconnect";
  }

  $record = new Audit([
    "group" => "offer",
    "action" => $subgroup . " offer `{$g_id}`",
    "subgroup" => $subgroup,
  ]);

  $record->addDetails([
    "offer_id" => $g_id,
  ]);

  $record->save();

  $stmt = $GLOBALS['DB']->prepare( $query );
  $response = $stmt->execute( array( ':u_id' => $u_id, ':g_id' => $g_id ) );

  echo json_encode( array( 'success' => $response, 'g_id' => $g_id, 'u_id' => $u_id ) );

  die();

?>