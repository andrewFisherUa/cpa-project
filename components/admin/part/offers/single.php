<?php

$offer = Offer::getInstance($_REQUEST['b']);

$error = false;
$errorMessage = "";

if ($offer === false) {
  $error = true;
  $errorMessage = "Оффер не найден";
} else {
  $user_id = User::get_current_user_id();

  if (!User::isAdmin() && !$offer->isAvailableToUser($user_id)) {
    $error = true;
    $errorMessage = "Оффер не доступен";
  }
}

if ($error === false){
  // countries
  $countries = array();
  foreach ($offer->getCountries() as $c) {
    $countries[] = array("code" => $c,
                         "name" => Country::getName($c));
  }

  // targets
  $targets = array();
  $query = "SELECT t1.t_id, t1.country_code as code, t1.comission_webmaster AS value, t2.name
            FROM goods2targets AS t1 INNER JOIN targets AS t2 ON t1.t_id = t2.target_id
            WHERE g_id = :id";
  $stmt = $GLOBALS['DB']->prepare($query);
  $stmt->bindParam(":id", $offer->getId(), PDO::PARAM_INT);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $currency = Country::getCurrency($row["code"]);
    $targets[$row["t_id"]]["name"] = $row["name"];
    $targets[$row["t_id"]]["values"][$row["code"]] = "{$row['value']}&nbsp;{$currency}";
  }

  $content['landings'] = Offer::getContent($offer->getId(), "landing");
  foreach($content['landings'] as &$c){
    $c["preview"] = Content::get_preview_link($c['c_id'], $offer->getId());
  }

  $content['blogs'] = Offer::getContent($offer->getId(), "blog");
  foreach($content['blogs'] as &$c){
    $c["preview"] = Content::get_preview_link($c['c_id'], $offer->getId());
  }

  $smarty->assign('content', $content);

  if (!User::isAdmin()){
    $uID = User::get_current_user_id();

    if ($offer->getStatus() == Offer::STATUS_ACTIVE) {
      $is_connected = $offer->connectedBy($uID);
      $can_be_connected = $offer->canBeConnectedBy($uID);
    } else {
      $is_connected = false;
      $can_be_connected = false;
    }

    $smarty->assign('owner', $uID);
    $smarty->assign('canBeConnected', $can_be_connected);
    $smarty->assign('is_connected', $is_connected);

    if ($is_connected) {
      $smarty->assign('flows', Flow::getByUID($uID, $offer->getId()));
      $smarty->assign('subaccounts', Flow::getUserSubaccounts($uID));
      $flow = new Flow(array(
        'user_id' => $uID,
        'offer_id' => $offer->getId()
      ));

      $smarty->assign("flow", $flow);
      $smarty->assign("postback", $flow->getPostback());
      $smarty->assign('prices', $flow->getPrices()->getTable());
      $smarty->assign('spaces', Space::getAll([
        "user_id" => User::get_current_user_id(),
        "status" => Space::STATUS_APPROVED
      ]));

      $flow_form = $smarty->fetch('admin' . DS . 'flows' . DS . 'ajax' . DS . 'edit-flow-form.tpl');
      $smarty->assign('flow_form', $flow_form);
    }
  }

  /*
  $offer_news = Notify::get_offers_news($offer->getId());
  foreach ( $offer_news as &$n ) {
    $n['type_icon'] = Notify::get_type_icon( $n['type'] );
  }
  $smarty->assign('offer_news', $offer_news);
  */
  $news = News::getAll($GLOBALS['DB'], $params, ($page-1)*$length , $length);

  $icons_name_jpg = array(
              1 => 'new_offer',     2 => 'offera_suspension',
              3 => 'offer_change',  4 => 'new_landing',
              5 => 'system_news',   6 => 'important'
            );
  $get_html_news = '';
  foreach ($news as $v) {

        if ($v->getGoodId() == $offer->getId()){

          $url_for_icons = $icons_name_jpg[$v->getTypeId()];

          $get_html_news .= '<div class="timeline-item">';
          $get_html_news .=   '<div class="timeline-badge">';
          $get_html_news .=     '<div class="timeline_basic timeline_' . $url_for_icons . '"></div>';
          $get_html_news .=    '</div>';
          $get_html_news .=    '<div class="timeline-body custom_bg_single bg_border_' . $url_for_icons .'">';
          $get_html_news .=      '<div class="timeline-body-arrow custom_arrow_'.  $url_for_icons . '"> </div>';
          $get_html_news .=    '<div class="timeline-body-head">';
          $get_html_news .=       '<div class="timeline-body-head-caption">';
          $get_html_news .=         '<a href="javascript:;" class="timeline-body-title font-blue-madison">' .  $v->getTitle() . '</a>';
          $get_html_news .=         '<span class="timeline-body-time font-grey-cascade">' . date("d.m.Y H:i", $v->getActivateTime()) . '</span>';
          $get_html_news .=       '</div>';
          $get_html_news .=     '</div>';
          $get_html_news .=    '<div class="timeline-body-content">';
          $get_html_news .=         '<span class="font-grey-cascade my_cascade">' .  $v->getContent() . '</span>';
          $get_html_news .=    '</div>';
          $get_html_news .=   '</div>';
          $get_html_news .=  '</div>';
        //  $get_html_news .=  '<hr />';

          htmlspecialchars ($get_html_news);
        }
}

  $smarty->assign('options', $offer->getOptions());
  $smarty->assign('countries', $countries);
  $smarty->assign('targets', $targets);
  $smarty->assign('offer', $offer);
  $smarty->assign('get_html_news', $get_html_news);
  $smarty->display('admin' . DS . 'offers' . DS . 'view.tpl');

  enqueue_scripts(array(
    "/misc/fancybox/lib/jquery.mousewheel-3.0.6.pack.js",
    "/misc/fancybox/source/jquery.fancybox.pack.js?v=2.1.5",
    "/misc/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7",
    "/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/uniform/jquery.uniform.min.js",
    "/assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js",
    "/assets/global/plugins/select2/js/select2.min.js",
    "/misc/plugins/jquery-zclip-master/jquery.zclip.js",
    "/misc/js/page-level/flows.js" ));
} else {
  echo "<div class='alert alert-danger'>" . $errorMessage . "</div>";
}



?>
