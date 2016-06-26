<?php

  $filters = [];

  if (!empty($_POST['filters'])) {
    foreach ($_POST['filters'] as $k=>$f) {
      if ($f != '-1') {
        $filters[$k] = $f;
      }
    }
  }

  $items = Converter::getSpecial($filters);
  $iTotalRecords = count($items);
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
    $user = User::getInstance($item["user_id"]);
    $isActive = "";
    if ($item['is_active']) {
      $isActive = "active";
    }

    $state = '<span class="label label-' . $item['status_alias'] . ' '.$isAstive.'">' . $item['status_alias'] . '</span>';

    $records["data"][] = array(
      '<input type="checkbox" name="id[]" value="' . $item['id'].'">',
      $item["id"],
      $item["from"] . "/" . $item["to"],
      round_val($item["bid"]),
      round_val($item["ask"]),
      date("Y-m-d H:i", $item["start"]),
      date("Y-m-d H:i", $item["end"]),
      '<div class="text-center">' . $state . '</div>',
      $user->getId() . ": " . $user->getLogin(),
      date("Y-m-d H:i", $item["created"]),
      );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>