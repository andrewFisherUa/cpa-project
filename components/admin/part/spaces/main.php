<?php

$isAdmin = User::isAdmin();
$template = "index";
$filter = new FIlter;

if (isset($_POST['submit']) && !empty($_POST['space']['type'])) {
  $message = [];

  $data = [];

  $data["id"] = $filter->sanitize($_POST['space']['id'], "int!"); 
  $data["type"] = $filter->sanitize($_POST['space']['type'], ["string", "striptags"]); 
  $data["name"] = $filter->sanitize($_POST['space']['name'], ["string", "striptags"]); 
  $data["url"] = $filter->sanitize($_POST['space']['url'], ["string", "striptags"]); 
  $data["desc"] = $filter->sanitize($_POST['space']['desc'], ["string", "striptags"]); 
  $data["comment"] = $filter->sanitize($_POST['space']['comment'], ["string", "striptags"]); 

  if (isset($_POST['space']['source'])) {
    $data["source"] = $filter->sanitize($_POST['space']['source'], "int!"); 
  }

  if (!empty($_POST['space']['meta'])) {
    $data["meta"] = [];
    $data["meta"]["stat_url"] = $filter->sanitize($_POST['space']['meta']['stat_url'], ["string", "striptags"]); 
    $data["meta"]["stat_login"] = $filter->sanitize($_POST['space']['meta']['stat_login'], ["string", "striptags"]); 
    $data["meta"]["stat_pass"] = $filter->sanitize($_POST['space']['meta']['stat_pass'], ["string", "striptags"]); 
    $data["meta"]["lang"] = [];

    foreach ($_POST['space']['meta']['stat_url'] as $a) {
      $data["meta"]["lang"][] = $filter->sanitize($a, ["string", "striptags"]); 
    }
  }

  unset($_POST);

  if (!$isAdmin && $data['id'] == 0) {

    $data['user_id'] = User::get_current_user_id();
    $saved = Space::add($data);

    if ($saved !== false) {
      $space = Space::getInstance($saved);
      
      $data['id'] = $saved;

      $message = [
        'class' => 'success',
        'text' => 'Новый источник трафика был добавлен'
      ];
    }
  }

  if ($data['id'] > 0) {

    $space = Space::getInstance($data['id']);
    $space->setName($data['name']);
    $space->setDescription($data['desc']);
    $space->setComment($data['comment']);
    $space->setUrl($data['url']);
    $space->setSource($data['source']);
    $space->setMeta($data['meta']);

    if ($space->save() === true){
      $message = [
        'class' => 'success',
        'text' => 'Изменения сохранены'
      ];
    }
  }

  if (empty($message)) {
    $message = [
      'class' => 'danger',
      'text' => 'Ошибка при сохранении'
    ];
  } else {

    // Мета данные не сохраняем в логи
    unset($data["meta"]);

    Audit::addRecord([
      "group" => "space",
      "subgroup" => "edit",
      "action" => "Создание / Редактирование источника трафика",
      "details" => $data
    ]);
  }

  $smarty->assign('message', $message);
}

if ($isAdmin) {
    // users list
    $smarty->assign('users', User::get_by_role_name("webmaster"));

    // sources
    $smarty->assign('sources', Space::getTrafficSources());

    // types
    $smarty->assign('types', Space::getTypeList());

    $template = "admin";
} else {

  $query = "SELECT id, url, status, note 
            FROM spaces WHERE type = ? AND (status = ? OR (status = ? AND viewed IS NULL)) AND user_id = ? ORDER BY created";
  $stmt = $GLOBALS['DB']->prepare($query);
  $stmt->execute([
    Space::TYPE_SITE,
    Space::STATUS_PROCESSING,
    Space::STATUS_CANCELED,
    User::get_current_user_id()
  ]);
  $ids = [];
  while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $message = $r['id'] . ": " . $r['url'];
    if ($r['status'] == Space::STATUS_PROCESSING) {
      $message .= " - не потверждено. <a href='/admin/spaces/validate/" . $r['id'] . "'>Инструкция подтверждения</a>";
    } else {
      $ids[] = $r['id'];
      $message .= " - отклонен. Комментарий: " . $r['note'];
    }
    $notes[] = $message;
  }  

  if (!empty($ids)) {
    $GLOBALS['DB']->exec("UPDATE spaces SET viewed = " . time() . " WHERE id IN (" . implode(",", $ids) . ")");
  }

  if (!empty($notes)) {
    $smarty->assign('notes', $notes);
  }

  $hasSpaces = count(Space::getAll(["user_id" => User::get_current_user_id()]));
  $smarty->assign('hasSpaces', $hasSpaces);
}

enqueue_scripts([
  "/assets/global/plugins/datatables/datatables.min.js",
  "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
  "/assets/global/scripts/datatable.js",
  "/assets/global/plugins/select2/js/select2.min.js",
  "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
  "/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
  "/misc/js/page-level/spaces.js" ]);

$smarty->display('admin' . DS . 'spaces' . DS . $template . '.tpl');

?>