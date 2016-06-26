<?php

$filter = new Filter;

if ($_REQUEST['k'] == "") {
	if (isset($_POST["make_replenishment"])) {

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

		Audit::addRecord([
			"group" => "transaction",
			"action" => "Пополнение баланса вебмастера `" . $data["user_id"] . "`",
			"priority" => Audit::HIGH_PRIORITY,
			"details" => [
				"user_id" => $data["user_id"],
				"amount" => $data["amount"],
				"country_code" => $data["country_code"]
			],
		]);

		unset($_POST);
	}

	// Users List
	$stmt = $GLOBALS['DB']->query("SELECT u.* FROM users AS u INNER JOIN user_role ur ON ur.user_id = u.user_id LEFT JOIN partners AS p ON u.user_id = p.id WHERE ur.role_id = 2 OR ur.role_id = 3");
	$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Countries list
	$countries = Country::getAll();

	require_once(PATH_ROOT . '/templates/admin/balance/operations.php');

	enqueue_scripts(array(
		"/assets/global/plugins/select2/js/select2.min.js",
		"/misc/js/page-level/balanceOperations.js"
	));
} else {
	$smarty->display('admin' . DS . 'balance' . DS . $_REQUEST['k'] . '.tpl');

	enqueue_scripts(array(
		"/assets/global/plugins/datatables/datatables.min.js",
	    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
	    "/assets/global/scripts/datatable.js",
	    "/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
		"/misc/js/page-level/transfer.js"
	));
}


?>