<?php

  $items = Article::get_rubrics();

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
    $rubric = $items[$i];

    $actions = "<a class='btn btn-xs default' href='#edit-rubrics' data-rubric='".$rubric['rubric_id']."'><i class='icon-pencil'></i> Редактировать</a> <span class='btn btn-xs default btn-remove' data-rubric='".$rubric['rubric_id']."'><i class='fa fa-trash'></i> Удалить</span>";

    $icon = ( $rubric['css'] != "" ) ? "<i class='".$rubric['css']."'></i> " : "";

    $records["data"][] = array(
      $rubric['rubric_id'],
      $icon . $rubric['name'],
      $rubric['weight'],
      $actions
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>