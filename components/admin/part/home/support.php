<?php

$query = "SELECT t1.id, t1.name, t2.image
	  FROM goods as t1 INNER JOIN goodimg as t2 ON t1.logo = t2.id
	  WHERE t1.id IN (332, 334, 339, 337, 333, 336)";
$stmt = $GLOBALS['DB']->query($query);
$smarty->assign('top_offers', $stmt->fetchAll(PDO::FETCH_ASSOC));

$smarty->display('admin' . DS . 'home' . DS . 'support.tpl');

?>
