<?php

  $users = [];

  $stmt = $GLOBALS['DB']->query("SELECT * FROM account_currency ORDER BY created DESC");
  $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $iTotalRecords = count( $items );
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $records = array();
  $records["data"] = array();

  $end = $iDisplayStart + $iDisplayLength;
  $end = $end > $iTotalRecords ? $iTotalRecords : $end;

  for($i = $iDisplayStart; $i < $end; $i++) {

    $item = $items[$i];

    if (!empty($users[$item['user_id']])) {
      $login = $users[$item['user_id']];
    } else {
      $temp = User::getInstance($item['user_id']);
      $login = $temp->getLogin();
      $users[$item['user_id']] = $login;
    }

    $created = date("d/m/Y H:m:s", $item["created"]);
    $changed = ($item["changed"] == 0) ? "" : date("d/m/Y H:m:s", $item["changed"]);

    if ($item['status'] == "processing") {
      $status = "<div class='text-center'><span class='label label-sm label-".$item['status']."'><a href='javascript:;' id='status" . $item['id'] . "' class='status' data-subject='". $item['id'] ."' data-type='select' data-pk='1' data-value='" . $item['status'] . "'>". $item['status'] ."</a></span></div>";
    } else {
      $status =  "<div class='text-center'><span class='label label-sm label-" . $item['status'] . "'>" . $item['status'] . "</span></div>";
    }

    $records["data"][] = array(
      $item['id'],
      $item['user_id'],
      $login,
      $item["default_currency"],
      $item["currency"],
      $created,
      $status,
      $changed
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>