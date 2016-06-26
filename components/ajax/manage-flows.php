<?php

$respond = array("errors"=>array(), "success"=>array());

$filter = new Filter;

$action = $filter->sanitize($_POST["action"], ["string", "striptags"]);

// Сохранение потока
if ($action == "save") {

	$post = [
		"id" => $filter->sanitize($_POST["id"], "int!"),
		"offer_id" => $filter->sanitize($_POST["offer_id"], "int!"),
		"name" => $filter->sanitize($_POST["name"], ["string", "striptags"]),
		"key" => $filter->sanitize($_POST["key"], ["string", "striptags"]),
		"space" => $filter->sanitize($_POST["space"], "int!"),
		"user_id" => $filter->sanitize($_POST["user_id"], "int!"),
		"landing_id" => $filter->sanitize($_POST["landing_id"], "int!"),
		"blog_id" => $filter->sanitize($_POST["blog_id"], "int!"),
		"subaccount_id" => $filter->sanitize($_POST["subaccount_id"], "int!"),
		"comebacker" => $filter->sanitize($_POST["comebacker"], "int!"),
		"subid1" => $filter->sanitize($_POST["subid1"], ["string", "striptags"]),
		"subid2" => $filter->sanitize($_POST["subid2"], ["string", "striptags"]),
		"subid3" => $filter->sanitize($_POST["subid3"], ["string", "striptags"]),
		"subid4" => $filter->sanitize($_POST["subid4"], ["string", "striptags"]),
		"subid5" => $filter->sanitize($_POST["subid5"], ["string", "striptags"]),
		"yandex_id" => $filter->sanitize($_POST["yandex_id"], ["string", "striptags"]),
		"google_id" => $filter->sanitize($_POST["google_id"], ["string", "striptags"]),
		"mail_id" => $filter->sanitize($_POST["mail_id"], "int"),
		"landing_alias" => $filter->sanitize($_POST["landing_alias"], ["string", "striptags"]),
		"blog_alias" => $filter->sanitize($_POST["blog_alias"], ["string", "striptags"]),
		"redirect_traffic" => $filter->sanitize($_POST["redirect_traffic"], ["string", "striptags"]),
		"trafficback" => $filter->sanitize($_POST["trafficback"], ["string", "striptags"]),
		"use_global_postback" => $filter->sanitize($_POST["use_global_postback"], "int!")
	];

    $postback = [];
    if (!empty($_POST['postback'])) {
    	$postback = [
			"use_global_postback" => $filter->sanitize($_POST['postback']["use_global_postback"], "int!"),
			"url" => $filter->sanitize($_POST['postback']['url'], ["string", "striptags"]),
			"send_on_create" => $filter->sanitize($_POST["postback"]["send_on_create"], "int!"),
			"send_on_confirm" => $filter->sanitize($_POST["postback"]["send_on_confirm"], "int!"),
			"send_on_cancel" => $filter->sanitize($_POST["postback"]["send_on_cancel"], "int!"),
		];	
    }

	foreach ($_POST['prices'] as $a) {
		$prices[] = [
			"country_code" => $filter->sanitize($a['country_code'], ["string", "striptags"]),
			"profit" => $filter->sanitize($a['profit'], "int!"),
			"target_id" => $filter->sanitize($a['target_id'], "int!"),
		];
	}

	$errors = [];
	if ($post["id"] == 0) {
		$flow = new Flow();
	} else {
		$flow = Flow::getInstance($post["id"]);
	}

	$flow->setOfferId($post['offer_id']);
	$flow->setName($post["name"]);
	$flow->setSpace($post["space"]);
	$flow->setUserId($post["user_id"]);
	$flow->setLandingId($post["landing_id"]);
	$flow->setSubaccountId($post["subaccount_id"]);
	$flow->setComebacker($post["comebacker"]);
	$flow->setSubid1($post["subid1"]);
	$flow->setSubid2($post["subid2"]);
	$flow->setSubid3($post["subid3"]);
	$flow->setSubid4($post["subid4"]);
	$flow->setSubid5($post["subid5"]);
	$flow->setYandexId($post["yandex_id"]);
	$flow->setGoogleId($post["google_id"]);
	$flow->setMailId($post["mail_id"]);
	$flow->setRedirectTraffic($post["redirect_traffic"]);
	$flow->setTrafficback($post["trafficback"]);
	$flow->setBlogId($post["blog_id"]);

	if (!empty($post['landing_alias'])) {
		if (!$flow->setLandingAlias($post['landing_alias'])) {
			$respond["errors"][] = "Псевдоним `".$post['landing_alias']."` занят. Выберите другое значение";
		}
	}
	if (!empty($post['blog_alias'])) {
		if (!$flow->setBlogAlias($post['blog_alias'])) {
			$respond["errors"][] = "Псевдоним `".$post['blog_alias']."` занят. Выберите другое значение";
		}
	}

	if (!empty($post['postback']['url'])) {
		$checkPostback = Postback::checkUrl($postback['url']);
		if ($checkPostback !== true) {
			$respond["errors"][] = "Неверные макросы в postback-ссылке: " . implode(", ", $checkPostback);
		}
	}

	if ($flow->nameExists()) {
		$respond["errors"][] = "Поток `{$flow->getName()}` уже существует";
	}

	if (count($respond["errors"]) == 0) {
		$flow->save();

		foreach ($prices as $price) {
			$flow->getPrices()->setTarget($price["target_id"], $price["country_code"]);
			$flow->getPrices()->setProfit($price["profit"], $price["country_code"]);
		}

		$flow->savePrices();

		$respond['flow_link'] = $flow->getFullLink();
		$respond["success"] = ($post["id"] > 0) ? "Изменения сохранены" : "Новый поток успешно создан";
		$content['landings'] = Offer::getContent($post['offer_id'], "landing");
        $smarty->assign('content', $content);

        if (!empty($postback['url'])) {
			$postback['user_id'] = $post["user_id"];
			$flow->setPostback($postback);
        }

		// Подготовка записи в логи
		$audit_record = [
			"group" => "stream",
			"subgroup" => "save",
			//"priority" => Audit::MEDIUM_PRIORITY,
			"action" => "Сохранение потока `" . $flow->getId() . "`",
			"details" => $post,
		];

		$audit_record["details"]["id"] = $flow->getId();

		if (!empty($postback)) {
			foreach ($postback as $a=>$b) {
				$audit_record["details"]["postback_" . $a] = $b;
			}
		}

		foreach ($prices as $a) {
			$audit_record["details"]["price_" . $a['country_code']] = $a['price'] . "target: " . $a["target_id"] . ", profit: " . $a['profit'];
		}

		Audit::addRecord($audit_record);
	}
}

// Добавление нового субаккаунта
if ($action == "add-account") {

	$uid = $filter->sanitize($_POST['user_id'], "int!");
	$name = $filter->sanitize($_POST["subaccount"], ["string", "striptags"]);

	$r = Flow::addSubaccount($uid, $name);
    if ($r == false) {
        $respond["errors"] = "Такой субаккаунт уже существует";
    } else {
    	$respond["success"] = "Новый SUB аккаунт был добавлен";
        $subs = Flow::getUserSubaccounts($uid);
        $respond["rows"] = "<option value='0'> </option>";
        foreach ($subs as $sub) {
            $respond["rows"] .= "<option value='".$sub['s_id']."'>" . $sub["name"] . "</option>";
        }

        Audit::addRecord([
        	"group" => "stream",
        	"subgroup" => "create_subaccount",
        	"action" => "Добавление субаккаунта `{$name}` для пользователя `{$uid}`",
        ]);
    }
}

// Удаление потока
if ($action == "remove") {
	$id = $filter->sanitize($_POST['id'], "int!");
	Flow::delete($id, true);

	Audit::addRecord([
		"group" => "stream",
		"subgroup" => "delete",
		"action" => "Удаление потока `{$id}`",
	]);
}

if ($action == "get-preview") {
	$content_id = $filter->sanitize($_POST['content_id'], "int!");
	$offer_id = $filter->sanitize($_POST['offer_id'], "int!");
	$target_url = $filter->sanitize($_POST['target_url'], ["string", "striptags"]);

	Preview_Generator::create_preview(Content::getInstance($content_id), $offer_id, $target_url);
	$respond["link"] = "/content/preview/{$content_id}/{$offer_id}.html";
}

if ( $action == "get-blogs" ) {
	$base_url = Content::BLOGS_URL;
	$landing_id = $filter->sanitize($_POST['landing_id'], "int!");

	$rows = Blog::get_by_landing($landing_id);
}

// Получить лендинги подключенные к офферу
if ( $action == "get-landings" ) {
	$base_url = Content::LANDINGS_URL;
	$offer_id = $filter->sanitize($_POST['offer_id'], "int!");

	$rows = Offer::getContent($offer_id, "landing");

	$flow = new Flow([
		"offer_id" => $offer_id
	]);

	$respond['prices'] = $flow->getPrices()->getTable();
}

if ( $action == "get-blogs" || $action == "get-landings" ) {

	$offer_id = $filter->sanitize($_POST['offer_id'], "int!");

	foreach ($rows as $row) {
		$p = Content::get_preview_link($row['c_id'], $offer_id);
		$name = "flow_" . $row['type'] . "[]";
		$respond["rows"] .=
		'<tr>
	        <td>
	        <label><input type="radio" name="'.$name.'" value="' . $row['c_id'] . '" id="content-'.$row['c_id'].'"><a href="'.$p.'" target="_blank">'.$row['name'].'</a></label>
	        </td>
	        <td class="text-center">-</td>
	        <td class="text-center">-</td>
	      </tr>';
	}
}

echo json_encode($respond);

die();

?>