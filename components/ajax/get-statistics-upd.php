<?php

if ( User::get_current_user_id() <= 0 ) {
  die();
}

function sortBy($a, $field = "name", $order = "asc") {

  $a = array_values($a);

  $length = count($a);

  for ($j = 0; $j < $length-1; $j++) {
    for ($i = 0; $i < $length-$j-1; $i++) {
      if ($order == "asc" && $a[$i][$field] > $a[$i+1][$field]) {
         $b = $a[$i]; //change for elements
         $a[$i] = $a[$i+1];
         $a[$i+1] = $b;
      }

      if ($order == "desc" && $a[$i][$field] < $a[$i+1][$field]) {
         $b = $a[$i]; //change for elements
         $a[$i] = $a[$i+1];
         $a[$i+1] = $b;
      }
    }
  }

  return $a;
}

$filter = new Filter;

$user_id = User::get_current_user_id();

if ($user_id <= 0) {
  die();
}

// Интервал времени
$range = [
  "from" => $filter->sanitize($_REQUEST['filter_data']['range']["from"], ["string", "striptags"]),
  "to" => $filter->sanitize($_REQUEST['filter_data']['range']["to"], ["string", "striptags"]),
];

$data = explode("-", $range['from']);
$range['from'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]);

$data = explode("-", $range['to']);
$range['to'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]) + 86400;

// группировка 
$group_by = $filter->sanitize($_REQUEST['filter_data']['group_by'], ["string", "striptags"]);

// фильтры
$params = [
  "offer" => $filter->sanitize($_REQUEST['filter_data']['filters']['offer'], "int"),
  "stream" => $filter->sanitize($_REQUEST['filter_data']['filters']['stream'], "int"),
  "landing" => $filter->sanitize($_REQUEST['filter_data']['filters']['landing'], "int"),
  "blog" => $filter->sanitize($_REQUEST['filter_data']['filters']['blog'], "int"),
  "source" => $filter->sanitize($_REQUEST['filter_data']['filters']['source'], "int"),
  "subid" => []
];

if (!empty($_REQUEST['filter_data']['filters']['subid'])) {
  foreach ($_REQUEST['filter_data']['filters']['subid'] as $name=>$values) {
    $name = $filter->sanitize($name, ["string", "striptags"]);
    $values = $filter->sanitize($values, ["string", "striptags"]);

    $params["subid"][$name] = $values;
  }
}

$ids = [];

if (isset($params[$group_by])) {
  $ids = $params[$group_by];
  unset($params[$group_by]);
}

if (($group_by == "subid1" || $group_by == "subid2" || $group_by == "subid3") && isset($params['subid'][$group_by])){
  $ids = $params['subid'][$group_by];
  unset($params['subid'][$group_by]);
}

$stat = Stat::get($user_id, $group_by, $ids);
$stat->setRange($range);
$stat->applyFilters($params);
$values = $stat->fetch();

// сортировка
$sort_by = $filter->sanitize($_REQUEST['filter_data']['sort_by'], ["string", "striptags"]);
$sort_order = $filter->sanitize($_REQUEST['filter_data']['sort_order'], ["string", "striptags"]);

$items = sortBy($values, $sort_by, $sort_order);

$totals = [
  "all" => 0,
  "unique" => 0,
  "count" => [
    "approved" => 0,
    "waiting" => 0,
    "canceled" => 0,
    "trash" => 0,
  ],
  "k" => [
    "approve" => 0, 
  ],
  "amount" => [
    "approved" => 0,
    "waiting" => 0,
    "canceled" => 0,
  ]
];

$iTotalRecords = count($items);
$iDisplayLength = intval($_REQUEST['length']);
$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
$iDisplayStart = intval($_REQUEST['start']);
$sEcho = intval($_REQUEST['draw']);

$records = array();
$records["data"] = array();

$end = $iDisplayStart + $iDisplayLength;
$end = $end > $iTotalRecords ? $iTotalRecords : $end;

for($i = $iDisplayStart; $i < $end; $i++) {

  $item = $items[$i];
  if ($group_by == "date") {
    $item['name'] = date("d-m-Y", $item['name']);
  }

  $records["data"][] = array(
    $item['name'],
    $item['all'],
    $item['unique'],
    $item['count']['approved'],
    $item['count']['waiting'],
    $item['count']['canceled'],
    $item['count']['total'],
    $item['count']['trash'],
    $item['k']['epc'],
    $item['k']['crs'],
    $item['k']['approve'],
    "<span class='approved'>" . $item['amount']['approved'] . "</span>",
    $item['amount']['waiting'],
    "<span class='canceled'>" . $item['amount']['canceled'] . "</span>",
  );

  $totals['all'] += $item['all'];
  $totals['unique'] += $item['unique'];
  $totals['count']['approved'] += $item['count']['approved'];
  $totals['count']['waiting'] += $item['count']['waiting'];
  $totals['count']['canceled'] += $item['count']['canceled'];
  $totals['count']['trash'] += $item['count']['trash'];
  $totals['count']['total'] += $item['count']['total'];
  $totals['k']['approve'] += $item['k']['approve'];
  $totals['amount']['approved'] += $item['amount']['approved'];
  $totals['amount']['waiting'] += $item['amount']['waiting'];
  $totals['amount']['canceled'] += $item['amount']['canceled'];

}

$epc = ( $totals['unique'] == 0 ) ? 0 : $totals['amount']['approved'] / $totals['unique'];
$crs = ( $totals['unique'] == 0 ) ? 0 : ($totals['count']['total'] / $totals['unique'])*100;
$approve = ( $totals['count']['total'] == 0 ) ? 0 : ($totals['count']['approved'] / $totals['count']['total']) * 100;

$epc = round($epc, 2);
$crs = round($crs, 2);
$approve = ceil($approve);

$records["data"][] = [
  "Всего: ",
  $totals['all'],
  $totals['unique'],
  $totals['count']['approved'],
  $totals['count']['waiting'],
  $totals['count']['canceled'],
  $totals['count']['total'],
  $totals['count']['trash'],
  $epc,
  $crs,
  $approve,
  "<span class='approved'>" . $totals['amount']['approved'] . "</span>",
  $totals['amount']['waiting'],
  "<span class='canceled'>" . $totals['amount']['canceled'] . "</span>",
];

$records["draw"] = $sEcho;
$records["recordsTotal"] = $iTotalRecords;
$records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>