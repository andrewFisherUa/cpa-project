<?php


$query = "SELECT id, name FROM goods WHERE available_in_offers = 1";
$offers = $GLOBALS['DB']->query($query)->fetchAll(PDO::FETCH_ASSOC);
$smarty->assign('offers', $offers);

//список вебмастеров
$query = "SELECT u.user_id as id, u.login 
		  FROM users AS u INNER JOIN user_role AS r ON u.user_id = r.user_id
		  WHERE r.role_id = 2";
$users = $GLOBALS['DB']->query($query)->fetchAll(PDO::FETCH_ASSOC);
$smarty->assign('users', $users);

$smarty->display('admin' . DS . 'offers' . DS . 'targets.tpl');

enqueue_scripts(array(
  "/assets/global/plugins/select2/js/select2.min.js",
  "/misc/js/page-level/targets.js"
));

?>