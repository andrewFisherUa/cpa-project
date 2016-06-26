<?php

  $country_code = (isset($_REQUEST['country_code'])) ? $_REQUEST['country_code'] : "ru";
  $currency_code = Country::getCurrencyCode($country_code);

  if (isset($_REQUEST['role']) && $_REQUEST['role'] != -1) {
    $sql = "SELECT u.*
            FROM users AS u INNER JOIN user_role ur ON ur.user_id = u.user_id LEFT JOIN partners AS p ON u.user_id = p.id
            WHERE ur.role_id = ?";
    $stmt = $GLOBALS['DB']->prepare($sql);
    $stmt->execute([$_REQUEST['role']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $stmt = $GLOBALS['DB']->query("SELECT u.*
                                   FROM users AS u INNER JOIN user_role ur ON ur.user_id = u.user_id LEFT JOIN partners AS p ON u.user_id = p.id
                                   WHERE ur.role_id = 2 OR ur.role_id = 3");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  $iTotalRecords = count( $items );
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
    $balance = Balance::get($item['user_id'], $country_code);

    $records["data"][] = array(
      $item["user_id"],
      $item["login"],
      "<div class='text-right'><span class='money'>{$balance->getCurrent()}</span>&nbsp;{$currency_code}</div>",
      "<div class='text-right'><span class='money'>{$balance->getHold()}</span>&nbsp;{$currency_code}</div>",
      "<div class='text-right'><span class='money'>{$balance->getCanceled()}</span>&nbsp;{$currency_code}</div>",
      "<div class='text-right'><span class='money'>{$balance->getReferal()}</span>&nbsp;{$currency_code}</div>",
      "<div class='text-right'><span class='money'>{$balance->getReferalInHold()}</span>&nbsp;{$currency_code}</div>",
      "<div class='text-right'><span class='money'>{$balance->getCanceledRef()}</span>&nbsp;{$currency_code}</div>",
      "<div class='text-right'><span class='money'>{$balance->getProcessing()}</span>&nbsp;{$currency_code}</div>",
      "<div class='text-center'>" . Partner::getRefCount($item["user_id"]) . "</span>");
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>