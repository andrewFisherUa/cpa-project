<?php

$template_data = [
	"balance" => Balance::getAll(0),
];

// Статистика
$date = new DateTime("tomorrow", new DateTimeZone('Europe/Kiev'));
$date->setTime(0,0);

$template_data["stat_range"] = [
	"to" => $date->sub(new DateInterval('P1D'))->format('d-m-Y'),
	"from" => $date->sub(new DateInterval('P11D'))->format('d-m-Y')
];

// Количество подтвержденных заказов
$query = "SELECT COUNT(id) 
		  FROM orders 
		  WHERE status = 1 AND created > UNIX_TIMESTAMP(CURDATE())";
$stmt = $GLOBALS['DB']->query($query);
$template_data["confirmed_orders_count"] = $stmt->fetchColumn();

$default_currency = get_default_currency();

// Общий баланс пользователей
$template_data["total_balance"] = [
	"currency" => $default_currency,
	"amount" => get_total_balance($default_currency)
];

// Приблизительная сумма выплат
$date = new DateTime("today", new DateTimeZone('Europe/Kiev'));

if ($date->format('N') == 2) {
	$template_data["approximate_payments"] = $template_data["total_balance"];
} else {
	$query = "SELECT AVG(t1.webmaster_commission) AS avg_profit, t2.currency_code
			  FROM orders as t1 INNER JOIN country AS t2 ON t1.country_code = t2.code
			  WHERE t1.pass > 0 AND t1.created BETWEEN ? AND ?
			  GROUP BY t2.currency_code";
	$stmt = $GLOBALS["DB"]->prepare($query);
	$stmt->execute([
		$date->sub(new DateInterval('P10D'))->format('U'),
		$date->add(new DateInterval('P7D'))->format('U'),
	]);

	$avg_profit = 0;
	while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$temp = Converter::getConvert($a["currency_code"], $default_currency, $a["avg_profit"]);
		$avg_profit += $temp["amount"];
	}

	$tuesday = new DateTime("next Tuesday", new DateTimeZone('Europe/Kiev'));
	$today = new DateTime("today", new DateTimeZone('Europe/Kiev'));

	$days_left = $tuesday->diff($today)->format('%a');

	$template_data["approximate_payments"] = [
		"currency" => $default_currency,
		"amount" => $template_data["total_balance"]["amount"] + $days_left * round($avg_profit)
	];
}

require_once PATH_TEMPLATES . '/admin/home/admin.php';

enqueue_scripts([
	"/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
	"/assets/global/plugins/flot/jquery.flot.min.js",
	"/assets/global/plugins/flot/jquery.flot.resize.min.js",
	"/assets/global/plugins/flot/jquery.flot.time.min.js",
    "https://www.gstatic.com/charts/loader.js",
	"/misc/js/page-level/dashboard-admin.js",
]);

function get_total_balance(){
	$profit = [];

	// прибыль за заказы
	$query = "SELECT SUM(webmaster_commission) as profit, country_code
			  FROM pass
			  WHERE closed = 0
			  GROUP BY country_code";
	$stmt = $GLOBALS["DB"]->query($query);
	while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$profit[$a["country_code"]] = $a["profit"];
	}

	$convert_to = get_default_currency();
	$converted = 0;

	// личный баланс
	$query = "SELECT SUM(a.balance) as balance, a.type, c.currency_code
		      FROM accounts as a INNER JOIN user_role as u ON a.user_id = u.user_id
		      					 INNER JOIN country as c ON a.type = c.code
		      WHERE u.role_id = 2
		      GROUP BY c.currency_code";
	$stmt = $GLOBALS["DB"]->query($query);
	while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$amount = $profit[$a["type"]] + $a["balance"];

		$temp = Converter::getConvert($a["currency_code"], $convert_to, $amount);
		$converted += $temp["amount"];
	}

	return round($converted);
}

?>
