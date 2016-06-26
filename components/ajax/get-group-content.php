<?php

  $g_id = isset($_POST['g_id']) ? $_POST['g_id'] : 0;
  $c_id = isset($_POST['c_id']) ? $_POST['c_id'] : 0;
  $action = $_POST['action'];

  $respond = array( "rows" => "" );

  foreach ( $_SESSION['content_groups'] as $group_id=>$group ) {
    if ( isset( $group[$c_id] ) ) {
      unset( $_SESSION['content_groups'][$group_id][$c_id] );
    }
    if ( empty( $_SESSION['content_groups'][$group_id] ) ) {
      unset( $_SESSION['content_groups'][$group_id] );
    } else {
      $temp = Content::get_group($group_id);
      $respond["rows"]["groups"] .= '<option value="' . $group_id . '">' . $temp['name'] . '</option>';
    }
  }

  if ( $action == "remove" ) {
    $stmt = $GLOBALS['DB']->prepare('SELECT cg.g_id, c.c_id, c.name FROM content_group AS cg RIGHT JOIN content AS c ON cg.c_id = c.c_id WHERE c.c_id = :c_id');
    $stmt->execute( array(":c_id" => $c_id ) );
    $rows = $stmt->fetchAll( PDO::FETCH_ASSOC );
    foreach ( $rows as $row ) {
      $_SESSION['content_groups'][$row['g_id']][$row['c_id']] = $row;
      $temp = Content::get_group($row['g_id']);
      $respond["rows"]["groups"] .= '<option value="' . $row['g_id'] . '">' . $temp['name'] . '</option>';
    }
  }

  $landings = $_SESSION['content_groups'][$g_id];

  if ( $landings ) {
    foreach ( $landings as $lp ) {
      $respond["rows"]["landings"] .= '<option value="' . $lp['c_id'] . '">' . $lp['name'] . '</option>';
    }
  }

  echo json_encode( $respond );
  die();

?>