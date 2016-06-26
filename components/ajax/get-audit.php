<?php

  $filter = new Filter;

  $users = [];
  $stmt = $GLOBALS['DB']->query("SELECT user_id as id, login FROM users");
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $users[$row['id']] = $row['login'];
  }

  $raw_params = $_REQUEST['params'];
  $w = []; $params = [];

  if (!empty($raw_params["pages"]["list"])) {
    $list = $filter->sanitize($raw_params["pages"]["list"], ["string", "striptags"]);
    $list = explode("|", $list);
    if (!empty($list)) {
      $l = [];
      $action = $filter->sanitize($raw_params["pages"]["action"], ["string", "striptags"]);
      foreach ($list as $a) {
        if (strpos($a, '%') === false) {
          if ($action == "include") {
            $l[] = "a.action = '" . trim($a) . "'";  
          } else {
            $l[] = "a.action != '" . trim($a) . "'";  
          } 
        } else {
          if ($action == "include") {
            $l[] = "a.action LIKE '%" . trim($a) . "%'";
          } else {
            $l[] = "a.action NOT LIKE '%" . trim($a) . "%'";
          }
        }
      }

      if ($action == "include") {
        $w[] = "(" . implode(" OR ", $l) . ")";
      } else {
        $w[] = "(" . implode(" AND ", $l) . ")";
      }
    }
  }

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

  if (!empty($raw_params['ip'])) {
    $params['ip'] = $filter->sanitize($raw_params['ip'], ["string", "striptags"]);
    $w[] = "a.ip like '%".$params['ip']."%'";
  }

  if ($raw_params['show_important']) {
    $w[] = "(a.priority = 'high' OR a.priority = 'medium')";
  }

  if (!empty($raw_params['action'])) {
    $params['action'] = $filter->sanitize($raw_params['action'], ["string", "striptags"]);
    $w[] = "action like '%".$params['action']."%'";
  }

  if (!empty($raw_params['user_id'])) {
    $params['user_id'] = $filter->sanitize($raw_params['user_id'], "int");
    $w[] = "a.user_id =" . $params['user_id'];
  }

  if (!empty($raw_params['id'])) {
    $params['id'] = $filter->sanitize($raw_params['id'], "int");
    $w[] = "a.id =" . $params['id'];
  }

  if (!empty($params['admin_id'])) {
    $params['admin_id'] = $filter->sanitize($raw_params['admin_id'], "int");
    $w[] = "a.admin_id = " . $params['admin_id'];
  }

  if ( !in_array(User::get_current_user_id(), [20, 69]) ) {
    $w[] = "a.user_id NOT IN (20, 69) AND a.admin_id NOT IN (20, 69)";
  }

  $query = "SELECT DISTINCT a.*, d.aid FROM audit as a LEFT JOIN audit_details AS d ON a.id = d.aid";
  if (!empty($w)) {
    $query .= " WHERE " . implode(" AND ", $w);
  }

  $query .= " ORDER BY a.`timestamp` DESC";
  $iTotalRecords = $GLOBALS['DB']->query($query)->rowCount();

  $query .= " LIMIT " . $_REQUEST['start'] . "," . $_REQUEST['length'];
  $stmt = $GLOBALS['DB']->query($query);

  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $records = array();
  $records["data"] = array();

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $item = new Audit($row);

    $admin = ($item->getAdminId() > 0) ? $item->getAdminId() . ": " . $users[$item->getAdminId()] : "нет";    

    $details = "";

    if ($row['user_id'] > 0) {
      $details .= '<a href="/admin/user/'.$item->getUserId().'" class="btn btn-sm btn-outline purple"><i class="fa fa-user"></i></a>';
    }

    if ($row['aid'] > 0) {
      $details .= '<a href="javascript:;" class="show-details btn btn-sm btn-outline dark" data-aid="'.$item->getId().'"><i class="fa fa-search"></i></a>';
    }

    $records["data"][] = array(
      "<span class='".$item->getPriority()."-priority'>" . $item->getId() . "</span>",
      $item->getTimestamp(true),
      $item->getUserId() . ": " . $users[$item->getUserId()],
      $admin,
      $item->getAction(),
      "<span class='flag flag-" . strtolower(geoip_country_code_by_name($item->getIp())) . "'></span>&nbsp;" . $item->getIp(),
      $details
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>