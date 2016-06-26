<?php

$filters = [];

// Офферы
$filters['offers'] = [];

$q = "SELECT t1.id, t1.name
     FROM goods AS t1 INNER JOIN users2goods as t2 on t1.id = t2.g_id
       WHERE available_in_offers = 1";
$stmt = $GLOBALS['DB']->query($q);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $filters['offers'][$row['id']] = $row['name'];
}

$users = User::get_by_role_name("webmaster");

$filters['status'] = [
  Order::STATUS_PROCESSING => "В обработке",
  Order::STATUS_CONFIRMED => "Подтвержден",
  Order::STATUS_DELIVERED => "Забран",
  Order::STATUS_CANCELED => "Аннулирован",
  Order::STATUS_RETURN => "Возврат"
];

$stmt = $GLOBALS['DB']->query("SELECT DISTINCT status2_name FROM orders WHERE status2_name != ''");

$filters['status2'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

$isAdmin = User::isAdmin();

require_once PATH_ROOT . DS . "templates" . DS . "admin" . DS . "orders" . DS . "bad_orders.php";

enqueue_scripts( array(
  "/assets/global/plugins/datatables/datatables.min.js",
  "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
  "/assets/global/scripts/datatable.js",
  "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
  "/assets/global/plugins/select2/js/select2.min.js",
  "/misc/js/page-level/bad-orders.js"));



?>