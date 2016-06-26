<?php

// Страница подробной информации о вебмастере
$query = "SELECT t1.email, t1.login, t2.phone, t2.name as first_name, t2.last_name, t1.created, t2.skype
		  FROM users as t1 INNER JOIN partners AS t2 ON t1.user_id = t2.id
		  				   INNER JOIN user_role AS t3 ON t1.user_id = t3.user_id
		  WHERE t1.user_id = ? AND t3.role_id = 2";
$stmt = $GLOBALS['DB']->prepare($query);
$stmt->execute([
	$user_id
]);

$info = $stmt->fetch(PDO::FETCH_ASSOC);
$info["regdate"] = date("d.m.Y H:i:s", $info['created']);

	// Баланс
$balance = Balance::getAll($user_id);
$main_balance = new DefaultBalance($user_id);
$info['profit'] = $main_balance->getCurrent();

// Валюта по умолчанию
$info['default_currency'] = Country::getCurrencyCode(Balance::getDefaultBalanceType($user_id));
$rates_widget = Converter::getWidget($info['default_currency'], false, true);

// Кол-во подтвержденных заказов
$info['approved_orders_count'] = 0;

$query = "SELECT COUNT(*)
		  FROM orders
		  WHERE user_id = ? AND target_close = 1 AND (status IN (" . Order::STATUS_DELIVERED . ", " . Order::STATUS_CONFIRMED . ") OR (status = ".Order::STATUS_RETURN." and target != 1))";
$stmt = $GLOBALS['DB']->prepare($query);
$stmt->execute([$user_id]);
$info['approved_orders_count'] = $stmt->fetchColumn();

// Количество рефералов
$stmt = $GLOBALS['DB']->prepare("SELECT count(*) FROM partners WHERE sub = ?");
$stmt->execute([$user_id]);
$info['ref_count'] = $stmt->fetchColumn();

// Статистика
  $filters = [];

  // Офферы
  $filters['offers'] = [];

  $q = "SELECT t1.id, t1.name
       FROM goods AS t1 INNER JOIN users2goods as t2 on t1.id = t2.g_id
         WHERE available_in_offers = 1 AND t2.u_id = ?";
  $stmt = $GLOBALS['DB']->prepare($q);
  $stmt->execute([$user_id]);
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $filters['offers'][$row['id']] = $row['name'];
  }

  // Потоки
  $filters['streams'] = [];
  $q = "SELECT f_id as id, name FROM flows WHERE user_id = ?";
  $stmt = $GLOBALS['DB']->prepare($q);
  $stmt->execute([$user_id]);
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $filters['streams'][$row['id']] = $row['name'];
  }

  // Лендинги --- выводим все лендинги, которые поключены к офферам вебмастера
  $filters['landings'] = [];

  $q = "SELECT DISTINCT t1.c_id as id, t1.name, t4.name as offer_name, t3.g_id as offer_id
      FROM content as t1 INNER JOIN offer_content as t2 ON t1.c_id = t2.landing_id
           INNER JOIN users2goods as t3 ON t3.g_id = t2.offer_id
           INNER JOIN goods AS t4 ON t4.id = t3.g_id
      WHERE t3.u_id = ?
      ORDER BY t4.name, t1.name";
  $stmt = $GLOBALS['DB']->prepare($q);
  $stmt->execute([$user_id]);
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
      WHERE t3.u_id = ?
      ORDER BY t4.name, t1.name";
  $stmt = $GLOBALS['DB']->prepare($q);
  $stmt->execute([$user_id]);
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

  $q = "SELECT id, name FROM spaces WHERE user_id = ?";
  $stmt = $GLOBALS['DB']->prepare($q);
  $stmt->execute([$user_id]);
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

  $q = "SELECT t1.subid_name, t1.subid_val
      FROM subid_stat AS t1 INNER JOIN pfinder_keys AS t2 ON t1.pfinder_id = t2.pfinder_id
        WHERE t2.user_id = ?";
  $stmt = $GLOBALS['DB']->prepare($q);
  $stmt->execute([$user_id]);
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!in_array($row['subid_val'], $filters['subid'][$row['subid_name']])) {
      $filters['subid'][$row['subid_name']][] = $row['subid_val'];
    }
  }

$stat_range = [];
$date = new DateTime("tomorrow", new DateTimeZone('Europe/Kiev'));
$date->setTime(0,0);
$stat_range['to'] = $date->sub(new DateInterval('P1D'))->format('d-m-Y');
$stat_range['from'] = $date->sub(new DateInterval('P11D'))->format('d-m-Y');

enqueue_scripts(array(
	"/assets/global/plugins/flot/jquery.flot.min.js",
	"/assets/global/plugins/flot/jquery.flot.resize.min.js",
	"/assets/global/plugins/flot/jquery.flot.time.min.js",
	"https://www.gstatic.com/charts/loader.js",
    "/misc/js/page-level/user-details.js",
    "/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
    "/assets/global/plugins/select2/js/select2.min.js",
    "/misc/js/page-level/admin-statistics-upd.js",
    "//cdn.jsdelivr.net/momentjs/latest/moment.min.js",
    "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
));

require_once $_SERVER['DOCUMENT_ROOT'] . "/templates/admin/user/role-" . $role_id . ".php";

?>