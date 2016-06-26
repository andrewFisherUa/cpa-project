<?php

$response = [];
$data = $_POST;

if ($data['action'] == "add-special") {
	$data["user_id"] = User::get_current_user_id();
	$r = Converter::addSpecialValue($data);

	if ($r === true) {
	    $response["success"] = true;
	} else {
	    $response["errors"] = $r;
	}
}

if ($data['action'] == "get-rate") {
	$r = Converter::getRate($data['from'], $data['to']);
	if ($r == false) {
		$response["errors"][] = "Не удалось найти курс " . $data['from'] . "/" . $data['to'];
	} else {
		$response["success"] = true;
		$response["rate"]["bid"] = round($r["bid"], 5);
		$response["rate"]["ask"] = round($r["ask"], 5);
	}
}

if ($data['action'] == "test-conflict") {
	$num = 0;
	// Проверка того, какие ID будут отменены при обновлении статуса
	foreach ($data['ids'] as $id) {
		$stmt = $GLOBALS['DB']->prepare("SELECT `from`, `to` FROM special_exchange_rates WHERE id = ?");
		$stmt->execute([$id]);
		$pair = $stmt->fetch(PDO::FETCH_ASSOC);

		$stmt = $GLOBALS['DB']->prepare("SELECT id FROM special_exchange_rates WHERE `from` = :from AND `to` = :to AND status = " . Converter::STATUS_APPROVED);
		$stmt->bindParam(":from", $pair['from'], PDO::PARAM_STR);
		$stmt->bindParam(":to", $pair['to'], PDO::PARAM_STR);
		$stmt->execute();
		$temp = $stmt->fetchAll(PDO::FETCH_COLUMN);

		if (!empty($temp)) {
			$temp[] = $id;
			$ids = Converter::testCollision($temp);
			if ($ids !== true) {
				$response['conflict'][$num] = [$id];
				foreach ($ids as $i) {
					if ($i != $id) {
						$response['conflict'][$num][] = $i;
					}
				}
				$num++;
			}
		}
	}

	$response['success'] = empty($response['conflict']);
}

if ($data['action'] == "cancel" || $data['action'] == "approve") {

	if ( $data['action'] == "approve" ) {
		$r = Converter::testCollision($data['ids']);
		if ($r === true) {
			$response["success"] = true;
			$response["error"] = false;
			foreach ($data['ids'] as $id) {
				Converter::updateStatus($id, Converter::STATUS_APPROVED);
			}
		} else {
			$response["error"] = $r;
		}
	} else {
		foreach ($data['ids'] as $id) {
			Converter::updateStatus($id, Converter::STATUS_CANCELED);
		}
	}

	$response['widgets'] = [];
	foreach (Converter::$currencies as $c) {
		$response['widgets'][strtolower($c)] = Converter::getWidget($c);
	}

	$response['widgets']['default'] = Converter::getWidget("USD", true);
}

echo json_encode($response);

?>