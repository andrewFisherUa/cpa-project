<?php

if (isset($_POST["save"])) {
  $applyToAll = isset($_POST['apply_to_all']);
  if ($applyToAll) {
    $options = new Options("goods_option");
  }
  $GLOBALS['DB']->exec("DELETE FROM default_refprofits");
  $query = "INSERT INTO default_refprofits(country_code, level, value, type) VALUES (:country_code, :level, :value, :type)";
  $stmt = $GLOBALS['DB']->prepare($query);

  foreach ($_POST["defaults"] as $country_code=>&$values) {
    if ($applyToAll) {
      $data[] = array("country_code"=>$country_code, "id"=>$options->getId("refprofit_type"), "value"=>$values["type"]);
    }
    foreach ($values["levels"] as $level=>&$value) {
      if ($applyToAll) {
        $data[] = array("country_code"=>$country_code, "id"=>$options->getId("refprofit_level{$level}"), "value"=>$value);
      }
      $stmt->bindParam(":country_code", $country_code, PDO::PARAM_STR);
      $stmt->bindParam(":type", $values["type"], PDO::PARAM_STR);
      $stmt->bindParam(":level", $level, PDO::PARAM_INT);
      $stmt->bindParam(":value", $value, PDO::PARAM_INT);
      $stmt->execute();
    }
  }

  if ($applyToAll) {
    $query = "UPDATE offer_option SET value = :value WHERE country_code = :country_code AND option_id = :id";
    $stmt = $GLOBALS['DB']->prepare($query);
    foreach ($data as &$item) {
      $stmt->bindParam(":value", $item['value'], PDO::PARAM_STR);
      $stmt->bindParam(":country_code", $item['country_code'], PDO::PARAM_STR);
      $stmt->bindParam(":id", $item['id'], PDO::PARAM_INT);
      $stmt->execute();
    }
  }
  unset($_POST);
}


$stmt = $GLOBALS['DB']->query("SELECT * FROM default_refprofits");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $data[$row["country_code"]]["type"] = $row["type"];
  $data[$row["country_code"]]["levels"][$row["level"]] = $row["value"];
}

foreach (Country::getAll() as $c) {
  $code = $c["code"];
  $refprofits[] = array(
    "code" => $code,
    "name" => $c["name"],
    "currency" => $c["currency_code"],
    "type" => $data[$code]["type"],
    "level1" => $data[$code]["levels"][1],
    "level2" => $data[$code]["levels"][2],
    "level3" => $data[$code]["levels"][3]);
}

$smarty->assign('refprofits', $refprofits);
$smarty->display('admin' . DS . 'partners' . DS . 'profits.tpl');
?>