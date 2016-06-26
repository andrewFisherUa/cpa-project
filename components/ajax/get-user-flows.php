<?php

  $filters = array();

  if ( isset($_REQUEST["flow_id"]) && $_REQUEST["flow_id"] > 0 ) $filters['f_id'] = (int) $_REQUEST["flow_id"];
  if ( isset($_REQUEST["offer_id"]) && $_REQUEST["offer_id"] > 0 ) $filters['offer_id'] = (int) $_REQUEST["offer_id"];
  if ( isset($_REQUEST["user_id"]) && $_REQUEST["user_id"] > 0 ) $filters['user_id'] = (int) $_REQUEST["user_id"];

  if ( isset($_REQUEST['oid']) && $_REQUEST['oid'] > 0  && !isset($filters['offer_id']) ) {
    $filters['offer_id'] = (int) $_REQUEST['oid'];
  }

  $items = Flow::getAll($filters);

  $iTotalRecords = count($items);
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $records = array();
  $records["data"] = array();

  $end = $iDisplayStart + $iDisplayLength;
  $end = $end > $iTotalRecords ? $iTotalRecords : $end;

  for($i = $iDisplayStart, $j=0; $i < $end; $i++, $j++) {
    $item = $items[$i];
    $offer = Offer::getInstance($item->getOfferId());
    $status = ($offer->getStatus() == Offer::STATUS_DISABLED) ? "<div class='margin-top-5'><span class='label label-sm label-danger'>Отключен</span></div>" : '';
    $id = $item->getId();
    $space = Space::getInstance($item->getSpace());


    $records["data"][$j][] = $item->getName();
    $records["data"][$j][] = $item->getModified(true);
    $records["data"][$j][] = $space->getTypeAlias();
    if (empty($_REQUEST['oid'])) {
      $records["data"][$j][] = "<a href='/admin/offers/view/{$offer->getId()}' title='{$offer->getName()}'>{$offer->getName()}</a>{$status}";
    }
    $records["data"][$j][] = '<div style="max-width:250px; max-height:1.5em; overflow:hidden;"><a href="'.$item->getFullLink().'" target="_blank" class="link" data-id="'.$id.'">'.$item->getFullLink().'</a></div>';
    $records["data"][$j][] = '<div style="display:inline-block; position: relative;"><a href="#" class="btn btn-sm btn-outline green copy-link" data-id="'.$id.'" id="copy-'.$id.'" title="Копировать ссылку в буфер обмена"><i class="fa fa-copy"></i></a></div>' .
    '<a href="/admin/flows/'.$id.'" class="btn btn-sm btn-outline blue" title="Редактировать"><i class="fa fa-edit"></i></a>
    <a href="javascript:;" class="btn btn-sm btn-outline remove-item red" title="Удалить" data-id="'.$id.'"><i class="fa fa-trash"></i></a>';
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>