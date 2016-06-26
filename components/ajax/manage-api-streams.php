<?php

$response = [
	"errors" => [],
	"success" => [],
];

$filter = new Filter;

$action = $filter->sanitize($_POST["action"], ["string", "striptags"]);

// Загрузка формы редактирования потока
if ($action == "get-prices") {

	$oid = $filter->sanitize($_POST['oid'], "int!");

	$query = "SELECT t1.country_code, t1.price, t2.comission_webmaster as profit, t2.max_price, t2.t_id as target_id, t3.name AS target_name, t4.name AS country_name, t4.currency_code as currency
			  FROM goods2countries AS t1, goods2targets AS t2, targets AS t3, country as t4
			  WHERE t1.country_code = t2.country_code AND 
				     t1.g_id = t2.g_id AND 
			         t2.t_id = t3.target_id AND
			         t1.country_code = t4.code AND
			         t1.g_id = ?";

	$stmt = $GLOBALS['DB']->prepare($query);
	$stmt->execute([
		$oid
	]);

	$prices = [];

	while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
		if (!array_key_exists($a['country_code'], $prices)) {
			$prices[$a['country_code']] = [
				"price" => (int) $a['price'],
				"recommended" => (int) $a['price'],
				"currency" => $a['currency'],
				"country_name" => $a['country_name'],
				"profit" => $a['profit'],
				"targets" => [],
			];
		}

		$prices[$a['country_code']]['targets'][$a['target_id']] = [
			"id" => $a['target_id'],
			"name" => $a['target_name'],
			"profit" => $a['profit'],
			"max" => $a['max_price'] - $a['price'] + $a['profit'],
			"selected" => false,
		];
	}

	$query = "SELECT * FROM user_target WHERE offer_id = :offer_id AND user_id = :user_id";
	$stmt = $GLOBALS['DB']->prepare($query);
	$stmt->bindParam(":offer_id", $oid, PDO::PARAM_INT);
	$stmt->bindParam(":user_id", User::get_current_user_id(), PDO::PARAM_INT);
	$stmt->execute();
	$can_edit_prices = $stmt->rowCount() > 0;
	$smarty->assign("editable", $can_edit_prices);	
	
	$smarty->assign('prices', $prices);
	$response['prices'] = $smarty->fetch('admin/api/ajax/prices.tpl');
}

// Сохранение потока
if ($action == "save") {
	
	$post = [
		"id" => $filter->sanitize($_POST["id"], "int!"),
		"offer_id" => $filter->sanitize($_POST["oid"], "int!"),
		"name" => $filter->sanitize($_POST["name"], ["string", "striptags"]),
		"prices" => []
	];

	foreach ($_POST['prices'] as $a=>$b) {
		$country_code = $filter->sanitize($a, ["string", "striptags"]);

		$post["prices"][$country_code] = [
			"target_id" => $filter->sanitize($b["target_id"], "int!"),
			"profit" => $filter->sanitize($b["profit"], "int!")
		];
	}

	if ($post["id"] == 0){
		$uid = User::get_current_user_id();
		$post["user_id"] = $uid;
	} else {
		$stream = Api_Stream::getById($GLOBALS['DB'], $post["id"]);
		$uid = $stream->getUserId();
	}

	// Проверка имени потока
	if (empty($post["name"])) {
		$response["errors"][] = "Введите название потока.";
	} else {
		$query = "SELECT id FROM api_streams WHERE name = :name AND user_id = :uid";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":name", $post["name"], PDO::PARAM_STR);
		$stmt->bindParam(":uid", $post["user_id"], PDO::PARAM_INT);
		$stmt->execute();

		if ($stmt->rowCount() > 0 && $stmt->fetchColumn() != $post["id"]) {
			$response["errors"][] = "Поток с таким названием уже существует";
		}
	}

	// Проверка оффера (при создании потока)
	if ($post["id"] == 0) {
		$sql = "SELECT g.id
			    FROM goods AS g INNER JOIN users2goods AS ug ON ug.g_id = g.id
			    WHERE g.available_in_offers = 1 AND 
			    	  g.offer_status = '" . Offer::STATUS_ACTIVE . "' AND 
			    	  ug.u_id = ? AND 
			    	  g.id = ?";
		$stmt = $GLOBALS['DB']->prepare($sql);
		$stmt->execute([
			$uid,
			$post["offer_id"]
		]);

		if ($stmt->rowCount() == 0) {
			$response["errors"][] = "Оффер `" . $post["offer_id"] . "` не найден.";
		}
	}
	

	if (count($response["errors"]) == 0) {
		if ($post["id"] == 0){
			$stream = new Api_Stream($GLOBALS['DB'], $post);
		} else {
			$stream->setName($post["name"]);
		}

		$stream->unsetPrices();

		foreach ($post['prices'] as $country_code => $data) {
			$stream->setProfit($country_code, $data["target_id"], $data["profit"]);
		}

		$r = $stream->save();
		if ($r === true) {
			if ($post["id"] == 0) {
				$response["key"] = $stream->getKey();
			}
		} else {
			$response["errors"] = $r;
		}
	}
}

if ($action == "delete") {
	$id = $filter->sanitize($_POST["id"], "int!");

	$query = "DELETE FROM api_streams WHERE id = ?";
	$stmt = $GLOBALS["DB"]->prepare($query);
	$stmt->execute([
		$id
	]);
}

echo json_encode($response);

?>