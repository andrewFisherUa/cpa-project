<?php

$items = $GLOBALS['DB']->query("SELECT id, name FROM goods")->fetchAll(PDO::FETCH_ASSOC);
$smarty->assign('items', $items);

$smarty->display("admin/offers/stat_index.tpl");

enqueue_scripts(array(
  "/assets/global/plugins/datatables/datatables.min.js",
  "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
  "/assets/global/scripts/datatable.js",
  "/assets/global/plugins/uniform/jquery.uniform.min.js",
  "/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
  "/assets/global/plugins/select2/js/select2.min.js",
  "/misc/js/page-level/offerStatIndex.js",
));

?>