<?php
  
  $filters = [];

  if (User::isAdmin()) {
    $accepted = [
      "id" => "int",
      "status" => "str",
      "user_id" => "int",
      "type" => "str",
      "source" => "int",
      "changed_from" => "str",
      "changed_to" => "str"
    ];

    $filter = new Filter;    

    foreach ($accepted as $a=>$b) {
      if (isset($_REQUEST['filters'][$a]) && $_REQUEST['filters'][$a] != -1) {
        if ($b == "int") {
          $filters[$a] = $filter->sanitize($_REQUEST['filters'][$a], "int");
        }

        if ($b == "str") {
          $filters[$a] = $filter->sanitize($_REQUEST['filters'][$a], ["string", "striptags"]);
        }
      }
    }

    if (!empty($filters['changed_from'])){
      $data = explode("/", $filters['changed_from']);
      $filters['changed_from'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]);
    }

    if(!empty($filters['changed_to'])){
      $data = explode("/", $filters['changed_to']);
      $filters['changed_to'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]);
    }
  }

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
    $status = $item->getStatus();

    $buttons = "<a href='/admin/spaces/{$id}' class='btn btn-sm btn-outline blue' title='Редактировать'><i class='fa fa-edit'></i></a>
                <a href='javascript:;' class='btn btn-sm btn-outline blue btn-view' title='Просмотр' data-id='{$id}'><i class='fa fa-search'></i></a>";

    $records["data"][] = array(
     $id,
     $item->getName(),
     date("d/m/Y", $item->getChanged()),
     $item->getUserId() . ":&nbsp;" . $item->getUserLogin(),
     $item->getTypeAlias(),
     $item->getSourceName(),
     '<a href="'.$item->getUrl().'" target="_blank" style="display:block;overflow:hidden; max-width:100px; max-height:1.2em;" data-toggle="tooltip" data-placement="top" title="' . $item->getUrl() .'">'.$item->getUrl().'</a></div>',
     "<span class='label label-sm label-{$item->getStatusClassName()}'><a href='javascript:;' id='status{$id}' class='status' data-subject='{$id}' data-type='select' data-pk='1' data-value='{$status}' data-original-title='Статус источника'>{$item->getStatusLabel()}</a></span>",
     $buttons
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>