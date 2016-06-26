<?php

$filter = new Filter;

if (isset($_POST["make_replenishment"]) && $_POST["make_replenishment"] == 1) {
	
	$data = [
		"user_id" => $filter->sanitize($_POST['user_id'], "int"),
		"amount" => $filter->sanitize($_POST['amount'], "int!"),
		"country_code" => $filter->sanitize($_POST['country_code'], ["striptags", "string"]),
	];
	
	$account = Balance::get($data['user_id'], $data['country_code']);
	$data["to_account"] = $account->getAccountId();
	$data["from_account"] = 0;
	$data["type"] = Transaction::TYPE_IN;
	$success = Transaction::add($data);
	$smarty->assign("success", $success);

	Audit::addRecord([
		"group" => "transaction",
		"action" => "Пополнение баланса UM",
		"priority" => Audit::HIGH_PRIORITY
	]);

	unset($_POST);
}

if (User::isAdmin()) {
	$smarty->assign('default_balance', Balance::getDefaultBalanceType(0));
	$smarty->assign('balance', Balance::getAll(0));
	$smarty->assign('countries', Country::getAll());
	$smarty->display( 'admin' . DS . 'balance' . DS . 'univermag.tpl' );
} 

enqueue_scripts(array(
	"/misc/js/page-level/balance.js",
	"/misc/js/page-level/payments.js"
));



?>