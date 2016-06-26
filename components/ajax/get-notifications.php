<?php

  $filter = new Filter;

  if (!empty($raw_params['date_from'])){
    $params['date_from'] = $filter->sanitize($raw_params['date_from'], ["string", "striptags"]);
    $data = explode("/", $params['date_from']);
    $w[] = "a.`timestamp` > " . mktime(0, 0, 0, $data[1], $data[0], $data[2]);
  }

  if(!empty($raw_params['date_to'])){
    $params['date_to'] = $filter->sanitize($raw_params['date_to'], ["string", "striptags"]);
    $data = explode("/", $params['date_to']);
    $w[] = "a.`timestamp` < " . mktime(0, 0, 0, $data[1], $data[0], $data[2]);
  }

  $query = "SELECT *, notification_id AS id FROM notifications";
  $iTotalRecords = $GLOBALS['DB']->query($query)->rowCount();

  $query .= " ORDER BY created DESC LIMIT " . $_REQUEST['start'] . "," . $_REQUEST['length'];
  $stmt = $GLOBALS['DB']->query($query);

  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);

  $data = [];

  while ($a = $stmt->fetch(PDO::FETCH_ASSOC)){
    $n = new Notification($a);


    $data[] = array(
      $n->getId(),
      date("d.m.Y H:i", $n->getCreated()),
      $n->getSection(),
      $n->getMessage(),
      $n->getType(),
      $n->getStatus(),
      '<span class="btn btn-sm btn-outline blue" data-action="show-details" data-id="' . $n->getId() . '">Детали</span>',
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