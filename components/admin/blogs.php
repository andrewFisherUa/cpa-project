<?php

$filter = new Filter;

$template = "blogs";

// Edit blog
if ( $_REQUEST['k'] == 'edit' && $_REQUEST['b'] ) {
	$template = "edit-content";

	$id = $filter->sanitize($_REQUEST['b'], "int");
	$content = Content::get_by_id( $id );

    if ( $content ) {
        $content["landings"] = Blog::get_landings( $content['c_id'] );
        $content["group"] = Landing::get_content_group( $content["landing"]['c_id'] );
   		$landings = array_column( $content["landings"], "c_id" );
        $smarty->assign('content', $content );
        $smarty->assign( 'groups', Content::get_groups() );
        $smarty->assign('type_link', get_site_url() . '/blogs');
		$form = $smarty->fetch( 'admin' . DS . 'content' . DS . 'ajax' . DS . 'single-edit.tpl' );
		$smarty->assign('form', $form);
    } else {
    	$message = "Не удалось найти контент с ID `{$id}`";
    }
}

if ( $_REQUEST['k'] == "new" || $_REQUEST['k'] == "edit" ) {
	$stmt = $GLOBALS['DB']->query('SELECT cg.g_id, c.c_id, c.name
                                   FROM content_group AS cg RIGHT JOIN content AS c ON cg.c_id = c.c_id
                                   WHERE c.type="landing"');
    $rows = $stmt->fetchAll( PDO::FETCH_ASSOC );
    $_SESSION['content_groups'] = array();
    foreach ( $rows as $row ) {
    	if ( !in_array($row['c_id'], $landings) ) {
    		$_SESSION['content_groups'][$row['g_id']][$row['c_id']] = $row;
    	}
    }
}

if ( $_REQUEST['k'] == "new" ) {
	$template = "new";

	$content = array( "type" => "blog" );
	$groups = Content::get_groups();
    $smarty->assign( 'groups', $groups );
    $smarty->assign( "landings", Landing::get_by_group( $groups[0]['g_id'] ) );
    $smarty->assign('content', $content );
    $smarty->assign('type_link', get_site_url() . '/blogs');
	$form = $smarty->fetch( 'admin' . DS . 'content' . DS . 'ajax' . DS . 'single-edit.tpl' );
	$smarty->assign('form', $form);
}

if (empty($_REQUEST['k'])) {
    $query = "SELECT DISTINCT a.c_id, a.name 
              FROM content AS a INNER JOIN landing_blog AS b ON a.c_id = b.landing_id";
    $stmt = $GLOBALS['DB']->query($query);
    $smarty->assign('landings', $stmt->fetchAll(PDO::FETCH_ASSOC));

    $stmt = $GLOBALS['DB']->query("SELECT name, link FROM content WHERE type = 'blog'");
    $smarty->assign('content', $stmt->fetchAll(PDO::FETCH_ASSOC));
}

$smarty->assign('type', 'blog');
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