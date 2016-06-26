<?php

$faq = [];

$query = "SELECT r.*, a.* 
	      FROM rubrics AS r INNER JOIN articles AS a ON r.rubric_id = a.rubric_id 
	      WHERE a.status = 2
	      ORDER BY r.weight, a.weight";

$stmt = $GLOBALS["DB"]->query($query);

while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
	if (!array_key_exists($a["rubric_id"], $faq)) {
		$faq[$a["rubric_id"]]["rubric"] = [
			"name" => $a["name"],
			"rubric_id" => $a["rubric_id"],
		];
	}

	$faq[$a["rubric_id"]]["articles"][] = [
		"title" => $a["title"],
		"content" => $a["content"],
		"article_id" => $a["article_id"]
	];
}

$smarty->assign('faq', $faq);
$smarty->display( 'admin' . DS . 'faq' . DS . "articles.tpl" );

?>