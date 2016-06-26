<?php

$filter = new Filter;
$payment_id = $filter->sanitize($_REQUEST["k"], "int");

// Данные по выплате
$query = "SELECT t1.*, t2.login as username
		  FROM payments AS t1 INNER JOIN users AS t2 ON t1.user_id = t2.user_id
		  WHERE t1.payment_id = ?";
$stmt = $GLOBALS["DB"]->prepare($query);
$stmt->execute([
	$payment_id
]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!empty($data)) {
	$data["can_be_approved"] = $data["status"] == Payment::STATUS_MODERATION || $data["status"] == Payment::STATUS_CANCELED;
	$data["can_be_canceled"] = $data["status"] == Payment::STATUS_MODERATION;
	$data["can_be_changed"] = $data["status"] == Payment::STATUS_MODERATION;
	$data["amount"] = round($data["amount"]);
	$data["balance_before"] = round($data["balance_before"]);
	$data["balance_after"] = round($data["balance_after"]);

	// Данные по кошельку
	$query = "SELECT SUM(t1.amount) as payed_amount, COUNT(t1.payment_id) as payed_count, t2.created as wallet_created
			  FROM payments AS t1 INNER JOIN user_wallet AS t2 ON t1.wallet = t2.wallet
			  WHERE t1.wallet = ? AND t1.status = ?";
	$stmt = $GLOBALS["DB"]->prepare($query);
	$stmt->execute([
		$data["wallet"],
		Payment::STATUS_APPROVED
	]);

	$temp = $stmt->fetch(PDO::FETCH_ASSOC);
	$data["payed_amount"] = round($temp["payed_amount"]);
	$data["payed_count"] = $temp["payed_count"];
	$data["wallet_created"] = $temp["wallet_created"];

	// Значек статуса
	switch ($data["status"]) {
		case "moderation" : $status_alias = "Модерация"; break;
		case "canceled" : $status_alias = "Отклонен"; break;
		case "approved" : $status_alias = "Одобрен"; break;
	}

	$status = "<span class='label label-lg label-" . $data["status"] . "'>" . $status_alias . "</span>";

	// Данные о том, кто одобрял или редактировал выплату
	if ($data["approved_by"] || $data["changed_by"]) {

		$query = "SELECT login FROM users WHERE user_id = ?";
		$stmt = $GLOBALS["DB"]->prepare($query);
		
		if ($data["approved_by"] > 0) {
			$stmt->execute([
				$data["approved_by"]
			]);

			$data["approved_by_username"] = $stmt->fetchColumn();
		}

		if ($data["changed_by"] > 0) {
			$stmt->execute([
				$data["changed_by"]
			]);

			$data["changed_by_username"] = $stmt->fetchColumn();
		}
	}



	// Баланс пользователя
	$balance = new DefaultBalance($data["user_id"]);
	$data["balance"] = [
		"amount" => $balance->getCurrent() + $balance->getReferal(),
		"currency" => $balance->getCurrencyCode()
	];

	require_once PATH_ROOT . '/templates/admin/payments/single.php';

	enqueue_scripts(array(
		"/misc/plugins/jquery-zclip-master/jquery.zclip.js",
		"/misc/js/page-level/user-payments.js",
		"/assets/global/plugins/datatables/datatables.min.js",
		"/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
		"/assets/global/scripts/datatable.js",
		"/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
	));
} else {
	echo "<div class='alert alert-danger'>Страница не найдена!</div>";
}