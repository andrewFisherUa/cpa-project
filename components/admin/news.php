<?php

if (User::isAdmin()) {

  require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/admin/news/admin.php';

  enqueue_scripts( array(
    "/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/select2/js/select2.min.js",
    "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
    "/assets/global/plugins/bootstrap-summernote/summernote.min.js",
    "/assets/global/plugins/moment.min.js",
    "/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
    "/assets/global/plugins/clockface/js/clockface.js",
    "/misc/js/page-level/news.js",
  ));

} else {

  enqueue_scripts( array(
    "/misc/js/page-level/ajax_news.js",
    "/misc/js/page-level/news.js",
  ));
  // Страница новостей для пользователя
  $length = 15;
  $user_id = User::get_current_user_id();

  if ($_REQUEST["k"] == "page" && $_REQUEST["b"] > 0) {
    $page = $filter->sanitize($_REQUEST["b"], "int!");
  } else {
    $page = 1;
  }

  $params = [
    "user_id" => $user_id
  ];

  $count = count(News::getAll($GLOBALS['DB'], $params));
  $total = ceil($count / $length);
  $mainID = (int) $_REQUEST['k'];

  if ( $mainID > 0 ) {
    News::setViewed($GLOBALS['DB'], $user_id, $mainID);
    $main = News::getById($GLOBALS['DB'], $mainID);
    $params['exclude'] = $mainID;
  }

  //$news = News::getAll($GLOBALS['DB'], $params, ($page-1)*$length , $length);
  $news = News::getAll($GLOBALS['DB'], $params, 0, 10);

  $icons_name_jpg = array(
              1 => 'new_offer',     2 => 'offera_suspension',
              3 => 'offer_change',  4 => 'new_landing',
              5 => 'system_news',   6 => 'important'
            );
  $search_id_news = $_REQUEST['k'];
  $news_top_url = News::getById($GLOBALS['DB'], $_REQUEST['k']);

  require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/admin/news/user.php';
}
