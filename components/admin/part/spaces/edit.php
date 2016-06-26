<?php


$filter = new Filter;

$error = false;

$template_data = [];

if ($_REQUEST['k'] == 'new') {
  $template = $filter->sanitize($_REQUEST['b'], ["string", "striptags"]);
}

if (!empty($_REQUEST['k']) && $_REQUEST['k'] != 'new') {

  $id = $filter->sanitize($_REQUEST['k'], "int!");

  $space = Space::getInstance($id);
  if ($space !== false) {
    if (User::isAdmin() || $space->getUserId() == User::get_current_user_id()) {
      $template_data = [
        "id" => $space->getId(),
        "name" => $space->getName(),
        "desc" => $space->getDescription(),
        "comment" => $space->getComment(),
        "url" => $space->getUrl(),
        "source" => $space->getSource()
      ];

      if ($space->getType() == Space::TYPE_SITE) {
        $template_data["stat_url"] = $space->getMeta("stat_url");
        $template_data["stat_login"] = $space->getMeta("stat_login");
        $template_data["stat_pass"] = $space->getMeta("stat_pass");

        foreach ($space->getMeta("lang") as $a) {
          $template_data["lang"][$a] = true;
        }
      }

      $template = $space->getType();    
    } else {
      $error = true;
      echo "<div class='alert alert-danger'>Отказано в доступе</div>";
    }
  } else {
    $error = true;
    echo "<div class='alert alert-danger'>Источник не найден</div>";
  }
}

if (!$error){
  $template_data["sources"] = Space::getTrafficSources($template);
  $smarty->assign('data', $template_data);
  $smarty->display('admin' . DS . 'spaces' . DS . $template . '.tpl');

  enqueue_scripts([
    "/misc/js/jquery.validate.min.js",
    "/misc/js/messages_ru.js",
    "/assets/global/plugins/select2/js/select2.min.js",
    "/misc/js/page-level/spaces.js" ]);
}

?>