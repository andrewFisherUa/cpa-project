<?php

$filter = new Filter;
$action = $filter->sanitize($_POST["action"], ["string", "striptags"]);

$response = [];

if ($action == "get-details") {
	$aid = $action = $filter->sanitize($_POST["aid"], "int");
	
	$query = "SELECT * FROM audit_details WHERE aid = ?";
	$stmt = $GLOBALS['DB']->prepare($query);
	$stmt->execute([
		$aid
	]);

	$response["details"] = ""; $temp = "";

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$temp .= "<tr><th>" . $row["audit_key"] . "</th><td>" . $row["audit_value"] . "</td></tr>"; 
	}

	if (!empty($temp)) {
		$response["details"] = "<table class='table table-striped table-bordered'><thead><tr><th width='50%'>Ключ</th><th>Значение</th></tr></thead><tbody>" . $temp . "</tbody></table>";
	} else {
		$response["details"] = "Детали не найдены";
	}
}

echo json_encode($response);


?>