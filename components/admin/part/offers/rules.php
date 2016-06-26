<?php

// Список офферов
$stmt = $GLOBALS['DB']->query("SELECT id, name FROM goods WHERE available_in_offers = 1 ORDER BY id DESC");
$offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$smarty->assign('offers', $offers);

// Правила
$stmt = $GLOBALS['DB']->query("SELECT `text` FROM offer_connection_rules WHERE offer_id = 0");
$rules = $stmt->fetchColumn();
$smarty->assign('rules', $rules);

$smarty->display('admin' . DS . 'offers' . DS . 'rules.tpl');

enqueue_scripts(array(
  "/assets/global/plugins/select2/js/select2.min.js",
  "/misc/js/page-level/offerRules.js"
));

?>