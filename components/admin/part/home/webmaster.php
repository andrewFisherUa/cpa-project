<?php


$time_from = mktime(0, 0, 0, date("n"), date("j"), date("Y"));

// Новости
$uid = User::get_current_user_id();

$b = [];

foreach (Country::getAll() as $c) {
	$b[$c['code']]['current'] = 0;
	$b[$c['code']]['referal'] = 0;
	$b[$c['code']]['currency'] = $c['currency_code'];
}

// Текущий баланс
$query = "SELECT SUM(webmaster_commission) as sum, country_code as code
		  FROM orders WHERE user_id = ? AND created > {$time_from} AND pass > 0
		  GROUP BY country_code";

$stmt = $GLOBALS['DB']->prepare($query);
$stmt->execute([$uid]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$b[$row['code']]['current'] = $row['sum'];
}

// Реф. баланс
$query = "SELECT SUM(t2.amount) AS sum, t1.country_code as code
		  FROM orders AS t1 INNER JOIN order_refprofit AS t2 ON t1.id = t2.order_id
		  WHERE t2.user_id = ? AND t1.status = " . Order::STATUS_DELIVERED . " AND t1.hold = 0 AND t2.closed = 0
		  AND target_time + hold_time > {$time_from}";
$stmt = $GLOBALS['DB']->prepare($query);
$stmt->execute([$uid]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	if (!empty($row['code'])) {
		$b[$row['code']]['referal'] = $row['sum'];
	}
}

$query = "SELECT t1.id, t1.name, t2.image
	  FROM goods as t1 INNER JOIN goodimg as t2 ON t1.logo = t2.id
	  WHERE t1.id IN (332, 334, 339, 337, 333, 336)";
$stmt = $GLOBALS['DB']->query($query);

$template_data = [
	"balance" => $b,
	"ref_link" => get_registration_ref_link($uid),
	"top_offers" => $stmt->fetchAll(PDO::FETCH_ASSOC)
];

// Статистика
$date = new DateTime("tomorrow", new DateTimeZone('Europe/Kiev'));
$date->setTime(0,0);

$template_data["stat_range"] = [
	"to" => $date->sub(new DateInterval('P1D'))->format('d-m-Y'),
	"from" => $date->sub(new DateInterval('P7D'))->format('d-m-Y')
];

require_once PATH_ROOT . "/templates/admin/home/webmaster.php";

enqueue_scripts([
	"/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
	"/assets/global/plugins/flot/jquery.flot.min.js",
	"/assets/global/plugins/flot/jquery.flot.resize.min.js",
	"/assets/global/plugins/flot/jquery.flot.time.min.js",
    "https://www.gstatic.com/charts/loader.js",
	"/misc/plugins/jquery-zclip-master/jquery.zclip.js",
	"/misc/js/page-level/dashboard-webmaster.js"
]);



?>
