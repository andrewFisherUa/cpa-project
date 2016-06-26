<?php

$filter = new Filter;

if (empty($_REQUEST['k'])) {
	
	$user_id = User::get_current_user_id();

	$isWebmaster = User::has_role($user_id, 2);
	$isAdv = User::has_role($user_id, 3);

	$default_balance = Balance::getDefaultBalanceType($user_id);

	$smarty->assign('user_id', $user_id);
	$smarty->assign('default_balance', $default_balance);
	$smarty->assign('countries', Country::getAll());

	$balance = [];
	$accounts = Balance::getAll($user_id);
	foreach ($accounts as $item) {
		$temp = $item->getCurrent();
		$max = ($temp > 0) ? $temp : 0;
		$balance[$item->getCurrencyCode()] = [
			"amount" => $max,
			"account_id" => $item->getAccountId(),
			"code" => $item->getCountryCode()
		];
	}

	$temp = new DefaultBalance($user_id);
	$defaultAccount = [
		'referal' => $temp->getReferal(),
		'current' => $temp->getCurrent(),
		'hold' => $temp->getHold(),
		'country_code' => $temp->getCountryCode(),
		'currency_code' => $temp->getCurrencyCode(),
	];

	$defaultAccount['balance'] = $defaultAccount['referal'] + $defaultAccount['current'];

	$smarty->assign('low_balance', $defaultAccount['balance'] < 1000);
	$smarty->assign('defaultAccount', $defaultAccount);
	$smarty->assign('balance', $balance);
	$smarty->assign('accounts', $accounts);

	if ($isWebmaster) {
		$options = new UserOptions($GLOBALS["DB"], $user_id);
		$smarty->assign('can_ask_for_payment', $options->getValue('allow_payment_request') == TRUE);
		$smarty->assign('wallets', Payment::getWallets($user_id));
		$smarty->display( 'admin' . DS . 'balance' . DS . 'partner.tpl' );
	}

	if ($isAdv) {

		$widget = Converter::getWidget(Country::getCurrencyCode($default_balance), false, true);
		$smarty->assign('widget', $widget);
		$smarty->display( 'admin' . DS . 'balance' . DS . 'advertiser.tpl' );
	}
	
	enqueue_scripts(array(
		"/misc/js/page-level/balance.js",
		"/misc/js/page-level/payments.js"
	));

}

if ($_REQUEST['k'] == "history") {
	$smarty->display('admin' . DS . 'balance' . DS . 'history.tpl');

	enqueue_scripts(array(
		"/assets/global/plugins/datatables/datatables.min.js",
	    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
	    "/assets/global/scripts/datatable.js",
	    "/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
		"/misc/js/page-level/payments.js"
	));
}


?>