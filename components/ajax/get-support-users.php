<?php

  $class = [ 0 => ["class"=>"warning", "label" => "На модерации" ],
             1 => ["class"=>"danger", "label" => "Не найден" ],
             2 => ["class"=>"success", "label" => "В ожидании" ],
             3 => ["class"=>"green", "label"=>"Добавлен"]
           ];

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

  if ( array_key_exists("skype_status", $request) && $request['skype_status'] != "-1") {
    $params[] = "p.skype_status = " . $filter->sanitize($request['skype_status'], "int");
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

  $params[] = "u.status = 2";
  $params[] = "ur.role_id = 2";

  $query = "SELECT DISTINCT u.country_code, u.country_name, u.user_id, u.login, u.created, p.skype, uo.user_id as `online`, p.skype_status
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

  while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $uid = $item['user_id'];

    $country = "";
    if (!empty($item['country_name'])) {
      $country = $item['country_name'] . "&nbsp;<span class='flag flag-" . $item['country_code'] . "'></span>";
    }

    $onlineStatus = ( $item['online'] == $item['user_id'] ) ? ' <span class="badge badge-roundless" style="background: #7ccd2d">online</span>' : '';

    $statusText = "<span class='label label-sm label-".$class[$item['skype_status']]['class']."'>";
    $statusText .= "<a href='javascript:;' id='status" . $uid . "' class='status' data-subject='". $uid ."' data-type='select' data-pk='1' data-value='" . $item['skype_status'] . "' data-original-title='Статус'>";
    $statusText .= $class[$item['skype_status']]['label'];
    $statusText .= "</a></span>";

    $records["data"][] = array(
      $uid,
      $item['login'] . $onlineStatus,
      $country,
      date("d/m/y H:i", $item['created']),
      $statusText,
      $item['skype'],
      '<a href="/admin/user/' . $uid . '" class="btn btn-sm btn-outline blue">Подробности</a>'
      );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>