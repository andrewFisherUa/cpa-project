<?php

$smarty->display( 'admin' . DS . 'balance' . DS . 'transfer.tpl' );

enqueue_scripts(array(
	"/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
	"/misc/js/page-level/transfer.js"
));

?>