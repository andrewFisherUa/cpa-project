<?php  

  $filter = new Filter;
  $uid = $filter->sanitize($_REQUEST['params']['uid'], "int!");

  $query = "SELECT a.*, u.login FROM user_audit as a inner join users as u on a.user_id = u.user_id";

  $w = [];

  if ( !in_array(User::get_current_user_id(), [20, 69]) ) {
    $w[] = "a.user_id NOT IN (20, 69)";
  }

  if (!empty($_REQUEST['params']['user_id'])) {
    $temp = $filter->sanitize($_REQUEST['params']['user_id'], "int");
    $w[] = "a.user_id =" . $temp;
  }

  if (!empty($_REQUEST['params']['login'])) {
    $temp = $filter->sanitize($_REQUEST['params']['login'], ["string", "striptags"]);
    $w[] = "u.login LIKE '%" . $temp . "%'";
  }

  if (!empty($_REQUEST['params']['ip'])) {
    $temp = $filter->sanitize($_REQUEST['params']['ip'], ["string", "striptags"]);
    $w[] = "a.ip like '%".$temp."%'";
  }

  if (!empty($_REQUEST['params']['country_name'])) {
    $temp = $filter->sanitize($_REQUEST['params']['country_name'], ["string", "striptags"]);
    $w[] = "a.country_name = '".$temp."'";
  }

  if (!empty($w)) {
    $query .= " WHERE " . implode(" AND ", $w);
  }

  $iTotalRecords = $GLOBALS['DB']->query($query)->rowCount();

  $query .=  " ORDER BY a.created DESC";

  $query .= " LIMIT " . $_REQUEST['start'] . "," . $_REQUEST['length'];
  $stmt = $GLOBALS['DB']->query($query);

  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $records = array();
  $records["data"] = array();

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    $b = getBrowser($row["user_agent"]);

    $records["data"][] = [
      "<span data-status='". $row['status'] ."'>" . $row['user_id'] . "</span>",
      $row['login'],
      $row["ip"],      
      '<span class="flag flag-'.$row['country_code'].'"></span> '. $row['country_name'],
      date("d.m.Y H:i", $row['created']),
      '<a href="javascript:;" class="btn btn-sm btn-outline green" data-ip="' . $row["ip"] . '" data-user="' . $row['user_id'] . '"><i class="fa fa-search"></i></a>'      
    ];

  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>