<?php

  $class = [ 0 => ["class"=>"warning", "label" => "Не подтвержден" ],
             1 => ["class"=>"info", "label" => "На модерации" ],
             2 => ["class"=>"success", "label" => "Активирован" ],
             3 => ["class"=>"danger", "label" => "Заблокирован" ]];

  $filter = new Filter;

  $params = [];
  $request = $_REQUEST['f'];

  if ( array_key_exists("login", $request) && !empty($request['login'])) {
    $params[] = "u.login LIKE '%" . $filter->sanitize($request['login'], ["striptags", "string"]) . "%'";
  }

  if ( array_key_exists("skype", $request) && !empty($request['skype'])) {
    $params[] = "p.skype LIKE '%" . $filter->sanitize($request['skype'], ["striptags", "string"]) . "%'";
  }

  if ( array_key_exists("country", $request) && $request['country'] != "-1") {
    $params[] = "u.country_name = '" . $filter->sanitize($request['country'], ["striptags", "string"]) . "'";
  }

  if ( array_key_exists("id", $request) && !empty($request['id'])) {
    $params[] = "u.user_id = " . $filter->sanitize($request['id'], "int");
  }

  if ( array_key_exists("status", $request) && $request['status'] != "-1") {
    $params[] = "u.status = " . $filter->sanitize($request['status'], "int");
  }

  if ( array_key_exists("role", $request) && $request['role'] != "-1") {
    $params[] = "ur.role_id = " . $filter->sanitize($request['role'], "int");
  }

  if (array_key_exists("date_from", $request) && !empty($request['date_from'])){
    $temp = $filter->sanitize($request['date_from'], ["string", "striptags"]);
    $data = explode("/", $temp);
    $params[] = "u.created > " . mktime(0, 0, 0, $data[1], $data[0], $data[2]);
  }

  if (array_key_exists("date_to", $request) && !empty($request['date_to'])){
    $temp = $filter->sanitize($request['date_to'], ["string", "striptags"]);
    $data = explode("/", $temp);
    $params[] = "u.created < " . mktime(0, 0, 0, $data[1], $data[0], $data[2]);
  }

  $query = "SELECT DISTINCT u.country_code, u.country_name, u.user_id, u.login, u.status, u.created, p.skype, uo.user_id as `online`
            FROM users AS u LEFT JOIN users_online AS uo ON u.user_id = uo.user_id
                            LEFT JOIN partners AS p ON u.user_id = p.id
                            INNER JOIN user_role as ur ON u.user_id = ur.user_id";

  if (!empty($params)) {
    $query .= " WHERE " . implode( " AND ", $params );
  }

  $iTotalRecords = $GLOBALS['DB']->query($query)->rowCount();
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $query .= " ORDER BY `online` DESC, u.user_id DESC ";
  $query .= " LIMIT {$iDisplayStart}, {$iDisplayLength}";

  $stmt = $GLOBALS['DB']->query($query); 

  $records = [
    "data" => []
  ];

  $role_colors = [
    "advertiser" => "#95A5A6",
    "admin" => "#D8334A",
    "operator" => "#8E44AD",
    "webmaster" => "#006a94",
    "support" => "#D770AD"
  ];

  while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $uid = $item['user_id'];

    $country = "";
    if (!empty($item['country_name'])) {
      $country = $item['country_name'] . "&nbsp;<span class='flag flag-" . $item['country_code'] . "'></span>";
    }

    // Роли
    $roles = [];
    foreach (Privileged_User::get_roles($uid) as $a) { 
      $is_webmaster = $a['role_name'] == "webmaster";
      $roles[] = "<span class='badge badge-roundless' style='background-color:".$role_colors[$a['role_name']]."'>" . Role::getAlias($a['role_name']) . "</span>";
    }

    $isBoss = User::isBoss($uid);

    $onlineStatus = ( $item['online'] == $item['user_id'] ) ? ' <span class="badge badge-roundless" style="background: #7ccd2d">online</span>' : '';
    $status = $item['status'];

    $statusText = "<span class='label label-sm label-".$class[$status]['class']."'>";
    if (!$isBoss) {
      $statusText .= "<a href='javascript:;' id='status" . $uid . "' class='status' data-subject='". $uid ."' data-type='select' data-pk='1' data-value='" . $status . "' data-original-title='Статус пользователя'>";
    }

    $statusText .= $class[$status]['label'];

    if (!$isBoss) {
      $statusText .= "</a>";
    }

    $statusText .= "</span>";

    $actions = '';

    //
    if (User::isAdmin() && User::get_current_user_id() == $uid || !User::isAdmin($uid)) {
      $actions .= '<a href="javascript:;" class="show-profile btn btn-sm btn-outline blue" data-user="'.$uid.'"><i class="icon-note"></i></a>';
    }

    if (!User::isAdmin($uid) ) {
      $actions .= '<a href="javascript:;" class="login-as btn btn-sm btn-outline blue" data-user="'.$uid.'"><i class="icon-login"></i></a>';
    }

    if ($is_webmaster) {
      $actions .= '<a href="/admin/user/' . $uid . '" class="btn btn-sm btn-outline blue"><i class="icon-user"></i></a>';
    }

    $records["data"][] = array(
      $uid,
      $item['login'] . $onlineStatus,
      implode(", ", $roles),
      $country,
      date("d/m/y H:i", $item['created']),
      $statusText,
      $item['skype'],
      getTotalReferalsCount($uid),
      $actions
      );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>