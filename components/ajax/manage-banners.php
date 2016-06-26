<?php

$action = $_POST["action"];
$response = array( "errors" => array(), "rows" => "" );

if ( $action == "get-cats" ) {
	$items = Categories::getAll();
	for ($i=0; $i < count($items); $i++ ) {
		$response["rows"] .= "<option value='".$items[$i]['link']."'>".$items[$i]['name']."</option>";
	}
	$response['selected'] = $items[0]['link'];
	$response['link'] = get_shop_url() . "/category/" . $response['selected'];
}

if ( $action == "get-products" ) {
	$items = Offer::getAll();
	for ($i=0; $i < count($items); $i++ ) {
		$response["rows"] .= "<option value='".$items[$i]->getId()."'>".$items[$i]->getId().": ".$items[$i]->getName()."</option>";
	}
	$response['selected'] = $items[0]->getId();
	$response['link'] = get_shop_url() . "/product/" . $response['selected'];
}

if ( $action == "get-link" ) {
	$type = ($_POST['type'] == 1) ? 'category' : 'product';
	$id = $_POST['id'];
	$response['link'] = get_shop_url() . "/{$type}/{$id}";
}

if ($action == "get-filtered") {
	$items = Banner::getFiltered($_POST);

	foreach ($items as $item) {
		$response['rows'] .= '<a class="thumb" href="#" data-id="'.$item->getId().'">
          						<img class="img-responsive" src="'.$item->getImage().'" alt="">
          						<div class="actions">
          						   <span class="btn btn-edit default" data-id="'.$item->getId().'">Редактировать</span>
          						   <span class="btn btn-remove red" data-id="'.$item->getId().'">Удалить</span>
          						</div>
      						  </a>';
	}
}

if ($action == "remove-banner") {
	Banner::delete($_POST["id"]);
}

if ($action == "get-form") {
	if ($_POST['id'] == 0){
		$banner = new Banner();
	} else {
		$banner = Banner::getInstance($_POST["id"]);

		if ($banner->getType() == "1") {
			foreach (Categories::getAll() as $item) {
				$response["rows"] .= "<option value='".$item['link']."'>".$item['name']."</option>";
			}
		}

		if ($banner->getType() == "2") {
			// product
			foreach (Offer::getAll() as $item) {
				$response["rows"] .= "<option value='".$item->getId()."'>".$item->getId().": ".$item->getName()."</option>";
			}
		}

		$smarty->assign("rows", $response["rows"]);
	}

	$response["data"] = array(
		"type" => $banner->getType(),
		"subject" => $banner->getSubject());

	$smarty->assign("banner", $banner);
	$response["form"] = $smarty->fetch('admin' . DS . 'shop' . DS . 'banners' . DS . 'ajax' . DS . 'edit-form.tpl');
}

echo json_encode($response);

?>