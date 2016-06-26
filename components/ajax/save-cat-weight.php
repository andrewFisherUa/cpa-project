<?php

  $weight = (int) $_POST['value'];

  $stmt = $GLOBALS['DB']->prepare("UPDATE categories SET sub_order = :weight WHERE id = :id");
  $stmt->bindParam(":weight", $weight, PDO::PARAM_INT);
  $stmt->bindParam(":id", $_POST['id'], PDO::PARAM_INT);
  $stmt->execute();
  die();

?>