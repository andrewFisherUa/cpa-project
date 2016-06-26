<?php

  if (isset($_REQUEST['type']) && $_REQUEST['type']!=-1) {
    $items = Categories::getByType($_REQUEST['type']);
  } else {
    $items = Categories::getAll();
  }

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

    $hidden = ( $item->isHidden() ) ? '<span class="label label-sm label-danger">Скрыта</span>' : '<span class="label label-sm label-success">Показана</span>';

    $actions = '<div class="btn-group">
                  <a class="btn btn-default btn-xs" href="javascript:;" data-toggle="dropdown" aria-expanded="false">
                  Действия <i class="fa fa-angle-down"></i>
                  </a>
                  <ul class="dropdown-menu" role="menu">
                    <li>
                      <a href="/admin/cats/edit/'.$item->getId().'">Редактировать</a>
                    </li>
                    <li>';

    if ($item->isHidden()) {
      $actions .= '<a href="javascript:;" class="action-btn" data-action="show" data-id="'.$item->getId().'">Показать</a>';
    } else {
      $actions .= '<a href="javascript:;" class="action-btn" data-action="hide" data-id="'.$item->getId().'">Скрыть</a>';
    }

    $actions .=  '</li>
                   <li>
                      <a href="javascript:;" class="action-btn" data-action="remove" data-id="'.$item->getId().'">Удалить</a>
                    </li>
                  </ul></div>';

    $records["data"][] = array(
      $item->getId(),
      " <a href='" . $item->getLink() . "' target='_blank'>" . $item->getName() . "</a>",
      $item->getType(),
      $item->getIcon(),
      '<a href="#" class="editable" data-id="'.$item->getId().'" data-type="text" data-pk="1" data-title="Введите вес">'.$item->getWeight().'</a>',
      $hidden,
      $item->getProductsCount('ua'),
      $item->getProductsCount('ru'),
      $item->getProductsCount('by'),
      $item->getProductsCount('kz'),
      $item->getProductsCount('uz'),
      $actions
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>