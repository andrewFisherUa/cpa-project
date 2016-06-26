<?php

if (empty($_REQUEST['b']) && $_POST['space']['type'] == Space::TYPE_SITE) {
  $data = $_POST['space'];
  if (!User::isAdmin() && $data['id'] == 0) {
    $data['user_id'] = User::get_current_user_id();
    $saved = Space::add($data);
    if ($saved !== false) {
      $data['id'] = $saved;
      $smarty->assign('data', $data);
      $smarty->display('admin' . DS . 'spaces' . DS . 'validate.tpl');
    }
  }
} 

if (empty($_REQUEST['b']) && $_POST['space']['type'] != Space::TYPE_SITE) {
  echo "<script>window.location = '/admin/spaces/' </script>";
}

if (!empty($_REQUEST['b'])) {
  $s = Space::getInstance($_REQUEST['b']);
  if ($s === false) {
    echo "<div class='alert alert-danger'>Источник не найден</div>";
  } else {
    if ($s->getType() != Space::TYPE_SITE || $s->getUserId() != User::get_current_user_id()) {
      echo "<div class='alert alert-danger'>Отказано в доступе</div>";
    } else {
      if ($s->getStatus() == Space::STATUS_MODERATION || $s->getStatus() == Space::STATUS_APPROVED) {
        echo "<div class='alert alert-danger'>Источник `".$s->getName."` уже был подтвержден</div>";
      }

      if ($s->getStatus() == Space::STATUS_PROCESSING){
        $smarty->assign('data', [
          "url" => $s->getUrl(),
          "name" => $s->getName(),
          "id" => $s->getId()
        ]);

        $smarty->display('admin' . DS . 'spaces' . DS . 'validate.tpl');
      }
    }
  }
}

enqueue_scripts([
  "/misc/js/page-level/spaces.js" ]);

?>