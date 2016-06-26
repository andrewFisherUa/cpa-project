<?php

$response = [
	"errors" => [],
];


$filter = new Filter;
$action = $filter->sanitize($_POST["action"], ["string", "striptags"]);

// Удаление магазина
if ( $action == "save" ) {

    $post = [
    	"url" => $filter->sanitize($_POST["url"], ["string", "striptags"]),
    	"user_id" => $filter->sanitize($_POST["user_id"], "int!"),
    	"send_on_create" => $filter->sanitize($_POST["send_on_create"], "int!"),
    	"send_on_confirm" => $filter->sanitize($_POST["send_on_confirm"], "int!"),
    	"send_on_cancel" => $filter->sanitize($_POST["send_on_cancel"], "int!"),
    ];

	$checkUrl = Postback::checkUrl($post['url']);
	if ($checkUrl === true) {
		$p = Postback::create($post);
		if ($p == true) {
			Audit::addRecord([
				"group" => "postback",
				"subgroup" => "create",
				"action" => "Сохранение postback ссылки",
				"details" => $post
			]);
		} else {
			$response["errors"] = $p;
		}
	} else {
		$response["errors"][] = "Неверные макросы в ссылке postback: " . implode(", ", $checkUrl);
	}

}

echo json_encode( $response );

?>