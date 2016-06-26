<?php

$filter = new Filter;

$user_id = $filter->sanitize($_POST['user_id'], "int");

$response = [];

// Интервал времени
$range = [
  "from" => $filter->sanitize($_REQUEST['range']["from"], ["string", "striptags"]),
  "to" => $filter->sanitize($_REQUEST['range']["to"], ["string", "striptags"]),
];

$data = explode("-", $range['from']);
$range['from'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]);

$data = explode("-", $range['to']);
$range['to'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]) + 86400;

$stat = Stat::get($user_id, "date");
$stat->setRange($range);
$values = $stat->fetch();

$i=0;

foreach ($values as $v) {
    $d = (int)$v['name'] * 1000;

    $response["all"][$i] = [$d, $v["all"]];
    $response["unique"][$i] = [$d, $v["unique"]];
    $response["crs"][$i] = [$d, $v["k"]["crs"]];
    $response["epc"][$i] = [$d, $v["k"]["epc"]];
    $response["approve"][$i] = [$d, $v["k"]["approve"]];
    $response["waiting"][$i] = [$d, $v["count"]["waiting"]];
    $response["approved"][$i] = [$d, $v["count"]["approved"]];
    $response["canceled"][$i] = [$d, $v["count"]["canceled"]];

    $i++;
}

echo json_encode($response);

?>