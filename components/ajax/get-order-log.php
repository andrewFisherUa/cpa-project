<?php

$response = "Нет данных";

$filter = new Filter;

$id = $filter->sanitize($_POST["id"], "int");

if ($id > 0) {
	$query = "SELECT status_name, comment, created FROM orders_logs WHERE order_id = ?";
	$stmt = $GLOBALS["DB"]->prepare($query);
	$stmt->execute([
		$id
	]);

	if ($stmt->rowCount() > 0) {
		$response = "";
		while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$response .= "<p>" . date("d.m.y H:i", $a["created"]) . " " . $a["status_name"];

			if (!empty($a["comment"])) {
				$response .= "(" . $a["comment"] . ")";
			}

			$response .= "</p>";
		}
	}

} else {
	$response = "Ошибка";
}

echo $response;

?>