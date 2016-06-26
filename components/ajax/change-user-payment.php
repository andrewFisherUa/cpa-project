<?php

$filter = new Filter;
$id = $filter->sanitize($_POST["payment_id"], "int!");

if (isset($_POST["approve"])) {

	$stmt = $GLOBALS['DB']->query("SELECT status FROM payments WHERE payment_id = {$id}");
	$status = $stmt->fetchColumn();
	if ($status == Payment::STATUS_APPROVED) {
		Audit::addRecord([
			"group" => "payment",
			"subgroup" => "repeat",
			"priority" => Audit::HIGH_PRIORITY,
			"action" => "Попытка одобрить выплату `{$id}`. Выплата была одобрена ранее."
		]);

	} else {
		$r = Payment::approve($id);

		if (!$r) {
			Audit::addRecord([
				"group" => "payment",
				"subgroup" => "low_balance",
				"priority" => Audit::HIGH_PRIORITY,
				"action" => "Попытка одобрить выплату `{$id}`. Недостаточно средств."
			]);
		}
	}
}

if (isset($_POST["cancel"])) {
	Payment::cancel($id, Payment::STATUS_CANCELED);

	Audit::addRecord([
		"group" => "payment",
		"subgroup" => "cancel",
		"priority" => Audit::HIGH_PRIORITY,
		"action" => "Отклонение выплаты `{$id}`"
	]);
}

if (isset($_POST["add-comment"])) {
	$comment = $filter->sanitize($_POST["comment"], ["string", "striptags"]);

	Payment::addComment($id, $comment);

	Audit::addRecord([
		"group" => "payment",
		"subgroup" => "add_comment",
		"action" => "Добавление комментария к выплате `{$id}`: {$comment}"
	]);
}

if (isset($_POST["edit_amount"])) {
	$amount = $filter->sanitize($_POST["amount"], "int");

	$query = "UPDATE payments SET amount = ?, changed = ?, changed_by = ? WHERE payment_id = ?";
	$stmt = $GLOBALS["DB"]->prepare($query);
	$stmt->execute([
		$amount, 
		time(),
		User::get_current_user_id(),
		$id
	]);
}

header("Location: /admin/payments/{$id}");

echo json_encode($response);


?>