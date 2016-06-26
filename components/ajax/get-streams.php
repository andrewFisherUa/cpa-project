<?php

  $filter = new Filter;

  $filters = [];

  if (isset($_REQUEST["flow_id"]) && $_REQUEST["flow_id"] > 0) {
    $filters['f_id'] = $filter->sanitize($_REQUEST["flow_id"], "int!"); 
  }

  if (isset($_REQUEST["offer_id"]) && $_REQUEST["offer_id"] > 0) {
    $filters['offer_id'] = $filter->sanitize($_REQUEST["offer_id"], "int!"); 
  }

  if (User::isAdmin() && isset($_REQUEST["user_id"]) && $_REQUEST["user_id"] > 0) {
    $filters['user_id'] = $filter->sanitize($_REQUEST["user_id"], "int!"); 
  }

  $items = Flow::getAll($filters);

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
    $item = $items[$i];

    $offer = Offer::getInstance($item->getOfferId());
    $user = User::getInstance($item->getUserId());
    $id = $item->getId();

    $status = ($offer->getStatus() == Offer::STATUS_DISABLED || $item->isDisabled()) ? "<div class='margin-top-5'><span class='label label-sm label-danger'>Отключен</span></div>" : '';

    $records["data"][] = array(
      $item->getName(),
      $item->getModified(true),
      "<a href='/admin/offers/view/{$offer->getId()}' title='{$offer->getName()}'>{$offer->getName()}</a>" . $status,
      $user->getLogin(),
      '<div style="max-width:250px; max-height:1.5em; overflow:hidden;"><a href="'.$item->getFullLink().'" target="_blank" class="link" data-id="'.$id.'">'.$item->getFullLink().'</a></div>',
     '<div style="display:inline-block; position: relative;"><a href="#" class="btn btn-sm btn-outline green copy-link" data-id="'.$id.'" id="copy-'.$id.'" title="Копировать ссылку в буфер обмена"><i class="fa fa-copy"></i></a></div>' .
      '<a href="/admin/flows/'.$id.'" class="btn btn-sm btn-outline blue" title="Редактировать"><i class="fa fa-edit"></i></a>');
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>