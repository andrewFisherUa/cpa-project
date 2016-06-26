<?php

if ( $_REQUEST['k'] == "edit" ) {

	$smarty->assign('rubrics', Article::get_rubrics());
	$smarty->assign('status', Article::$status_list);
	$smarty->display( 'admin' . DS . 'faq' . DS . "admin-index.tpl" );

	enqueue_scripts(array(
	  "/assets/global/plugins/datatables/datatables.min.js",
	  "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
	  "/assets/global/scripts/datatable.js",
	  "/assets/global/plugins/uniform/jquery.uniform.min.js",
	  "/assets/global/plugins/select2/js/select2.min.js",
	  "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
	  "/assets/global/plugins/bootstrap-daterangepicker/moment.min.js",
	  "/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js",
	  "/assets/global/plugins/bootstrap-summernote/summernote.min.js",
	  "/misc/js/page-level/faq.js"
	));
} 


if (empty($_REQUEST['k'])) {

	$faq = [];

	$query = "SELECT r.*, a.* 
		      FROM rubrics AS r INNER JOIN articles AS a ON r.rubric_id = a.rubric_id 
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
}

?>