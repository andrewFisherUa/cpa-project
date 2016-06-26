<?php

  if (!User::isAdmin()) {
    die();
  }

  $filter = new Filter;

  $user_id = $filter->sanitize($_REQUEST["user_id"], "int");
  
  $query = "SELECT login FROM users WHERE user_id = ?";
  $stmt = $GLOBALS["DB"]->prepare($query);
  $stmt->execute([
    $user_id
  ]);

  $username = $stmt->fetch(PDO::FETCH_ASSOC);

  $query = "SELECT * FROM payments WHERE user_id = ?";
  $stmt = $GLOBALS["DB"]->prepare($query);
  $stmt->execute([
    $user_id
  ]);
    
  $iTotalRecords = $stmt->rowCount();
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);

  $query .= " ORDER BY payment_id DESC LIMIT {$iDisplayStart}, {$iDisplayLength}";
  $stmt = $GLOBALS["DB"]->prepare($query);
  $stmt->execute([
    $user_id
  ]);

  $data = [];

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $item = new Payment($row);
    $status = "<span class='label label-sm label-" . $item->getStatus() . "'>" . $item->getStatusAlias() . "</span>";

    $username = $user_id . ": " . $username;

    $id = $item->getId();

    $actions = "<a href='/admin/payments/{$id}' class='btn btn-sm yellow-crusta'>Просмотр</a>";

    $balance_after = "";

    if ($item->getStatus() == Payment::STATUS_APPROVED) {
      $balance_before = "<span class='money'>" . round($item->getBalanceBefore()) . "</span> " . $item->getCurrency();
      $balance_after = "<span class='money'>" . round($item->getBalanceAfter()) . "</span> " . $item->getCurrency();
    } else {

      if (!isset($b)) {
        $b = new DefaultBalance($user_id);
      }

      $balance_before = "<span class='money'>" . round($b->getCurrent() + $b->getReferal()) . "</span> " . $item->getCurrency();
    }

    $was_changed = ($row["changed"] > 0) ? "was_changed" : "";

    $data[] = array(
      "<span class='{$was_changed}'>{$id}</span>",
      date('Y-m-d H:i', $item->getCreated()),
      $balance_before,
      "<span class='money'>" . round($item->getAmount()) . "</span> " . $item->getCurrency(),
      $balance_after,
      $item->getWallet(),
      $status,
      $actions
    );
  }

  $records = [
    "data" => $data,
    "draw" => intval($_REQUEST['draw']),
    "recordsTotal" => $iTotalRecords,
    "recordsFiltered" => $iTotalRecords
  ];

  echo json_encode($records);
?>