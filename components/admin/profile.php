<?php

$uid = User::get_current_user_id();

$query = "SELECT u.*, p.*
          FROM users as u INNER JOIN partners as p ON p.id = u.user_id 
          WHERE u.user_id = ?";
$stmt = $GLOBALS['DB']->prepare( $query );
$stmt->execute([
  $uid
]);
    
$profile = $stmt->fetch( PDO::FETCH_ASSOC );

// options 
$query = "SELECT t1.option_desc as `desc`, t1.id, t2.value
		  FROM options AS t1 INNER JOIN user_option AS t2 ON t1.id = t2.uoption
		  WHERE t1.id BETWEEN 1 AND 6 AND t2.user_id = ?";
$stmt = $GLOBALS["DB"]->prepare($query);
$stmt->execute([
	$uid
]);
$options = $stmt->fetchAll(PDO::FETCH_ASSOC);

$smarty->assign('options', $options);
$smarty->assign('wallets', Payment::getWallets($uid));
$smarty->assign('profile', $profile);

// Проверям может ли пользователь запросить API-ключ
$stmt = $GLOBALS['DB']->prepare("SELECT hash, status FROM api_requests WHERE user_id = ?");
$stmt->execute([$uid]);
$api_key = $stmt->fetch(PDO::FETCH_ASSOC);
$smarty->assign('api_link', get_api_url() . "/help?hash=" . $api_key["hash"]);
$smarty->assign('api_key', $api_key);


$smarty->assign('uid', $uid);
$smarty->display('admin' . DS . 'partners' . DS . 'profile.tpl');

enqueue_scripts(array(
	"/misc/js/jquery.mask.min.js",
	"/misc/plugins/jquery-zclip-master/jquery.zclip.js",
	"/misc/js/page-level/payments.js",
	"/misc/js/page-level/webmaster-profile.js"
));

?>