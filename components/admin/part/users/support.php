<?php

// страны
$query = "SELECT DISTINCT country_name FROM users WHERE country_name != '' ORDER BY country_name";
$countries = $GLOBALS['DB']->query($query)->fetchAll(PDO::FETCH_COLUMN);
$smarty->assign('countries', $countries);
$smarty->display( 'admin' . DS . 'users' . DS . 'support-index.tpl' );

enqueue_scripts( array(
    	"/assets/global/plugins/datatables/datatables.min.js",
	    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
	    "/assets/global/scripts/datatable.js",
	    "/assets/global/plugins/uniform/jquery.uniform.min.js",
	    "/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
		"/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
		"/misc/js/page-level/support-users.js"));

?>