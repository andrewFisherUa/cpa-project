<?php

  $isAdmin = User::isAdmin();
  $filter = new Filter;

  if ($isAdmin) {
    $query = "SELECT a.id, a.`key`, a.name, a.offer_id, a.changed, g.name AS offer_name, u.login
              FROM api_streams AS a INNER JOIN goods AS g ON a.offer_id = g.id
                                    INNER JOIN users AS u ON u.user_id = a.user_id";    
  } else {
    $query = "SELECT a.id, a.`key`, a.name, a.offer_id, a.changed, a.user_id, g.name AS offer_name
              FROM api_streams AS a INNER JOIN goods AS g ON a.offer_id = g.id";    
  }

  $params = $_REQUEST['params'];
  $where = [];

  if (!$isAdmin) {
    $where[] = "a.user_id = " . User::get_current_user_id();
  }

  if (array_key_exists("id", $params)) {
    $temp = $filter->sanitize($params["id"], "int!");
    if ($temp > 0) {
      $where[] = "a.id = " . $temp;
    }
  }

  if (array_key_exists("offer_id", $params)) {
    $temp = $filter->sanitize($params["offer_id"], "int!");
    if ($temp > 0) {
      $where[] = "a.offer_id = " . $temp;
    }
  }

  if (array_key_exists("user_id", $params)) {
    $temp = $filter->sanitize($params["user_id"], "int!");
    if ($temp > 0) {
      $where[] = "a.user_id = " . $temp;
    }
  }

  if (array_key_exists("name", $params)) {
    $temp = $filter->sanitize($params["name"], ["string", "striptags"]);
    if (!empty($temp)) {
      $where[] = "a.name LIKE '%" . $temp . "%'";
    }
  }

  if (array_key_exists("key", $params)) {
    $temp = $filter->sanitize($params["key"], ["string", "striptags"]);
    if (!empty($temp)) {
      $where[] = "a.key LIKE '%" . $temp . "%'";
    }
  }

  if (array_key_exists("changed_from", $params) && !empty($params["changed_from"])) {
    $temp = explode("/", $filter->sanitize($params["changed_from"], ["string", "striptags"]));    
    $where[] = "a.changed > " . mktime(0, 0, 0, $temp[1], $temp[0], $temp[2]);
  }

  if (array_key_exists("changed_to", $params) && !empty($params["changed_to"])) {
    $temp = explode("/", $filter->sanitize($params["changed_to"], ["string", "striptags"]));    
    $where[] = "a.changed < " . mktime(0, 0, 0, $temp[1], $temp[0], $temp[2]);
  }

  if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
  }

  $iTotalRecords = $GLOBALS['DB']->query($query)->rowCount();
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);

  $query .= " ORDER BY a.changed DESC";
  $query .= " LIMIT {$iDisplayStart}, {$iDisplayLength}";

  $stmt = $GLOBALS['DB']->query($query); 

  $records = [
    "data" => [],
    "draw" => intval($_REQUEST['draw']),
    "recordsTotal" => $iTotalRecords,
    "recordsFiltered" => $iTotalRecords
  ];  

  while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $actions = '<div style="display:inline-block; position: relative;"><a href="#" data-key="' . $a['key'] . '" class="btn btn-sm btn-outline green copy-key" title="Копировать ключ буфер обмена"><i class="fa fa-copy"></i></a></div>'; 
    $actions .= '<a href="/admin/api/streams/' . $a['id'].'" class="btn btn-sm btn-outline blue" title="Редактировать"><i class="fa fa-edit"></i></a>';
    $actions .= '<a href="javascript:;" class="btn btn-sm btn-outline remove-item red" title="Удалить" data-id="' . $a['id'] . '"><i class="fa fa-trash"></i></a>';

    $temp = [
      $a['id'],
      $a['name'], 
      date("d/m/Y H:i", $a['changed']),
      "<a href='/admin/offers/view/" . $a['offer_id'] . "'>" . $a['offer_id'] . ": " . $a['offer_name'] . "</a>",
    ];

    if ($isAdmin) {
      $temp[] = $a['login'];
    }

    $temp[] = "<div style='max-width:100%;word-break:break-all'>" . $a['key'] . "</div>";
    $temp[] = $actions;

    $records["data"][] = $temp;
  }

 

echo json_encode($records);

?>