<?php

  $class = [
    "moderation" => "info", 
    "active" => "success", 
    "disabled" => "danger", 
    "archive" => "warning"
  ];
  
  $accepted_filters = [
    'country_code' => 'str', 
    'target' => 'int', 
    'status' => 'str', 
    'category' => 'int', 
    'id' => 'int'
  ];
  
  $filter = new Filter;
  $filters = [];

  foreach ($accepted_filters as $a=>$b) {
    if (isset($_REQUEST[$a]) && $_REQUEST[$a] != -1) {
      if ($b == "int") {
        $filters[$a] = $filter->sanitize($_REQUEST[$a], "int");
      }

      if ($b == "str") {
        $filters[$a] = $filter->sanitize($_REQUEST[$a], ["string", "striptags"]);
      }
    }
  }

  $user_id = User::get_current_user_id();

  $isAdmin = User::isAdmin();
  $items = Offer::getAll();
  if ( !$isAdmin ) {

    $filters['status'] = [
      Offer::STATUS_ACTIVE,
      Offer::STATUS_DISABLED
    ]; // Вебмастерам показывать только активные офферы

    $filters['available_in_offers'] = true;
    $filters['available_to_user'] = $user_id;
  }

  $items = Offer::getFiltered($items, $filters);
  $iTotalRecords = count($items);
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $records = array();
  $records["data"] = array();

  $end = $iDisplayStart + $iDisplayLength;
  $end = $end > $iTotalRecords ? $iTotalRecords : $end;

  for ($i = $iDisplayStart; $i < $end; $i++) {
    $item = $items[$i];

    $isConnected = $item->connectedBy($user_id);

    $id = $item->getId();
    $buttons = ''; $catList='';

    if ( $isAdmin ) {
      $buttons = "<span class='label label-sm label-".$class[$item->getStatus()]."'>
                  <a href='javascript:;' id='status{$id}' class='status' data-subject='{$id}' data-type='select' data-pk='1' data-value='{$item->getStatus()}' data-original-title='Статус оффера'>{$item->getStatusLabel()}</a></span>
                  <a class='btn btn-sm default margin-top-5' href='/admin/offers/edit/{$id}'><i class='icon-pencil'></i> Редактировать</a>";
    } else {
      $user_id = User::get_current_user_id();

      if ($item->getStatus() == Offer::STATUS_ACTIVE) {
        if ( Privileged_User::has_role($user_id, "webmaster") ) {
          if ( $isConnected ) {
            $buttons = "<a href='/admin/offers/view/{$id}/#streams' class='btn btn-sm awesome-green margin-bottom-5'><i class='icon-link'></i> Получить ссылку</a>
            <button class='btn default btn-sm remove-user-good' data-g_id='{$id}'><i class='fa fa-times'></i> Отключить</button>";
          } else {
            $o = new GoodsOptions($id);
            $show_rules = $o->get("show_rules");
            $buttons = "<button class='btn default btn-sm green add-user-good' data-g_id='{$id}' data-rules='{$show_rules}'><i class='fa fa-plus'></i> Подключить</button>";
          }
        }
      } else {
        $buttons = "<span class='disabled-offer'></span>";
      }
    }

    foreach ( $item->getCategories() as $cat) {
      $catList .= '<li><span class="badge badge-roundless">'.$cat['name'].'</span></li>';
    }

    if ($item->getStatus() == Offer::STATUS_DISABLED) {
      $ribbon = '<div class="ribbon ribbon-border-hor ribbon-clip ribbon-color-danger uppercase"><div class="ribbon-sub ribbon-clip"></div>Отключен</div>';
    } else {
      $ribbon = ($item->isTop()) ? "<div class='ribbon top-offer-ribbon'><i class='fa fa-star'></i></div>" : "";
    }

    $name = ( $isAdmin ) ? "# {$item->getId()} {$item->getName()}" : "{$item->getName()}";

    $records["data"][] = array(
     "<a class='fancybox' href='/admin/offers/view/{$item->getId()}'>{$ribbon}<img src='{$item->getMainImagePath()}' class='img-responsive'>
     </a>",
     "<a class='title' href='/admin/offers/view/{$item->getId()}'>{$name}</a><ul class='offer-cats'>{$catList}</ul>",
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