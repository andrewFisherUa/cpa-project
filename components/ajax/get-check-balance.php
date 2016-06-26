<?php

function sortItems($a) {
  $length = count($a);
  for ($j = 0; $j < $length-1; $j++) {
    for ($i = 0; $i < $length-$j-1; $i++) {
      if ($a[$i]["diff"] < $a[$i+1]["diff"]) {
         $b = $a[$i]; //change for elements
         $a[$i] = $a[$i+1];
         $a[$i+1] = $b;
      }
    }
  }

  return $a;
}

$users = [297, 288, 337, 364, 346, 399, 365];

  //profit
  $profit = [];
  $query = "SELECT sum(t1.webmaster_commission) AS profit, t1.user_id, t2.currency_code as currency
            FROM orders as t1 INNER JOIN country as t2 ON t1.country_code = t2.code
            WHERE ((t1.status IN (1, 3)) OR (t1.status = 4 AND t1.target = 1)) AND t1.target_close = 1 AND user_id IN (297, 288, 337, 364, 346, 399, 365)
            GROUP BY t1.user_id, t1.country_code;";
  $stmt = $GLOBALS['DB']->query($query);
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['profit'] > 0) {
      $temp = Converter::getConvert($row['currency'], "RUB", $row['profit']);
      $profit[$row['user_id']] += $temp['amount'];
    }
  }

  // payment
  $payment = [];
  $query = "SELECT SUM(amount) as amount, user_id 
            FROM payments
            WHERE status = 'approved' AND user_id IN (297, 288, 337, 364, 346, 399, 365)
            GROUP BY user_id";
  $stmt = $GLOBALS['DB']->query($query);
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $payment[$row['user_id']] = $row['amount'];
  }

  // Webmasters
  $query = "SELECT t1.user_id as id, t1.login
            FROM users AS t1 INNER JOIN user_role AS t2 ON t1.user_id = t2.user_id
            WHERE t2.role_id = 2 AND t1.user_id IN (297, 288, 337, 364, 346, 399, 365) 
            GROUP BY t1.user_id";
  $stmt = $GLOBALS['DB']->query($query);

  $items = [];
  $i = 0;

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $items[$i]['id'] = $row['id'];
    $items[$i]['login'] = $row['login'];

    // balance
    $temp = new DefaultBalance($row['id']);
    $items[$i]['balance'] = $temp->getCurrent();
    $items[$i]['hold'] = round($temp->getHold());
    $items[$i]['ref'] = round($temp->getReferal());
    $items[$i]['canceled'] = round($temp->getCanceled());
    $items[$i]['payment'] = round($payment[$row['id']]);
    $items[$i]['profit'] = round($profit[$row['id']]);
    $items[$i]['diff'] = $items[$i]['profit'] - $items[$i]['balance'] - $items[$i]['hold'] - $items[$i]['payment'];
    $i++;
  }

  $items = sortItems($items);

  $iTotalRecords = count( $items );
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $records = array();
  $records["data"] = array();

  $end = $iDisplayStart + $iDisplayLength;
  $end = $end > $iTotalRecords ? $iTotalRecords : $end;

  for ($i = $iDisplayStart; $i < $end; $i++) {
    $item = $items[$i];

    $records["data"][] = array(
      $i+1,
      $item['id'],
      $item['login'],
      '<span class="money">' . $item['profit'] . '</span>',
      '<span class="money">' . $item['hold'] . '</span>',
      '<span class="money">' . $item['balance'] . '</span>',
      '<span class="money">' . $item['payment'] . '</span>',
      '<span class="money">' . $item['ref'] . '</span>',
      '<span class="money">' . $item['canceled'] . '</span>',
      '<span class="diff money">' . $item['diff'] . '</span>',
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>