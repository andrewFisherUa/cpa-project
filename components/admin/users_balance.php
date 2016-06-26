<?php

$smarty->display( 'admin' . DS . 'partners' . DS . 'balance.tpl' );

enqueue_scripts( array(
    "/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/uniform/jquery.uniform.min.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
    "/assets/global/plugins/bootstrap-daterangepicker/moment.min.js",
    "/assets/global/plugins/fullcalendar/fullcalendar.min.js",
    "/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js" ));


?>