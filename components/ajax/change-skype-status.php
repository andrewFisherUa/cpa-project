<?php

  $filter = new Filter;

  $id = $filter->sanitize($_POST['id'], "int");
  $status = $filter->sanitize($_POST['value'], "int");

  $defs = [ 0 => ["class"=>"warning", "label" => "На модерации" ],
            1 => ["class"=>"danger", "label" => "Не найден" ],
            2 => ["class"=>"success", "label" => "В ожидании" ],
            3 => ["class"=>"green", "label"=>"Добавлен"]];
  
  $query = "UPDATE partners SET skype_status = ? WHERE id = ?";
  $stmt = $GLOBALS['DB']->prepare($query);
  $stmt->execute([
    $status,
    $id
  ]);

  $record = new Audit([
    "group" => "user",
    "subgroup" => "change_skype_status",
    "action" => "Изменения статуса для пользователя с ID `{$id}`",
  ]);

  $record->addDetails([
    "status" => $status,
    "user_id" => $id,
  ]);

  $record->save();

  echo $defs[$status]['class'];
  die();

?>