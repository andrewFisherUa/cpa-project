<?php

$filter = new Filter;

$template = "landings";

// Edit landing
if ( $_REQUEST['k'] == 'edit' && $_REQUEST['b'] ) {
	$template = "edit-content";

	$id = $filter->sanitize($_REQUEST['b'], "int!");
	$groups = Content::get_groups();
	$content = Content::get_by_id( $id );
	if ( $content ) {
		$selected = array_column( Landing::get_content_groups( $content['c_id'] ), 'g_id');
		foreach ( $groups as &$group ) {
			$group["checked"] = in_array( $group['g_id'], $selected );
		}
		$smarty->assign( 'groups', $groups );
		$smarty->assign('content', $content );
		$smarty->assign('type_link', get_site_url() . '/landings');
		$form = $smarty->fetch( 'admin' . DS . 'content' . DS . 'ajax' . DS . 'single-edit.tpl' );
		$smarty->assign('form', $form);
	} else {
		$message = "Не удалось найти контент с ID `{$id}`";
	}
}

if ( $_REQUEST['k'] == "new" ) {
	$template = "new";

	$content = array( "type" => "landing" );
	$groups = Content::get_groups();
    $smarty->assign( 'groups', $groups );
    $smarty->assign('content', $content );
    $smarty->assign('type_link', get_site_url() . '/landings');
	$form = $smarty->fetch( 'admin' . DS . 'content' . DS . 'ajax' . DS . 'single-edit.tpl' );
	$smarty->assign('form', $form);
}

if (empty($_REQUEST['k'])) {
	$smarty->assign('groups', Content::get_groups());
	$stmt = $GLOBALS['DB']->query("SELECT name, link FROM content WHERE type = 'landing'");
	$smarty->assign('content', $stmt->fetchAll(PDO::FETCH_ASSOC));
}

$smarty->assign('type', 'landing');
$smarty->assign('message', $message);
$smarty->display( 'admin' . DS . 'content' . DS . $template . '.tpl' );

enqueue_scripts( array(
        "/assets/global/plugins/datatables/datatables.min.js",
        "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
        "/assets/global/scripts/datatable.js",
        "/assets/global/plugins/uniform/jquery.uniform.min.js",
        "/assets/global/plugins/select2/js/select2.min.js",
        "/misc/js/page-level/content.js" ));

?>