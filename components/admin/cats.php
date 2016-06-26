<?php

$filter = new Filter;

$messages = array();
$template = "index";

if (isset($_POST['id'])) {

	$raw = [
		"id" => $filter->sanitize($_POST["id"], "int"),
	    "sub" => $filter->sanitize($_POST["sub"], "int"),
	    "sub_order" => $filter->sanitize($_POST["sub_order"], "int"),
	    "name" => $filter->sanitize($_POST["name"], ["string", "striptags"]),
	    "link" => $filter->sanitize($_POST["link"], ["string", "striptags"]),
	    "type" => $filter->sanitize($_POST["type"], ["string", "striptags"]),
	    "hidden" => $filter->sanitize($_POST["hidden"], "int!"),
	    "css" => $filter->sanitize($_POST["css"], ["string", "striptags"]),
	    "heading" => $filter->sanitize($_POST["heading"], ["string", "striptags"]),
	    "cattext" => $filter->sanitize($_POST["cattext"], ["string", "striptags"]),
	    "seo" => $filter->sanitize($_POST["seo"], ["string", "striptags"]),
	    "title" => $filter->sanitize($_POST["title"], ["string", "striptags"]),
	    "description" => $filter->sanitize($_POST["description"], ["string", "striptags"]),
	    "keywords" => $filter->sanitize($_POST["keywords"], ["string", "striptags"]),
	    "mainimg" => $filter->sanitize($_POST["mainimg"], ["string", "striptags"]),
	    "topimg" => $filter->sanitize($_POST["topimg"], ["string", "striptags"]),
	];
	
	$cat = new Categories($raw);
	$cat->save();

	Audit::addRecord([
		"group" => "category",
		"subgroup" => "edit",
		"action" => "Создание / Редактирование категории `{$cat->getId()}`: `{$cat->getName()}`",
	]);

	unset($_POST);
	echo "<script>window.location = '/admin/cats/' </script>";
}

if ( $_REQUEST['k'] == 'edit' && $_REQUEST['b'] != '' ) {
	$id = $filter->sanitize($_REQUEST['b'], "int");
	
	$smarty->assign('cat', Categories::getInstance($id));
}

if ($_REQUEST['k'] == 'new') {
	$smarty->assign('cat', new Categories());
}

if ( $_REQUEST['k'] == 'new' ||  $_REQUEST['k'] == 'edit') {
	$template = "edit";
	$smarty->assign('icons', range(1,12));
}

if ( $_REQUEST['k'] == '' ) {
	enqueue_scripts(array(
		"/assets/global/plugins/datatables/datatables.min.js",
		"/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
		"/assets/global/scripts/datatable.js",
		"/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"));
}

$smarty->assign('messages', $messages);
$smarty->display( 'admin' . DS . 'cats' . DS . $template . '.tpl' );

enqueue_scripts(array(
	"/assets/global/plugins/uniform/jquery.uniform.min.js",
	"/misc/js/SimpleAjaxUploader.js",
	"/misc/js/page-level/cats.js"
));

?>