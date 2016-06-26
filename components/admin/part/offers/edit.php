<?php

$filter = new Filter;

if (isset($_POST['save-offer'])){ 

  $data = [
    "id" => $filter->sanitize($_POST['offer']['id'], "int"),
    "gid" => $filter->sanitize($_POST['offer']['gid'], "int"),
    "status" => $filter->sanitize($_POST['offer']['status'], ["string", "striptags"]),
    "available_in_offers" => $filter->sanitize($_POST['offer']['available_in_offers'], "int!"),
    "available_in_shop" => $filter->sanitize($_POST['offer']['available_in_shop'], "int!"),
    "type" => $filter->sanitize($_POST['offer']['type'], "int!"),
    "user_id" => $filter->sanitize($_POST['offer']['user_id'], "int!"),
    "description" => $_POST['offer']['description'],
    "name" => $filter->sanitize($_POST['offer']['name'], ["string", "striptags"]),
    "categories" => $filter->sanitize($_POST['offer']['categories'], "int"),
    "traffic_sources" => $filter->sanitize($_POST['offer']['traffic_sources'], "int"),
    "logo" => $filter->sanitize($_POST['offer']['logo'], ["string", "striptags"]),
  ];

  $audit_details = [
    "gid" => $data["gid"],
    "status" => $data["status"],
    "available_in_offers" => $data["available_in_offers"],
    "available_in_shop" => $data["available_in_shop"],
    "type" => $data["type"],
    "user_id" => $data["user_id"],
    "name" => $data["name"],
  ];

  $offer_id = $filter->sanitize($_REQUEST['b'], "int");

  if ($offer_id > 0) {
    $offer = Offer::getInstance($offer_id);
    $offer->setType($data["type"]);
    $offer->setGID($data['gid']);
    $offer->setOwnerId($data["user_id"]);
    $offer->setName($data["name"]);
    $offer->setDescription($data["description"]);
    $offer->setAvailableInShop($data['available_in_shop']);
    $offer->setAvailableInOffers($data['available_in_offers']);
  } else {
    $offer = new Offer($data);
  }

  // сохранение оффера
  $offer->save();

  $audit_details["id"] = $offer->getId();

  // Добавить вебмастеров если оффер приватный
  $wlist = $filter->sanitize($_POST["webmaster_list"], "int");
  $offer->savePrivateOfferWebmasters($wlist);

  // Обновить статус
  Offer::updStatus($offer->getId(), $data['status']);

  // Сохранение логотипа
  $main_image = $offer->getMainImage();
  if ($main_image["name"] != $data["logo"]) {
    $main_image = array(
      "id" => 0,
      "name" => $data['logo']);
    $offer->setMainImage($main_image);
    $offer->saveMainImage();
  }

  // сохранение цен
  $prices = unserialize($_SESSION["offer"][$offer->getId()]);
  $offer->unsetCountries();

  $stream_prices = [];

  foreach ($prices as $country_code=>$item) {
    $country = $filter->sanitize($country_code, ["string", "striptags"]);
    $qty = $filter->sanitize($item["qty"], "int");
    
    $price_id = $filter->sanitize($item["price"]["id"], "int");
    $price = $filter->sanitize($item["price"]["value"], "int");

    $offer->addCountry($country);
    $offer->setQty($qty, $country);
    $offer->addPrice($price_id, $price, $country);
    $offer->clearTargets();

    if (!empty($item["targets"])) {
      foreach ($item["targets"] as $target) {
        $temp = $filter->sanitize($target, "int");
        $offer->getPrice($country_code)->addTarget($temp);
        $audit_details["target_" . $target["id"] . "_" . $country . "_commission"] =  $temp["commission"];
        $audit_details["target_" . $target["id"] . "_" . $country . "_webmaster_commission"] =  $temp["webmaster_commission"];

        $stream_prices[] = [
          "offer_id" => $offer->getId(),
          "country_code" => $country,
          "price" => $price,
          "target_id" => $target["id"],
          "profit" => $temp["webmaster_commission"]
        ];

      }
    }

    $audit_details["price_" . $country] = $price;
    $audit_details["qty_" . $country] = $qty;
  }

  $offer->savePrices();

  //Flow::updatePrices($stream_prices);

  // Сохранение списка вебмастеров которым доступно изменение цен для оффера
  $stmt = $GLOBALS['DB']->prepare("DELETE FROM user_target WHERE offer_id = ?");
  $stmt->execute([
      $offer->getId()
  ]);

  if (!empty($_SESSION["offer"]["available_to"])) {
    // Доступ на изменение цены для подтвержденного заказа (target_id = 0)
    
    $w = [];
    foreach ($_SESSION["offer"]["available_to"] as $id=>$item) {
      $uid = $filter->sanitize($id, "int");
      $w[] = "({$uid}, 0, {$offer->getId()})";
    }

    if (!empty($w)) {
      $query = "INSERT INTO user_target(user_id, target_id, offer_id) VALUES " . implode(",", $w);
      $GLOBALS['DB']->exec($query);
    }
  }

  // Сохранение настроек
  $options = [
    "refprofit_type" => $filter->sanitize($_POST['options']["refprofit_type"], ["string", "striptags"]),
    "refprofit_level1" => $filter->sanitize($_POST['options']["refprofit_level1"], "int!"),
    "refprofit_level2" => $filter->sanitize($_POST['options']["refprofit_level2"], "int!"),
    "refprofit_level3" => $filter->sanitize($_POST['options']["refprofit_level3"], "int!"),
    "phone" => $filter->sanitize($_POST['options']["phone"], ["string", "striptags"]),
    "address" => $filter->sanitize($_POST['options']["phone"], ["string", "striptags"]),
    "delivery_time" => $filter->sanitize($_POST['options']["delivery_time"], "int"),
    "postclick_cookie" => $filter->sanitize($_POST['options']["postclick_cookie"], "int"),
    "order_processing_time" => $filter->sanitize($_POST['options']["order_processing_time"], "int"),
    "orders_per_day" => $filter->sanitize($_POST['options']["orders_per_day"], "int"),
  ];

  foreach ($options as $option=>$value) {
    $offer->getOptions()->clear($option);
    if (is_array($value)) {
      foreach ($value as $k=>$v) {
        $offer->setOption($option, $v, $k);

        $audit_details[$option . "_" . $k] = $v;
      }
    } else {
      $offer->setOption($option, $value);

      $audit_details[$option] = $value;
    }
  }

  $offer->getOptions()->save();

  // Обновление контента
  $query = "DELETE FROM offer_content WHERE offer_id = ?";
  $stmt = $GLOBALS['DB']->prepare($query);
  $stmt->execute([
    $offer->getId()
  ]);

  $v = []; $content = $_POST["contents"];

  foreach ($content["landings"] as $landing_id) {
    
    $lid = $filter->sanitize($landing_id, "int");

  	if (empty($content["blogs"][$lid])) {
      $bid = 0;
      $v[] = "({$offer->getId()}, {$lid}, {$bid})";
  	} else {
  		foreach($content["blogs"][$lid] as $blog_id) {
        $bid = $filter->sanitize($blog_id, "int");
        $v[] = "({$offer->getId()}, {$lid}, {$bid})";
      }
  	}
  }

  if (!empty($v)) {
    $query = "INSERT INTO offer_content(offer_id, landing_id, blog_id) VALUES " . implode(",", $v);
    $GLOBALS['DB']->exec($query);
  }

  // Сохранение категорий
  $stmt = $GLOBALS['DB']->prepare("DELETE FROM goods2categories WHERE g_id = ?");
  $stmt->execute([
    $offer->getId()
  ]);

  $v = []; 
  foreach ($data['categories'] as $c_id){
    $v[] = "({$offer->getId()}, {$c_id})";
  }

  if (!empty($v)) {
    $query = "INSERT INTO goods2categories (g_id, c_id) VALUES " . implode(",", $v);
    $GLOBALS['DB']->exec($query);
  }

  // Сохранить источники трафика
  $stmt = $GLOBALS['DB']->prepare("DELETE FROM goods2traffic WHERE g_id = ?");
  $stmt->execute([
    $offer->getId()
  ]);

  $v = []; 
  foreach ($data['traffic_sources'] as $tid){
    $v[] = "({$offer->getId()}, {$tid})";
  }

  if (!empty($v)) {
    $query = "INSERT INTO goods2traffic (g_id, t_id) VALUES " . implode(",", $v);
    $GLOBALS['DB']->exec($query);
  } 

  Audit::addRecord([
    "group" => "offer",
    "subgroup" => "edit",
    "action" => "Редактирование оффера `{$offer->getId()}`",
    "priority" => Audit::MEDIUM_PRIORITY,
    "details" => $audit_details
  ]);

  echo "<script>window.location = '/admin/offers/view/{$offer->getId()}' </script>";
}

if ( $_REQUEST['k'] == "edit" || $_REQUEST['k'] == "new") {

  if ($_REQUEST['k'] == "edit") {
    $offer_id = $filter->sanitize($_REQUEST['b'], "int");
    $offer = Offer::getInstance($offer_id);
  } else {
    $offer = new Offer();
  }

  if (is_null($offer)){
    echo "<script>window.location = '/admin/offers/' </script>";
  }

  /* Traffic Sources */
  $traffic_sources = $offer->getTrafficSources();
  $parts = ceil(count($traffic_sources) / 3);
  $traffic_sources = array_chunk($traffic_sources, $parts, TRUE);

  /* Countries */
  $countries = array();
  foreach ($offer->getCountries() as $c) {
    $countries[] = array( "code" => $c,
                          "name" => Country::getName($c));
  }

  /* options */
  $content_options = array();
  $options = $offer->getOptions();
  $data = Goods::getDefaultRefprofits();

  foreach ($offer->getCountries() as $c) {
    $country_name = Country::getName($c);
    $currency = Country::getCurrencyCode($c);

    $content_options[] = array(
      "code" => $c,
      "name" => $country_name,
      "address" => $options->get("address", $c),
      "phone" => $options->get("phone", $c),
      "delivery_time" => $options->get("delivery_time", $c));

    $refoptions[$c] = array(
      "code" => $c,
      "name" => $country_name,
      "currency" => $currency,
      "price" => $offer->getPrice($c)->getValue());

    $refoptions[$c]["type"] = $options->get('refprofit_type', $c) == "" ? $data[$c]["type"] : $options->get('refprofit_type', $c);
    $refoptions[$c]["level1"] = is_null($options->get('refprofit_level1', $c)) ? $data[$c]["levels"][1] : $options->get('refprofit_level1', $c);
    $refoptions[$c]["level2"] = is_null($options->get('refprofit_level2', $c)) ? $data[$c]["levels"][2] : $options->get('refprofit_level2', $c);
    $refoptions[$c]["level3"] = is_null($options->get('refprofit_level3', $c)) ? $data[$c]["levels"][3] : $options->get('refprofit_level3', $c);
  }

  $smarty->assign('options', $content_options);
  $options_list = $smarty->fetch('admin' . DS . 'offers' . DS . 'ajax' . DS . 'options-form.tpl');
  $smarty->assign('options_list', $options_list);
  $smarty->assign('refoptions', $refoptions);

  /* data for ajax */
  $data = array();
  foreach ($offer->getCountries() as $code) {
    $price = $offer->getPrice($code);

    $data[$code]["qty"] = $offer->getQty($code);
    $data[$code]["price"] = array( "id" => $price->getId(),
                                   "value" => $price->getValue());

    foreach ($price->getTargets() as $target) {
      $data[$code]["targets"][$target->getId()] = array("id" => $target->getId(),
                                                        "commission" => $target->getCommission(),
                                                        "max_price" => $target->getMaxPrice(),
                                                        "webmaster_commission" => $target->getWebmasterCommission());
    }
  }

  $_SESSION["offer"][$offer->getId()] = serialize($data);

  $_SESSION['content_groups'] = array();
  $query = 'SELECT cg.g_id, c.c_id, c.name
            FROM content_group AS cg RIGHT JOIN content AS c ON cg.c_id = c.c_id
            WHERE c.type="landing"';
  $stmt = $GLOBALS['DB']->query($query);
  if ($stmt->rowCount()){
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $_SESSION['content_groups'][$row['g_id']][$row['c_id']] = $row;
    }
  }

  // Список вебмастеров, которым достпно изменение цены оффера для цели подтвержденный заказ
  $query = "SELECT t1.login, t1.user_id as id FROM users as t1 INNER JOIN user_target AS t2 ON t1.user_id = t2.user_id WHERE t2.offer_id = :offer_id";
  $stmt = $GLOBALS['DB']->prepare($query);
  $stmt->bindParam(":offer_id", $offer->getId(), PDO::PARAM_INT);
  $stmt->execute();
  $selected_webmasters = array();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $selected_webmasters[$row["id"]] = $row;
  }
  $_SESSION["offer"]["available_to"] = $selected_webmasters;

  if ($offer->getType() == 3) {
    $smarty->assign('selected_webmasters', $offer->getPrivateOfferWebmasters());
  }

  $categories = array();
  $all_cats = array();
  $query = "SELECT c_id FROM goods2categories WHERE g_id = :id";
  $stmt = $GLOBALS['DB']->prepare($query);
  $stmt->bindParam(":id", $offer->getId(), PDO::PARAM_INT);
  $stmt->execute();
  while ($val = $stmt->fetchColumn()) {
    $all_cats[] = $val;
  }

  foreach (Categories::getByType("shop_category") as $cat) {
    $categories["shop_category"][] = array(
      "id" => $cat->getId(),
      "name" => $cat->getName(),
      "selected" => in_array($cat->getId(), $all_cats));
  }

  foreach (Categories::getByType("offer_category") as $cat) {
    $categories["offer_category"][] = array(
      "id" => $cat->getId(),
      "name" => $cat->getName(),
      "selected" => in_array($cat->getId(), $all_cats));
  }

  $smarty->assign('categories', $categories);
  $smarty->assign('webmasters', User::get_by_role_name("webmaster"));
  $smarty->assign('options', $options);
  $smarty->assign('main_image', $offer->getMainImage());
  $smarty->assign('targets', Target::getAll());
  $smarty->assign('countries', $countries);
  $smarty->assign('traffic_sources', $traffic_sources);
  $smarty->assign('partners', User::get_by_role_name("advertiser"));
  $smarty->assign('typeList', Offer::getTypeList());
  $smarty->assign('statusList', Offer::getStatusList());
  $smarty->assign("offer", $offer);
}

$smarty->display('admin' . DS . 'offers' . DS . 'edit.tpl');

enqueue_scripts(array(
  "/assets/global/plugins/datatables/datatables.min.js",
  "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
  "/assets/global/scripts/datatable.js",
  "/assets/global/plugins/uniform/jquery.uniform.min.js",
  "/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
  "/assets/global/plugins/select2/js/select2.min.js",
  "/assets/global/plugins/jstree/dist/jstree.min.js",
  "/misc/js/SimpleAjaxUploader.js",
  "/misc/fancybox/lib/jquery.mousewheel-3.0.6.pack.js",
  "/misc/fancybox/source/jquery.fancybox.pack.js?v=2.1.5",
  "/misc/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7",
  "/misc/js/page-level/singleOffer.js"
));

?>