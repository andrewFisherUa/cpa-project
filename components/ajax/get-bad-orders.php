<?php

if ( !User::isAdmin() ) {
  die();
}

$raw_data = $_REQUEST['filter_data'];

$filter = new Filter;

// Интервал времени
$range = [
  "from" => $filter->sanitize($raw_data['range']["from"], ["string", "striptags"]),
  "to" => $filter->sanitize($raw_data['range']["to"], ["string", "striptags"]),
];

$data = explode("-", $range['from']);
$range['from'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]);

$data = explode("-", $range['to']);
$range['to'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]) + 86400;

// фильтры
$params = [
  "offer" => $filter->sanitize($raw_data['filters']['offer'], "int"),
  "stream" => $filter->sanitize($raw_data['filters']['stream'], "int"),
  "landing" => $filter->sanitize($raw_data['filters']['landing'], "int"),
  "blog" => $filter->sanitize($raw_data['filters']['blog'], "int"),
  "source" => $filter->sanitize($raw_data['filters']['source'], "int"),
  "webmaster" => $filter->sanitize($raw_data['filters']['webmaster'], "int"),
  "status_problem" => TRUE,
  "subid" => []
];

if (array_key_exists('status', $raw_data['filters']) && $raw_data['filters']['status'] != -1) {
  $params['status'] = $filter->sanitize($raw_data['filters']['status'], ["string", "striptags"]); 
}

if (!empty($raw_data['filters']['status2_name']) && $raw_data['filters']['status2_name'] != -1) {
  $params['status2_name'] = $filter->sanitize($raw_data['filters']['status2_name'], ["string", "striptags"]); 
}

if (!empty($raw_data['filters']['subid'])) {
  foreach ($raw_data['filters']['subid'] as $name=>$values) {
    $name = $filter->sanitize($name, ["string", "striptags"]);

    $quoted = [];
    foreach ($values as $v) {
      $quoted[] = "'" . $filter->sanitize($v, ["string", "striptags"]) . "'";
    }

    $params["subid"][$name] = $quoted;
  }
}

$stat = new Order_Statistics(0, $range);
$stat->addFilters($params);
$stat->setOrderBy("status_problem_time");

$iDisplayStart = $filter->sanitize($_REQUEST['start'], "int");
$iDisplayLength = $filter->sanitize($_REQUEST['length'], "int");

$page = $iDisplayStart / $iDisplayLength;
$items = $stat->fetch($page, $iDisplayLength);

$iTotalRecords = $stat->getOrdersCount();
$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
$sEcho = intval($_REQUEST['draw']);

$records = array();
$records["data"] = array();

$isAdmin = User::isAdmin();

$users = [];

for($i = 0; $i < count($items); $i++) {

  $item = $items[$i];

  if ($item['status2_name'] == "На модерации") {
    $status2 = '<span class="label label-sm label-moderation" data-toggle="tooltip" data-placement="left" title="Предварительно подтвержденный заказ">' . $item['status2_name'] . '</span>';
  } else {
    $status2 = $item['status2_name'];
  }

  $records["data"][] = array(
    "<a href='/admin/orders/view/".$item['id']."' target='_blank'>" . $item['id'] . "<a>",
    $item['oid'],
    $item['status_problem_time'],
    "<a href='/admin/offers/view/".$item['offer_id']."'>" . $item['offer_name'] . "</a>",
    '<span class="label label-sm label-'.$item['status_class'].'">'.$item['status_name'].'</span>',
    '<span class="flag flag-' . $item['country_code'] . '"></span>',
    $status2,
    $item["user_id"] . ": " . get_username($item["user_id"]),
    '<span class="btn btn-sm btn-outline blue" data-action="show-log" data-id="' . $item['oid'] . '">Показать лог</span>',
  );
}

$records["draw"] = $sEcho;
$records["recordsTotal"] = $iTotalRecords;
$records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

function get_username($id) {
  global $users;

  if (array_key_exists($id, $users)) {
    return $users[$id];
  }

  $query = "SELECT login FROM users WHERE user_id = ?";
  $stmt = $GLOBALS["DB"]->prepare($query);
  $stmt->execute([$id]);
  $username = $stmt->fetchColumn();
  $users[$id] = $username;

  return $username;
}

?>