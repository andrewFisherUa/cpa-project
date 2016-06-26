<?php

require_once PATH_ROOT . '/templates/admin/payments/index.php';

enqueue_scripts(array(
	"/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
	"/misc/js/page-level/payments.js"
));

?>