<?php

$widget = [];

foreach (Converter::$currencies as $c) {
	$widget[strtolower($c)] = Converter::getWidget($c);
}

$smarty->assign('currencies', Converter::getCurrencies(["USD"]));
$smarty->assign('default_exchange_widget', Converter::getWidget("USD", true));
$smarty->assign('widget', $widget);
$smarty->display( 'admin' . DS . 'exchange_rates' . DS . 'index.tpl' );

enqueue_scripts(array(
    "/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/uniform/jquery.uniform.min.js",
    "/assets/global/plugins/moment.min.js",
    "/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
    "/assets/global/plugins/clockface/js/clockface.js",
    "/misc/js/page-level/exchange.js",
));

?>