<?php

  $isAdmin = User::isAdmin();

  if ($isAdmin) {
    $stmt = $GLOBALS['DB']->query("SELECT * FROM transactions WHERE `type` = 'transfer' ORDER BY created DESC");
  } else {
    $user_id = User::get_current_user_id();
    $query = "SELECT * FROM transactions WHERE user_id = ? AND `type` = 'transfer' ORDER BY created DESC";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute([$user_id]);
  }

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

    $data = [];
    $data[] = $item['transaction_id'];
    $data[] = date("d/m/Y H:m:s", $item['created']);
    if ($isAdmin) {
      $data[] = $item["user_id"];
    }
    $data[] = "<span class='money'>" . $item["from_amount"] . "</span>&nbsp;" . $item["from_currency"] . " / " . "<span class='money'>" . $item["amount"] . "</span>&nbsp;" . $item["to_currency"];
    $data[] = empty($item['residue']) ? "-" : "<span class='money'>" . $item["residue"] . "</span>&nbsp;" . $item["from_currency"];
    $data[] = round($item["rate"], 5);
    if ($isAdmin) {
      $data[] = "<span class='label label-sm label-".$item['status']."'><a href='javascript:;' id='status" . $item['transaction_id'] . "' class='status' data-subject='". $item['transaction_id'] ."' data-type='select' data-pk='1' data-value='" . $item['status'] . "' data-original-title='Статус пользователя'>". $item['status'] ."</a></span>";
    } else {
      $data[] = "<span class='label label-sm label-".$item['status']."'>".$item['status']."</span>";
    }

    $records["data"][] = $data;
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>