<?php


$stmt = $GLOBALS["DB"]->query("SELECT currency_code FROM country");
$c = $stmt->fetchAll(PDO::FETCH_COLUMN);

$amount = 1000;

$t = "<table class='table'>
		<thead>
			<tr>
				<th>Default</th>
				<th>From/To</th>
				<th>Сумма</th>
				<th>Курс</th>
				<th>Валютная пара</th>
			</tr>
		</thead>";



foreach ($c as $a) {
	foreach ($c as $b) {
		foreach ($c as $d) {
			if ($b != $d) {
				$r = Converter::getConvert($b, $d, $amount, $a);
				$converted = floor($r['amount']);
				$rate = $r['rate'];
				$pair = $r['pair'];

				$t .= "<tr>";
				$t .= "<td>{$a}</td>";
				$t .= "<td>{$b} / {$d}</td>";
				$t .= "<td><span class='money'>{$amount}</span>&nbsp;{$b} / <span class='money'>{$converted}</span>&nbsp;{$d}</td>";
				$t .= "<td>{$rate}</td>";
				$t .= "<td>{$pair}</td>";
			}

		}
	}

}


$t .= "<tbody></tbody></table>";
$smarty->assign("t", $t);

$widget = [];

foreach (Converter::$currencies as $c) {
	$widget[strtolower($c)] = Converter::getWidget($c);
}

$smarty->assign('currencies', Converter::getCurrencies(["USD"]));
$smarty->assign('default_exchange_widget', Converter::getWidget("USD", true));
$smarty->assign('widget', $widget);

$smarty->display( 'admin' . DS . 'exchange_rates' . DS . 'test.tpl' );


?>