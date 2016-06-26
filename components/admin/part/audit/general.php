<?php 

$stmt = $GLOBALS['DB']->query("SELECT user_id as id, login FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once PATH_ROOT . "/templates/admin/audit/index.php";

enqueue_scripts(array(
	"/assets/global/plugins/datatables/datatables.min.js",
	"/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
	"/misc/js/page-level/audit.js"
));

?>