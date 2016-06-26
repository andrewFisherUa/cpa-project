<?php

$badges = [];

$user_id = User::get_current_user_id();
$isAdmin = User::isAdmin();

$time_from = mktime(0, 0, 0, date("n"), date("j"), date("Y"));

// new orders count
if ($isAdmin) {
  $query = "SELECT COUNT(id) FROM orders WHERE created > " . $time_from;
} else if ( User::has_role($user_id, 2) ){
  // webmaster
  $query = "SELECT COUNT(id) FROM orders WHERE user_id = " . $user_id . " AND created > " . $time_from;
} else if ( User::has_role($user_id, 3) ){
  // advertiser
  $query = "SELECT COUNT(t1.id)
            FROM orders AS t1 INNER JOIN order_goods AS t2 ON t1.id = t2.order_id
            WHERE t2.product_owner = " . $user_id . " AND t1.created > " . $time_from;
}

$stmt = $GLOBALS['DB']->query($query);
$num = $stmt->fetchColumn();
if ($num > 0) {
    $badges[] = ["url" => "/admin/orders", "num" => $num];
}

// users badge for admin
if ($isAdmin) {
	$stmt = $GLOBALS['DB']->query("SELECT COUNT(user_id) FROM users WHERE created > {$time_from} AND status = 1");

  $num = $stmt->fetchColumn();
  if ($num > 0) {
      $badges[] = ["url" => "/admin/users", "num" => $num];
  }
}

// transfers
if ($isAdmin) {
	$stmt = $GLOBALS['DB']->query("SELECT COUNT(transaction_id) FROM transactions WHERE status = '" . Transaction::STATUS_PROCESSING . "'");

  $num = $stmt->fetchColumn();
  if ($num > 0) {
      $badges[] = ["url" => "/admin/balance_operations/transfer", "num" => $num];
  }
}

// traffic sources
if ($isAdmin){
    $stmt = $GLOBALS['DB']->query("SELECT COUNT(id) FROM spaces WHERE type = '" . Space::TYPE_SITE . "' AND status = '" . Space::STATUS_MODERATION . "'");
    $num = $stmt->fetchColumn();
    if ($num > 0) {
        $badges[] = ["url" => "/admin/spaces", "num" => $num];
    }
}

echo json_encode($badges);

?>