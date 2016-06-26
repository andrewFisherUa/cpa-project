<?php

$filter = new Filter;

$response = array("errors"=>array(), "success"=>array());

$action = $filter->sanitize($_POST["action"], ["string", "striptags"]);

if ($action == "add-wallet") {
	$user_id = User::get_current_user_id();

	$filter = new Filter;
	$wallet = $filter->sanitize($_POST['wallet'], ["string", "striptags"]);
	$wallet = str_replace(" ", "", $wallet);
	$wallet = str_replace("R", "", $wallet);	

	if (strlen($wallet) != 12) {
		$response["errors"][] = "Неверный формат кошелька";

		Audit::addRecord([
			"group" => "user",
			"subgroup" => "new_wallet",
			"priority" => Audit::MEDIUM_PRIORITY,
			"action" => "Не удалось добавить кошелек {$wallet}. Неправильный формат.",
			"details" => [
				"wallet_id" => $wallet,
			]
		]);	

	} else { 
		$wallet = "R" . $wallet;
		$r = Payment::addWallet($user_id, $wallet);
		if ($r) {
			$response["wallet"]["wid"] = $wallet;
			$response["wallet"]["type"] = "WMR";

			Audit::addRecord([
				"group" => "user",
				"subgroup" => "new_wallet",
				"priority" => Audit::MEDIUM_PRIORITY,
				"action" => "Добавление кошелька {$wallet}",
				"details" => [
					"wallet_id" => $wallet,
				]
			]);		

		} else {
			$response["errors"][] = "Кошелек {$wallet} уже существует";
		}
	}
}

if ($action == "ask-payment") {

	$data = [
		"wallet" => $filter->sanitize($_POST["wallet"], ["string", "striptags"]),
		"amount" => $filter->sanitize($_POST["amount"], "int"),
		"type" => $filter->sanitize($_POST["type"], ["string", "striptags"]),
	];

	$uid = User::get_current_user_id();
	$amount = $data['amount'];

	if ($amount < 1000) {
		$response["errors"][] = "Сумма выплаты меньше минимальной";
	} else {
		// Проверяем достаточно ли денег на балансе

		$b = new DefaultBalance($uid);
		$account_balance = $b->getCurrent();
		$ref_balance = $b->getReferal();
		$balance = $account_balance + $ref_balance;

		$low_balance = false;

		switch ($data['type']) {
			case "account" : $low_balance = $account_balance < $amount; break;
			case "referal" : $low_balance = $ref_balance < $amount; break;
			case "all" : $low_balance = $balance < $amount; break;
		}

		if ($low_balance) {
			$response["errors"][] = "Недостаточно средств";
		} else {
			// Создание запроса
			$data['user_id'] = $uid;
			$data['currency'] = "RUB";
			$response['success'] = Payment::ask($data);
			
			Audit::addRecord([
				"group" => "user",
				"subgroup" => "ask_for_payment",
				"priority" => Audit::HIGH_PRIORITY,
				"balance" => $balance,
				"action" => "Запрос на выплату " . $data['amount'] . " " . $data['currency'],
				"details" => $data
			]);
		}
	}
}

if ($action == "check-changed") {

	$id = $filter->sanitize($_POST["id"], "int");

	$response = [
		"changed" => FALSE,
	];

	$query = "SELECT t1.changed, t2.user_id, t2.login, t1.amount, t1.currency
			  FROM payments AS t1 INNER JOIN users AS t2 ON t1.changed_by = t2.user_id
			  WHERE t1.payment_id = ?";
	$stmt = $GLOBALS["DB"]->prepare($query);
	$stmt->execute([
		$id
	]);

	if ($stmt->rowCount() > 0){
		$temp = $stmt->fetch(PDO::FETCH_ASSOC);

		$response["changed"] = TRUE;
		$response["message"] = "<div class='alert alert-danger'>";
		$response["message"] .= "<p> Выплата была изменена " . date("d.m.Y H:i", $temp["changed"]) . " пользователем " . $temp["login"] . ".</p>";
		$response["message"] .= "<p> Размер выплаты состовляет: " . $temp["amount"] . " " . $temp["currency"] . "</p>";
		$response["message"] .= "</div>";
	} 
}

echo json_encode($response);


?>