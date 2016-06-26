<?php

  $filter = new Filter;

  $allowed_filters = ["id", "oid", "user_id", "status"];
  $params = [];

  foreach ($allowed_filters as $var) {
    if (!empty($_REQUEST[$var]) && $_REQUEST[$var] != -1) {
      $params[] = $var . " = " . $filter->sanitize($_REQUEST[$var], "int!");
    }
  }

  if (array_key_exists('status', $_REQUEST) && $_REQUEST['status'] != '-1') {
    $params[] = "status = " . $filter->sanitize($_REQUEST["status"], "int!");
  }

  if (!empty($_REQUEST['date_from'])){
    $data = explode("/", $_REQUEST['date_from']);
    $temp = mktime(0, 0, 0, $data[1], $data[0], $data[2]);
    $params[] = "created > " . $filter->sanitize($temp, "int!");
  }

  if(!empty($_REQUEST['date_to'])){
    $data = explode("/", $_REQUEST['date_to']);
    $temp = mktime(0, 0, 0, $data[1], $data[0], $data[2]);
    $params[] = "created < " . $filter->sanitize($temp, "int!");
  }

  if (!empty($_REQUEST['commission_from'])) {
    $params[] = "commission > " . $filter->sanitize($_REQUEST['commission_from'], "int!");
  }

  if (!empty($_REQUEST['commission_to'])) {
    $params[] = "commission < " . $filter->sanitize($_REQUEST['commission_to'], "int!");
  }

  if (!empty($_REQUEST['amount_from'])) {
    $params[] = "amount > " . $filter->sanitize($_REQUEST['amount_from'], "int!");
  }

  if (!empty($_REQUEST['amount_to'])) {
    $params[] = "amount < " . $filter->sanitize($_REQUEST['amount_to'], "int!");
  }

  if (array_key_exists('source', $_REQUEST) && $_REQUEST['source'] != '-1') {
    $params[] = "source = '" . $filter->sanitize($_REQUEST["source"], ["string", "striptags"]) . "'";
  }

  $query = "SELECT * FROM orders";

  if (!empty($params)) {
    $query .= " WHERE " . implode(" AND ", $params);
  }

  $query .= " ORDER BY created DESC";
  $total = $GLOBALS['DB']->query($query);

  $iTotalRecords = $total->rowCount();
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $records = ["data" => []];

  $query .= " LIMIT {$iDisplayStart}, {$iDisplayLength}";
  $stmt = $GLOBALS['DB']->query($query);
  $time = time();

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $item = new Order($row);
    $commission = User::isAdmin() ? $item->getCommission() : $item->getWebmasterCommission();
    $hold_time = "";

    if ($item->getTargetTime() > 0) {
      $hold_time = $item->getTargetTime()+$item->getHoldTime();
      $class = ($time > $hold_time) ? "success" : "warning";
      $hold_time = '<span class="label label-sm label-' . $class . '">' . date("d/m/Y H:i", $hold_time) . '</span>';
    }

    $records["data"][] = array(
      $item->getId(),
      $item->getOid(),
      $item->getCreated(true),
      '<span class="label label-sm label-'.$item->getStatusAlias().'">'.$item->getStatusLabel().'</span>',
      "<i class='flag flag-{$item->getCountryCode()}'></i> {$item->getAmount()}&nbsp;{$item->getCurrency()}",
      "{$commission}&nbsp;{$item->getCurrency()}",
      $item->getOwner()->getLogin(),
      $hold_time,
      $item->getSource(),
      "<a href='/admin/orders/view/{$item->getId()}' class='btn btn-xs btn-default'>Просмотр</a>",
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>