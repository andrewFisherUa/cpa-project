<?php

  $items = ShopPage::getAll(!User::isAdmin());
  if ( $items == false ) unset($items);

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

    $geo = ( $item->getGeo() == 1 ) ? "Да" : "Нет";
    $editable = ( $item->isEditable() ) ? "Да" : "Нет";

    $records["data"][] = array(
      $item->getId(),
      '<a href="/admin/shop/pages/'.$item->getLink().'">'.$item->getTitle().'</a>',
      $editable,
      $geo,
      $item->getCreated(true)
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>