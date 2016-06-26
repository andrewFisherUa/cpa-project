<?php

  $items = Content::get_groups();

  $iTotalRecords = count( $items );
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $records = array();
  $records["data"] = array();

  $end = $iDisplayStart + $iDisplayLength;
  $end = $end > $iTotalRecords ? $iTotalRecords : $end;

  for($i = $iDisplayStart, $pricesView = ''; $i < $end; $i++) {

    $records["data"][] = array(
        $items[$i]['g_id'],
        $items[$i]['name'],
        '<a href="javascript:;" data-group="'.$items[$i]['g_id'].'" class="btn btn-sm btn-outline green btn-edit"><i class="fa fa-edit"></i></a>
        <span class="btn btn-sm btn-outline red remove-group" data-id="'.$items[$i]['g_id'].'"><i class="fa fa-trash"></i></span>'
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>