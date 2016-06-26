<?php


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

if (empty($_REQUEST['filter_data']['uid'])) {
  $user_id = 0;
} else {
  $user_id = $filter->sanitize($_REQUEST['filter_data']['uid'], "int");
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
  "webmaster" => $filter->sanitize($_REQUEST['filter_data']['filters']['webmaster'], "int"),
  "offer" => $filter->sanitize($_REQUEST['filter_data']['filters']['offer'], "int"),
  "stream" => $filter->sanitize($_REQUEST['filter_data']['filters']['stream'], "int"),
  "landing" => $filter->sanitize($_REQUEST['filter_data']['filters']['landing'], "int"),
  "blog" => $filter->sanitize($_REQUEST['filter_data']['filters']['blog'], "int"),
  "source" => $filter->sanitize($_REQUEST['filter_data']['filters']['source'], "int"),
  "country" => $filter->sanitize($_REQUEST['filter_data']['filters']['country'], ["string", "striptags"]),
  "order_type" => $filter->sanitize($_REQUEST['filter_data']['filters']['order_type'], ["string", "striptags"]),
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

if (in_array($group_by, ["subid1", "subid2", "subid3", "subid4", "subid5"])) {
  if (isset($params['subid'][$group_by])) {
    $ids = $params['subid'][$group_by];
    unset($params['subid'][$group_by]);
  }
} else {
  if (isset($params[$group_by])) {
    $ids = $params[$group_by];
    unset($params[$group_by]);
  }
}

$stat = Stat::get($user_id, $group_by, $ids);
$stat->setRange($range);
$stat->applyFilters($params);
$values = $stat->fetch();

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

for($i = $iDisplayStart, $k=0; $i < $end; $i++, $k++) {

  $item = $items[$i];

  if ($group_by == "referer") {
    $item['name'] =  "<a href='http://".$item['name']."' target='_blank'>" . $item['name'] . "</a>"; //Возвращаем кодировку в utf-8
  }

  if ($group_by == "date") {
    $item['name'] = date("d-m-Y", $item['name']);
  }

  $records["data"][$k] = [
     $item['name'],
    "<span class='f'>" . $item['all'] . "</span>",
    "<span class='f'>" . $item['unique'] . "</span>",
    "<span class='f'>" . $item['count']['approved'] . "</span>",
    "<span class='f'>" . $item['count']['waiting'] . "</span>",
    "<span class='f'>" . $item['count']['canceled'] . "</span>",
    "<span class='f'>" . $item['count']['total'] . "</span>",
    "<span class='f'>" . $item['count']['trash'] . "</span>",
  ];

  if ($user_id == 0) {
    $records["data"][$k][] = $item['count']['delivered'];
    $records["data"][$k][] = $item['delivered_percent'];
  }

   
  $records["data"][$k][] = $item['k']['epc'];
  $records["data"][$k][] = $item['k']['crs'];
  $records["data"][$k][] = $item['k']['approve'];
  $records["data"][$k][] = "<span class='approved f'>" . $item['amount']['approved'] . "</span>";
  $records["data"][$k][] = "<span class='f'>" . $item['amount']['waiting'] . "</span>";
  $records["data"][$k][] = "<span class='canceled f'>" . $item['amount']['canceled'] . "</span>";

  $totals['all'] += $item['all'];
  $totals['unique'] += $item['unique'];
  $totals['count']['approved'] += $item['count']['approved'];
  $totals['count']['waiting'] += $item['count']['waiting'];
  $totals['count']['canceled'] += $item['count']['canceled'];
  $totals['count']['trash'] += $item['count']['trash'];
  $totals['count']['total'] += $item['count']['total'];

  if ($user_id == 0) {
    $totals['count']['delivered'] += $item['count']['delivered'];
  }

  $totals['k']['approve'] += $item['k']['approve'];
  $totals['amount']['approved'] += $item['amount']['approved'];
  $totals['amount']['waiting'] += $item['amount']['waiting'];
  $totals['amount']['canceled'] += $item['amount']['canceled'];

}

if ($user_id == 0) {
  $p2 = round(($totals['count']['delivered'] / $totals['count']['approved'])*100);
}

$epc = ( $totals['unique'] == 0 ) ? 0 : $totals['amount']['approved'] / $totals['unique'];
$crs = ( $totals['unique'] == 0 ) ? 0 : ($totals['count']['total'] / $totals['unique'])*100;
$approve = ( $totals['count']['total'] == 0 ) ? 0 : ($totals['count']['approved'] / $totals['count']['total']) * 100;

$epc = round($epc, 2);
$crs = round($crs, 2);
$approve = ceil($approve);

$records["data"][$k] = [
  "Всего: ",
  "<span class='f'>" . $totals['all'] . "</span>",
  "<span class='f'>" . $totals['unique'] . "</span>",
  "<span class='f'>" . $totals['count']['approved'] . "</span>",
  "<span class='f'>" . $totals['count']['waiting'] . "</span>",
  "<span class='f'>" . $totals['count']['canceled'] . "</span>",
  "<span class='f'>" . $totals['count']['total'] . "</span>",
  "<span class='f'>" . $totals['count']['trash'] . "</span>"];


if ($user_id == 0) {
  $records["data"][$k][] = $totals['count']['delivered'];
  $records["data"][$k][] = $p2;
}
  
$records["data"][$k][] = $epc;
$records["data"][$k][] = $crs;
$records["data"][$k][] = $approve;
$records["data"][$k][] = "<span class='approved f'>" . $totals['amount']['approved'] . "</span>";
$records["data"][$k][] = "<span class='f'>" . $totals['amount']['waiting'] . "</span>";
$records["data"][$k][] = "<span class='canceled f'>" . $totals['amount']['canceled'] . "</span>";

$records["draw"] = $sEcho;
$records["recordsTotal"] = $iTotalRecords;
$records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>