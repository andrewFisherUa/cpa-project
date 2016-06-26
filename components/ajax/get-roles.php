<?php

  $items = Role::get_all();

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

    $disabled = ( $items[$i]['essential'] == 1 ) ? "disabled" : "";

    $records["data"][] = array(
        '<input type="checkbox" name="id[]" value="'.$items[$i]['role_id'].'">',
        $items[$i]['role_id'],
        $items[$i]['role_name'],
        '<a href="javascript:;" data-role="'.$items[$i]['role_id'].'" class="btn btn-xs default btn-edit"><i class="fa fa-edit"></i> Редактировать</a>
        <a href="#" class="btn btn-xs default remove-item" data-role="'.$items[$i]['role_id'].'" '.$disabled.'><i class="fa fa-edit"></i> Удалить</a>'
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>