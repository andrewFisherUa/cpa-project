<?php

  $user_id = User::get_current_user_id();
  $referals = [];

  // 1 уровень
  $temp = getReferals($user_id);

  if (!is_null($temp)) {
    $referals[1] = $temp;
  }

  if (!empty($referals[1])) {
    foreach (array_keys($referals[1]) as $ref1) {
      $temp = getReferals($ref1);
      if (!is_null($temp)) {
        $referals[2] = $temp;
      }
    }
  }

  if (!empty($referals[2])) {
    foreach (array_keys($referals[2]) as $ref2) {
      $temp = getReferals($ref2);
      if (!is_null($temp)) {
        $referals[3] = $temp;
      }
    }
  }

  $items = [];

  foreach ($referals as $level=>&$referals) {
    foreach ($referals as $referal_id=>$referal) {
      $items[] = [
        "id" => $referal_id,
        "login" => $referal['login'],
        "created" => $referal['created'],
        "profit" => getProfit($user_id, $referal_id, "RUB"),
        "level" => $level
      ];
    }
  }

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

    $records["data"][] = array(
      $i+1,
      $items[$i]['login'],
      $items[$i]['created'],
      $items[$i]['level'],
      $items[$i]['profit'] . "&nbsp;RUB",
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>