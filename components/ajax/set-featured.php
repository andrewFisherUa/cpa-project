<?php

$stmt = $GLOBALS['DB']->prepare("UPDATE goods SET featured = :featured WHERE id = :id");
$stmt->bindParam(":id", $_POST['id'], PDO::PARAM_INT);
$stmt->bindParam(":featured", $_POST['value'], PDO::PARAM_INT);
$stmt->execute();

?>