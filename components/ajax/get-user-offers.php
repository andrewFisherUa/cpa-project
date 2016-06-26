<?php

  $filter = new Filter;

  $filters = [];

  if ( isset($_REQUEST["country_code"]) && $_REQUEST["country_code"] != -1 ) {
    $filters["country_code"] = $filter->sanitize($_REQUEST["country_code"], ["string", "striptags"]);
  }

  if ( isset($_REQUEST["category"]) && $_REQUEST["category"] != -1 ) {
    $filters["category"] = $filter->sanitize($_REQUEST["category"], "int");
  }

  if ( isset($_REQUEST["id"]) && $_REQUEST["id"] != -1 ) {
    $filters["id"] = $filter->sanitize($_REQUEST["id"], "int");
  }

  $uID = User::get_current_user_id();

  $filters['status'] = [
      Offer::STATUS_ACTIVE,
      Offer::STATUS_DISABLED
  ]; // Вебмастерам показывать только активные офферы

  $filters['available_in_offers'] = true;

  $items = Offer::getFiltered(Offer::getbyUID($uID), $filters);

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

    $buttons = ''; $catList='';
    $id = $item->getId();

    foreach ( $item->getCategories() as $cat) {
      $catList .= '<li><span class="badge badge-roundless">'.$cat['name'].'</span></li>';
    }

    if ($item->getStatus() == Offer::STATUS_ACTIVE) {
      if ( Privileged_User::has_role( $uID, "webmaster" ) ) {
        $buttons = "<a href='/admin/offers/view/{$id}/#streams' class='btn btn-sm awesome-green margin-bottom-5'><i class='icon-link'></i> Получить ссылку</a>
        <button class='btn default btn-sm remove-user-good' data-g_id='{$id}'><i class='fa fa-times'></i> Отключить</button>";
      }
    } else {
      $buttons = "<span class='disabled-offer'></span>";
    }

    if ($item->getStatus() == Offer::STATUS_DISABLED) {
      $ribbon = '<div class="ribbon ribbon-border-hor ribbon-clip ribbon-color-danger uppercase"><div class="ribbon-sub ribbon-clip"></div>Отключен</div>';
    } else {
      $ribbon = "";
    }

    $records["data"][] = array(
     "<a class='fancybox' href='/admin/offers/view/{$id}' >{$ribbon}<img src='{$item->getMainImagePath()}' class='img-responsive'></a>",
     "<a class='title' href='/admin/offers/view/{$id}' >{$item->getName()}</a><ul class='offer-cats'>{$catList}</ul>",
      $item->getEpc(),
      $item->getCr(),
      $item->getPricesView("short"),
      $buttons
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

  echo json_encode($records);
?>