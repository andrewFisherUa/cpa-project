<?php

$params = $_REQUEST;

if(!empty($params['dateFrom'])){
  $data = explode("/", $params['dateFrom']);
  $params['dateFrom'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]);
}

if(!empty($params['dateTo'])){
  $data = explode("/", $params['dateTo']);
  $params['dateTo'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]);
}

$iTotalRecords = count(News::getAll($GLOBALS['DB'], $params));
$iDisplayLength = intval($_REQUEST['length']);
$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
$iDisplayStart = intval($_REQUEST['start']);
$sEcho = intval($_REQUEST['draw']);

$records = ["data" => []];

$items = News::getAll($GLOBALS['DB'], $params, $iDisplayStart, $iDisplayLength);// Все новости

$classes = array(
  1 => 'label label-info',
  2 => 'label label-success',
  3 => 'label label-warning',
  4 => 'label label-um-green'
);

foreach ($items as $item){
  $activate_time = ($item->getActivateTime() > 0) ? date("d/m/Y H:i", $item->getActivateTime()) : " ";

  $actions = '<a href="javascript:;" class="action btn btn-sm btn-outline blue" data-action="edit" data-news="'.$item->getId().'"><i class="icon-note"></i></a>' . 
             '<a href="javascript:;" class="action btn btn-sm btn-outline blue" data-action="delete" data-news="'.$item->getId().'"><i class="icon-trash"></i></a>';

  $records["data"][] = array(
    $item->getId(),
    $item->getTitle(),
    $item->getTypeLabel(),
    '<span class="label-sm ' . $classes[$item->getStatus()] . '">' . $item->getStatusLabel() . '</span>',
    date('d/m/Y H:i', $item->getCreated()),
    $activate_time,
    "",
    $actions
  );
}

$records["draw"] = $sEcho;
$records["recordsTotal"] = $iTotalRecords;
$records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);
?>