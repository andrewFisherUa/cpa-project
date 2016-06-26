<?php

$smarty->display( 'admin' . DS . 'content' . DS . 'groups.tpl' );

enqueue_scripts( array(
        "/assets/global/plugins/datatables/datatables.min.js",
        "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
        "/assets/global/scripts/datatable.js",
        "/assets/global/plugins/uniform/jquery.uniform.min.js",
        "/misc/js/page-level/groups.js" ));

?>