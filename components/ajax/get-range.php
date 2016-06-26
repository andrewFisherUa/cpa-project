<?php

$response = [];
$range = [];

$date = new DateTime("now", new DateTimeZone('Europe/Kiev'));
$date->setTime(0,0);

switch ($_POST['range']) {
  case "yesterday" : $range['from'] = $date->sub(new DateInterval('P1D'))->format('U');
                     $range['to'] = $range['from'];
                     break;
  case "today" : $range['from'] = $date->format('U');
                 $range['to'] = $range['from'];
                 break;
  case "week"  : $range['from'] = $date->modify('last Monday')->format('U');
                 $range['to'] = $date->add(new DateInterval('P6D'))->format('U');
                 break;
  case "month" : $range['from'] = $date->modify('first day of this month')->format('U');
                 $range['to'] = $date->modify('last day of this month')->format('U');
                 break;
}

$response['range']['from'] = date('d-m-Y', $range['from']);
$response['range']['to'] = date('d-m-Y', $range['to']);

echo json_encode($response);

?>