<?php 

$filter = new Filter;
$uid = $filter->sanitize($_REQUEST['b'], "int!");

$countries = $GLOBALS['DB']->query("SELECT DISTINCT country_name FROM user_audit")->fetchAll(PDO::FETCH_COLUMN);

require_once PATH_ROOT . "/templates/admin/audit/user.php";

enqueue_scripts(array(
    "/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/misc/js/page-level/user-audit.js"
));

?>