<?php

$filter = new Filter;

$response = [];

$action = $filter->sanitize($_POST["action"], ["string", "striptags"]);

if ($action == "change-account-balance") {

	$uid = $filter->sanitize($_POST["user_id"], "int!");
	$currency = $filter->sanitize($_POST["currency"], ["string", "striptags"]);

	$current = Balance::getDefaultBalanceType($uid);
	if ($current == $currency) {
		$response = [
			"success" => false,
			"errors" => ["Валюта `" . Country::getCurrencyCode($current) . "` уже является валютой по умолчанию"]
		];
	} else {
		$response['success'] = Balance::requestChangeAccountCurrency($uid, $currency);

		if ($response['success']) {
			
			Audit::addRecord([
				"group" => "balance",
				"subgroup" => "change_currency",
				"action" => "Запрос на изменение валюты по умолчанию c {$current} на {$currency}"
			]);

		} else {
			$response['errors'] = ["Произошла ошибка"];
		}
	}
}

if ($action == "make_transfer") {
	$data = [
		"user_id" => User::get_current_user_id(),
		"type" => Transaction::TYPE_TRANSFER,
		"from_currency" => $filter->sanitize($_POST["from_currency"], ["string", "striptags"]),
		"to_currency" => $filter->sanitize($_POST["to_currency"], ["string", "striptags"]),
		"from_amount" => $filter->sanitize($_POST["from_amount"], "int!")
	];
	
	$r = Transaction::add($data);
	if ($r === true) {

		$response = [
			"success" => true
		];

		Audit::addRecord([
			"group" => "balance",
			"subgroup" => "transfer",
			"action" => "Запрос на перевод " . $data['from_amount'] . $data["from_currency"] . " в валюту " . $data["to_currency"]
		]);

	} else {
		$response = [
			"success" => false,
			"errors" => $r
		];
	}
}

echo json_encode($response);

?>