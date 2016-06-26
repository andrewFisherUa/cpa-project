<?php

function getIds($filter, $ids){
	if (empty($ids)) {
		return array();
	}

	$stmt = $GLOBALS['DB']->query("SELECT user_id, offer_id FROM user_target ORDER BY user_id");
	$items = array();

	if ($filter == "offer") {
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$items[$row["user_id"]][] = $row["offer_id"];
		}
	} else if ($filter == "webmaster") {
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$items[$row["offer_id"]][] = $row["user_id"];
		}
	}

	foreach ($items as $k=>$v) {
		foreach ($ids as $x) {
			if (!in_array($x, $v)) {
				unset($items[$k]);
				break;
			}
		}
	}
	return array_keys($items);
}

$response = array();
$action = $_POST["action"];

if ($action == "get-selection") {
	$response["ids"] = getIds($_POST['filter'], $_POST["ids"]);
}

if ($action == "save") {
	$result = implode(",", getIds($_POST['filter']));
	$filter = implode(",", $_POST['ids']);
	$query = "DELETE FROM user_target WHERE";

	if ($_POST['filter'] == "offer") {
		if (!empty($result)) {
			$query .= " user_id IN (:result) AND ";
		}
		$query .= " offer_id IN (:filter)";
	} else if ($_POST['filter'] == "webmaster") {
		if (!empty($result)) {
			$query .= " offer_id IN (:result) AND ";
		}
		$query .= " user_id IN (:filter)";
	}

	$stmt = $GLOBALS['DB']->prepare($query);
	if (!empty($result)) {
		$stmt->bindParam(":result", $result, PDO::PARAM_STR);
	}
	$stmt->bindParam(":filter", $filter, PDO::PARAM_STR);
	$stmt->execute();

	// Добавляем новые
	$target_id = 2;
	$query = "INSERT INTO user_target(user_id, target_id, offer_id) VALUES (:user_id, :target_id, :offer_id)";
	$stmt = $GLOBALS['DB']->prepare($query);
	foreach ($_POST['ids'] as &$k) {
		foreach ($_POST['result'] as &$v) {
			$offer_id = ($_POST['filter'] == "offer") ? $k : $v;
			$user_id = ($_POST['filter'] == "offer") ? $v : $k;
			$stmt->bindParam(":target_id", $target_id, PDO::PARAM_INT);
			$stmt->bindParam(":offer_id", $offer_id, PDO::PARAM_INT);
			$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
			$stmt->execute();
		}
	}
}

echo json_encode($response);
?>