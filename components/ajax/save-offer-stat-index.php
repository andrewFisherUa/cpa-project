<?php

$filter = new Filter;

$epc_mode = $filter->sanitize($_POST["epc_mode"], ["string", "striptags"]);
$cr_mode = $filter->sanitize($_POST["cr_mode"], ["string", "striptags"]);
$epc = floatval($_POST['epc']);
$cr = floatval($_POST['cr']);
$id = $filter->sanitize($_POST['id'], "int");

$modeset = ["no_data", "specific", "stat"];

if (!in_array($epc_mode, $modeset)) {
	$epc_mode = "no_data";
}

if (!in_array($cr_mode, $modeset)) {
	$cr_mode = "no_data";
}

$query = "UPDATE offer_stat 
		  SET epc_mode = :epc_mode,
		      cr_mode = :cr_mode,
			  specific_epc = :epc,
			  specific_cr = :cr
		  WHERE offer_id = :id";

$stmt = $GLOBALS['DB']->prepare($query);
$stmt->bindParam(":epc_mode", $epc_mode, PDO::PARAM_STR);
$stmt->bindParam(":cr_mode", $cr_mode, PDO::PARAM_STR);
$stmt->bindParam(":id", $id, PDO::PARAM_INT);
$stmt->bindParam(":epc", $epc, PDO::PARAM_INT);
$stmt->bindParam(":cr", $cr, PDO::PARAM_INT);

$success = $stmt->execute();

if ($success) {
	Audit::addRecord([
		"action" => "Сохранение показателей EPC / CR",
		"group" => "stat",
		"details" => [
			"offer" => $id,
			"epc_mode" => $epc_mode,
			"cr_mode" => $cr_mode,
			"cr" => $cr,
			"epc" => $epc
		]
	]);
}

echo json_encode(["success" => $success]);

?>