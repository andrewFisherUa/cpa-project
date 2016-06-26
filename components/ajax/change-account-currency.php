<?php

  $filter = new Filter;

  $id = $filter->sanitize($_POST['id'], "int!");
  $status = $filter->sanitize($_POST["value"], ["string", "striptags"]);
  
  Balance::changeAccountCurrencyStatus($id, $status);


  $audit_record = [
  	"group" => "balance",
  	"subgroup" => "change_account_currency",
  	"action" => "",
  	"details" => [
  		"request_id" => $id 
  	]
  ];

  if ($status == "approved") {
  	$audit_record["priority"] = Audit::HIGH_PRIORITY;
  	$audit_record["action"] = "Одобрение запроса на изменение валюты по умолчанию. ID запроса: {$id}";	
  }

  if ($status == "canceled") {
  	$audit_record["action"] = "Отклонение запроса на изменение валюты по умолчанию. ID запроса: {$id}";	
  }

  Audit::addRecord($audit_record);
  

  echo $status;
?>