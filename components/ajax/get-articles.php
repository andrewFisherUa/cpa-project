<?php

  $filter = new Filter;

  $filters = array();

  if ( isset($_REQUEST["title"]) && $_REQUEST["title"] != "" ){
    $filters['title'] = $filter->sanitize($_REQUEST["title"], ["string", "striptags"]);
  } 

  if ( isset($_REQUEST["rubric_id"]) && $_REQUEST["rubric_id"] > 0 ) {
    $filters['rubric_id'] = $filter->sanitize($_REQUEST["rubric_id"], "int"); 
  }

  if ( isset($_REQUEST["status"]) && $_REQUEST["status"] > 0 ) {
    $filters['status'] = $filter->sanitize($_REQUEST["status"], "int");
  } 

  if ( array_key_exists('weight', $_REQUEST) && $_REQUEST["weight"] != "") {
    $filters['weight'] = $filter->sanitize($_REQUEST["weight"], "int"); 
  }

  if(!empty($_REQUEST['date_from'])){
    $data = explode("/", $filter->sanitize($_REQUEST['date_from'], ["string", "striptags"]));
    $filters['date_from'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]);
  }

  if(!empty($_REQUEST['date_to'])){
    $data = explode("/", $filter->sanitize($_REQUEST['date_to'], ["string", "striptags"]));
    $filters['date_to'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]);
  }

  $items = Article::get_all( $filters );

  $iTotalRecords = count( $items );
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $records = array();
  $records["data"] = array();

  $end = $iDisplayStart + $iDisplayLength;
  $end = $end > $iTotalRecords ? $iTotalRecords : $end;

  for($i = $iDisplayStart; $i < $end; $i++) {
    $article = $items[$i];

    $actions = "<a class='btn btn-sm btn-outline blue' href='#edit-articles' data-article='".$article['article_id']."'><i class='icon-pencil'></i></a>";
    $actions .= "<span class='btn btn-sm btn-outline blue btn-remove' data-article='".$article['article_id']."'><i class='fa fa-trash'></i></span>";

    $rubric = Article::get_rubric( $article['rubric_id'] );

    $records["data"][] = array(
      $article['article_id'],
      $article['title'],
      $rubric['name'],
      Article::get_status_html( $article['status'] ),
      $article["weight"],
      date('d.m.Y H:i', $article['modified']),
      $actions
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>