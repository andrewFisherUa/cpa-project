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

/*
$filters['streams'] = [];
$stmt = $GLOBALS['DB']->query("SELECT f_id as id, name FROM flows");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $filters['streams'][$row['id']] = $row['name'];
}

// Лендинги --- выводим все лендинги, которые поключены к офферам вебмастера
$filters['landings'] = [];

$q = "SELECT DISTINCT t1.c_id as id, t1.name, t4.name as offer_name, t3.g_id as offer_id
    FROM content as t1 INNER JOIN offer_content as t2 ON t1.c_id = t2.landing_id
         INNER JOIN users2goods as t3 ON t3.g_id = t2.offer_id
         INNER JOIN goods AS t4 ON t4.id = t3.g_id
    ORDER BY t4.name, t1.name";
$stmt = $GLOBALS['DB']->query($q);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $filters['landings'][] = [
    "offer" => [
      "id" => $row['offer_id'],
      "name" => $row['offer_name']
    ],
    "landing" => [
      "id" => $row['id'],
      "name" => $row['name']
    ],
  ];
}

// Блоги --- выводим все лендинги, которые поключены к офферам вебмастера
$filters['blogs'] = [];
$q = "SELECT t1.c_id as id, t1.name, t4.name as offer_name, t3.g_id as offer_id
    FROM content as t1 INNER JOIN offer_content as t2 ON t1.c_id = t2.blog_id
         INNER JOIN users2goods as t3 ON t3.g_id = t2.offer_id
         INNER JOIN goods AS t4 ON t4.id = t3.g_id
    ORDER BY t4.name, t1.name";
$stmt = $GLOBALS['DB']->query($q);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $filters['blogs'][] = [
    "offer" => [
      "id" => $row['offer_id'],
      "name" => $row['offer_name']
    ],
    "blog" => [
      "id" => $row['id'],
      "name" => $row['name']
    ],
  ];
}

// Источники
$filters['spaces'] = [];

$stmt = $GLOBALS['DB']->query("SELECT id, name FROM spaces");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $filters['spaces'][$row['id']] = $row['name'];
}

// SUBID
$filters['subid'] = [
  "subid1" => [],
  "subid2" => [],
  "subid3" => [],
  "subid4" => [],
  "subid5" => [],
];

*/

$users = User::get_by_role_name("webmaster");

if ($_REQUEST['k'] == 'orders') {

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

  require_once PATH_ROOT . DS . "templates" . DS . "admin" . DS . "stats" . DS . "admin-orders.php";

  enqueue_scripts( array(
    "/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
    "/assets/global/plugins/select2/js/select2.min.js",
    "/misc/js/page-level/ordersStat.js"));

} else {

  $range = [
    "from" => mktime(0, 0, 0, date("n"), date("j"), date("Y")),
    "interval" => 86400
  ];

  $smarty->assign('filters', $filters);
  $smarty->assign('users', User::get_by_role_name("webmaster"));
  $smarty->assign('today', date("d-m-Y"));

  $smarty->display('admin/stats/admin_upd.tpl');


  enqueue_scripts(array(
    "/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
    "/assets/global/plugins/select2/js/select2.min.js",
    "/misc/js/page-level/admin-statistics-upd.js",
    "//cdn.jsdelivr.net/momentjs/latest/moment.min.js",
      "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
  ));

}



?>