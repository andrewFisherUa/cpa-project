<?php

  if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {

    $action = $_REQUEST['customActionName'];

    $ids = "(" . implode(",", $_REQUEST['id']) . ")";

    $param = null;
    $query = "";

    switch ($action) {
      case "publish" : $query = "UPDATE comments SET status = :param WHERE comment_id IN {$ids}";
                       $param = Comment::STATUS_PUBLISHED;
                       break;
      case "reject"  : $query = "UPDATE comments SET status = :param WHERE comment_id IN {$ids}";
                       $param = Comment::STATUS_REJECTED;
                       break;
      case "archive" : $query = "UPDATE comments SET status = :param WHERE comment_id IN {$ids}";
                       $param = Comment::STATUS_ARCHIVE;
                       break;
      case "delete"  : $query = "DELETE FROM comments WHERE comment_id IN {$ids}";
                       break;
      case "view"    : $query = "UPDATE comments SET viewed = 1 WHERE comment_id IN {$ids}";
                       break;
    }

    if (!empty($query)) {
      $stmt = $GLOBALS['DB']->prepare($query);
      if (!is_null($param)) {
        $stmt->bindParam(":param", $param, PDO::PARAM_INT);
      }
      $stmt->execute();
    }
  }

  $class = array( 3 => array("class"=>"warning", "label" => "Архив" ),
                  1 => array("class"=>"info", "label" => "На модерации" ),
                  2 => array("class"=>"success", "label" => "Опубликован" ),
                  4 => array("class"=>"danger", "label" => "Отклонен" ));

  if(!empty($_REQUEST['date_from'])){

    $data = explode("/", $_REQUEST['date_from']);
    $_REQUEST['date_from'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]);
  }

  if(!empty($_REQUEST['date_to'])){
    $data = explode("/", $_REQUEST['date_to']);
    $_REQUEST['date_to'] = mktime(0, 0, 0, $data[1], $data[0], $data[2]);
  }

  $items = Comment::getFiltered($_REQUEST);
  $comments = array();

  $iTotalRecords = count( $items );
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);

  $records = array("data" => array());
  $temp = array();

  $end = $iDisplayStart + $iDisplayLength;
  $end = $end > $iTotalRecords ? $iTotalRecords : $end;

  for ($i = $iDisplayStart; $i < $end; $i++) {
    $item = $items[$i];
    $comments[$item->getId()] = $item;

    $temp[] = array(
      '',
      $item->getId(),
      $item->getCreated(),
      $item->getStatus(),
      $item->getName(),
      $item->getContent(),
      $item->getShopDomen(),
      $item->getGoodName(),
      $item->getScore(),
    );
  }

  $order = $_REQUEST['order'][0];
  if ($order['column'] != 0) {
    $temp = sortByColumn($temp, $order['dir'], $order['column']);
  }

  for ($i=0; $i < count($temp); $i++) {
    $item = $comments[$temp[$i][1]];
    $id = $item->getId();
    $status = $item->getStatus();
    $statusText = "<span class='label label-sm label-".$class[$status]['class']."'>
                    <a href='javascript:;' id='status" . $id . "' class='editable' data-id='". $id ."' data-type='select' data-pk='1' data-value='" . $status . "' data-original-title='Статус отзыва'>". $class[$status]['label'] ."</a>
                    </span>";

    $content = "<a href='#commentModal' data-comment='".$id."'>" . $item->getContent() . "</a>";
    if ($item->hasReply()) {
      $content .= "<hr />" . $item->getReply();
    }

    $domen = $item->getShopDomen();
    $shopUrl = get_shop_url($domen);

    $records["data"][] = array(
       '<input type="checkbox" class="check" name="id[]" value="'.$item->getId().'" data-viewed="'. (int) $item->isViewed().'">',
       $item->getId(),
       $item->getCreated(true),
       $statusText,
       $item->getName(),
       $content,
       "<a href='{$shopUrl}' target='_blank'><span class='flag flag-".$item->getCountryCode()."'></span> ".$item->getShopDomen()."</a>",
       "<a href='{$shopUrl}/product/".$item->getGoodId()."' target='_blank'>".$item->getGoodName()."</a>",
       $item->getScore(),
    );
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);

?>