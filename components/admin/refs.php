<?php

$user_id = User::get_current_user_id();
$referals = [];

$referal_profit = [
  "country" => [],
  "total" => 0
];

foreach (Country::getAll() as $c) {
  $balance = Balance::get($user_id, $c['code']);
  $p = $balance->getReferal();
  $cr = $balance->getCurrencyCode();

  if ($p > 0) {
    $v = Converter::getConvert($cr, "RUB", $p);
    $referal_profit['total'] += $v['amount'];
  }

  $referal_profit['country'][$c['code']] = $p . "&nbsp" . $cr;
}

$referal_profit['total'] = round($referal_profit['total']);

$referal_count = getTotalReferalsCount($user_id);
$ref_links = [
  "home" => get_ref_link($user_id),
  "registration" => get_registration_ref_link($user_id)
];

$smarty->assign('referal_count', $referal_count);
$smarty->assign('referal_profit', $referal_profit);
$smarty->assign('user_id', $user_id);
$smarty->assign('currency', "RUB");
$smarty->assign('ref_links', $ref_links);
$smarty->display( 'admin' . DS . 'partners'.DS.'refs.tpl' );

enqueue_scripts( array(
  "/assets/global/plugins/datatables/datatables.min.js",
  "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
  "/assets/global/scripts/datatable.js",
  "/misc/plugins/jquery-zclip-master/jquery.zclip.js",
  "/misc/js/page-level/referals.js"));


?>