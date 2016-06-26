<?php

$admin = User::isAdmin();

$smarty->assign('admin', $admin);
$template = "index.tpl";

if ( $_REQUEST['k'] == 'view' ) {
  require_once ('part/offers/single.php');
}

if ( $_REQUEST['k'] == 'edit' || $_REQUEST['k'] == 'new') {
  require_once ('part/offers/edit.php');
}

if ( $_REQUEST['k'] == 'targets' ) {
  require_once ('part/offers/targets.php');
}

if ( $_REQUEST['k'] == 'rules' ) {
  require_once ('part/offers/rules.php');
}

if ( $_REQUEST['k'] == "" || $_REQUEST['k'] == 'user') {

  $user_id = User::get_current_user_id();
  $filters = [];

  if (empty($_REQUEST['k'])) {

    $offers = Offer::getAll();
    if ( !$admin ) {
      $filters['status'] = Offer::STATUS_ACTIVE; // Вебмастерам показывать только активные офферы
      $filters['available_in_offers'] = true;
      $filters['available_to_user'] = $user_id;
    }

    $filtered = Offer::getFiltered($offers, $filters);

  } else {
    $filters['status'] = Offer::STATUS_ACTIVE; // Вебмастерам показывать только активные офферы
    $filters['available_in_offers'] = true;

    $filtered = Offer::getFiltered(Offer::getbyUID($user_id), $filters);
  }

  $items = [];
  foreach ($filtered as $f) {
    $items[] = [
      "id" => $f->getId(),
      "name" => $f->getName()
    ];
  }

  $smarty->assign('items', $items);
  $smarty->assign('status_list', Offer::getStatusList());
  $smarty->assign('country', Country::getAll());
  $smarty->assign('cats', Categories::getByType(Categories::TYPE_OFFER_CATEGORY));
}

if ($_REQUEST['k'] == 'user'){
  $template = 'user.tpl';
}

if ($_REQUEST['k'] == 'user' || $_REQUEST['k'] == ''){

  $smarty->assign('admin', $admin);
   $smarty->display('admin' . DS . 'offers' . DS . $template);

   enqueue_scripts(array(
    "/assets/pages/scripts/table-datatables-fixedheader.min.js",
    "/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/uniform/jquery.uniform.min.js",
    "/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
    "/assets/global/plugins/select2/js/select2.min.js",
    "/misc/js/page-level/offers.js"

  ));
}

?>