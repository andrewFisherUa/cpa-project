<?php

  if (!User::isAdmin()) {
    die();
  }

  $class = [ "moderation" => ["class"=>"info", "label" => "Модерация" ],
             "accepted" => ["class"=>"success", "label" => "Одобрен" ],
             "refused" => ["class"=>"danger", "label" => "Отклонен" ]];

  $query = "SELECT a.*, u.login
            FROM api_requests AS a INNER JOIN users AS u ON a.user_id = u.user_id";

  $params = $_REQUEST['params'];
  $where = [];

  $filter = new Filter;

  if (!empty($params["id"])) {
    $temp = $filter->sanitize($params["id"], "int!");
    if ($temp > 0) {
      $where[] = "a.id = " . $temp;
    }
  }

  if (!empty($params["user_id"])) {
    $temp = $filter->sanitize($params["user_id"], "int!");
    if ($temp > 0) {
      $where[] = "a.user_id = " . $temp;
    }
  }

  if (!empty($params["ticket_id"])) {
    $temp = $filter->sanitize($params["ticket_id"], "int!");
    $where[] = "a.ticket_id = " . $temp;
  }

  if (!empty($params["status"]) && $params["status"] != -1) {
    $temp = $filter->sanitize($params["status"], ["string", "striptags"]);
    $where[] = "a.status = '" . $temp . "'";
  }

  if (!empty($params["changed_from"])) {
    $temp = $filter->sanitize($params["changed_from"], ["string", "striptags"]);
    $where[] = "a.changed > " . strtotime($temp);
  }

  if (!empty($params["changed_to"])) {
    $temp = $filter->sanitize($params["changed_to"], ["string", "striptags"]);
    $where[] = "a.changed < " . strtotime($temp);
  }

  if (!empty($params["created_from"])) {
    $temp = $filter->sanitize($params["created_from"], ["string", "striptags"]);
    $where[] = "a.created > " . strtotime($temp);
  }

  if (!empty($params["created_to"])) {
    $temp = $filter->sanitize($params["created_to"], ["string", "striptags"]);
    $where[] = "a.created < " . strtotime($temp);
  }

  if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
  }

  $iTotalRecords = $GLOBALS['DB']->query($query)->rowCount();
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);

  $query .= " ORDER BY a.created DESC";
  $query .= " LIMIT {$iDisplayStart}, {$iDisplayLength}";

  $stmt = $GLOBALS['DB']->query($query); 

  $records = [
    "data" => [],
    "draw" => intval($_REQUEST['draw']),
    "recordsTotal" => $iTotalRecords,
    "recordsFiltered" => $iTotalRecords
  ];  

  while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $changed = ($item['changed'] > 0) ? date("d/m/Y H:i", $item['changed']) : "";
    $status = "<span class='label label-sm label-".$class[$item['status']]['class']."'>";
    $status .= "<a href='javascript:;' id='status" . $item["user_id"] . "' class='status' data-subject='". $item["user_id"] ."' data-type='select' data-pk='1' data-value='" . $item['status'] . "' data-original-title='Статус пользователя'>" . $class[$item['status']]['label'] . "</a>";
    $status .= "</span>";

    $records["data"][] = [
      $item["id"],
      $item["user_id"] . ": " . $item["login"],
      $status,
      date("d/m/Y H:i", $item['created']),
      $changed,
      "<a href='/admin/tickets/".$item["ticket_id"]."'>Перейти</a>",
      ""
    ];
  }

echo json_encode($records);

?>