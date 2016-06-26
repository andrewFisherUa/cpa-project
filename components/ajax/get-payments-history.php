<?php

  $filter = new Filter;

  $isAdmin = User::isAdmin();
  $uid = User::get_current_user_id();

  $rows = [];

  if ($isAdmin) {

    $status = $filter->sanitize($_REQUEST['status'], ["string", "striptags"]);

    if (in_array("moderation", $status)) {
      $query = "SELECT * FROM payments WHERE status = 'moderation' ORDER BY created desc";
      $rows = $GLOBALS['DB']->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    $iTotalRecords = count($rows);

    $parameters = [];

    if (in_array("approved", $status)) {
      $parameters[] = "status = 'approved'";
    }

    if (in_array("canceled", $status)) {
      $parameters[] = "status = 'canceled'";
    }

    if (!empty($parameters)) {
      $query = "SELECT * FROM payments WHERE " . implode(" OR ", $parameters) . " ORDER BY created desc";
      $stmt = $GLOBALS['DB']->query($query);
      
      $iTotalRecords += $stmt->rowCount();
      $iDisplayLength = intval($_REQUEST['length']);
      $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
      $iDisplayStart = intval($_REQUEST['start']);

      $query .= " LIMIT {$iDisplayStart}, {$iDisplayLength}";
      $rows = array_merge($rows, $GLOBALS['DB']->query($query)->fetchAll(PDO::FETCH_ASSOC));
    }


  } else {
    $query = "SELECT * FROM payments WHERE user_id = ? ORDER BY created DESC";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute([
      $uid
    ]);

    $iTotalRecords = $stmt->rowCount();
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
    $iDisplayStart = intval($_REQUEST['start']);
  
    $query .= " LIMIT {$iDisplayStart}, {$iDisplayLength}";    

    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute([
      $uid
    ]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  $sEcho = intval($_REQUEST['draw']);

  $data = [];

  foreach ($rows as $row) {
    $item = new Payment($row);
    $status = "<span class='label label-sm label-" . $item->getStatus() . "'>" . $item->getStatusAlias() . "</span>";

    if ($isAdmin) {

      $user = User::getInstance($item->getUserId());

      $username = $user->getId() . ": " . $user->getLogin();

      $id = $item->getId();

      $actions = "<a href='/admin/payments/{$id}' target='_blank' class='btn btn-sm yellow-crusta'>Просмотр</a>";
 
      $balance_after = "";

      if ($item->getStatus() == Payment::STATUS_APPROVED) {
        $balance_before = "<span class='money'>" . round($item->getBalanceBefore()) . "</span> " . $item->getCurrency();
        $balance_after = "<span class='money'>" . round($item->getBalanceAfter()) . "</span> " . $item->getCurrency();
      } else {
        $b = new DefaultBalance($user->getId());
        $balance_before = "<span class='money'>" . round($b->getCurrent() + $b->getReferal()) . "</span> " . $item->getCurrency();
      }

      $was_changed = ($row["changed"] > 0) ? "was_changed" : "";

      $data[] = array(
        "<span class='{$was_changed}'>{$id}</span>",
        $username,
        date('Y-m-d H:i', $item->getCreated()),
        $balance_before,
        "<span class='money'>" . round($item->getAmount()) . "</span> " . $item->getCurrency(),
        $balance_after,
        $item->getWallet(),
        $status,
        $actions
      );
    } else {
      $data[] = array(
        $item->getId(),
        date('Y-m-d h:i:s', $item->getCreated()),
        $item->getAmount() . " " . $item->getCurrency(),
        $status,
        $item->getWallet(),
        $item->getComment(),
      );
    }
  }

  $records["data"] = $data;
  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>