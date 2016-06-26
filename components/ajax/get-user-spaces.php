<?php

  $filters = [
    "user_id" => User::get_current_user_id()
  ];

  $items = Space::getAll($filters);

  $iTotalRecords = count($items);
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $records = array();
  $records["data"] = array();

  $end = $iDisplayStart + $iDisplayLength;
  $end = $end > $iTotalRecords ? $iTotalRecords : $end;

  for($i = $iDisplayStart; $i < $end; $i++) {
    $item = $items[$i];
    $id = $item->getId();

    $buttons = "<a href='/admin/spaces/{$id}' class='btn btn-sm btn-outline blue btn-edit' title='Редактировать источник'><i class='fa fa-edit'></i></a>
                <a href='javascript:;' class='btn btn-sm btn-outline red btn-remove' data-id='{$id}' title='Удалить источник'><i class='fa fa-trash'></i></a>";

    $url = ($item->getUrl() == "") ? "" : "<a href='{$item->getUrl()}' target='_blank'>{$item->getUrl()}</a>";

    $records["data"][] = array(
     $id,
     $item->getName(),
     $item->getSourceName(),
     $url,
     $item->getTypeAlias(),
     "<div class='text-center'><span class='label label-sm label-{$item->getStatusClassName()}'>{$item->getStatusLabel()}</span></div>",
     $buttons
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>