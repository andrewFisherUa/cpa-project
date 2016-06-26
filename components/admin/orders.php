<?php

$smarty->assign('status', Order::getStatusList());

if ( $_REQUEST['k'] == 'view' && isset($_REQUEST['b']) ) {
    $order = Order::getInstance($_REQUEST['b']);
    if (is_null($order)) {
        echo "<script>window.location = '/admin/orders' </script>";
    }

    if ($order->getSource() == "stream") {
        $stmt = $GLOBALS['DB']->query("SELECT link FROM flows WHERE f_id = " . $order->getSourceId());
        $stream_link = STREAMS_URL . "/" . $stmt->fetchColumn();
        $smarty->assign('stream_link', $stream_link);
    }

    $smarty->assign('country', Country::getName($order->getCountryCode()));

    $log = $order->getLog();
    foreach ($log as &$a) {
        $a['created'] = date("d/m/Y H:i", $a['created']);
    }

    $smarty->assign('log', $log);
    $smarty->assign('order', $order);
    $smarty->display( 'admin' . DS . 'orders' . DS . 'single.tpl' );
}

if ( $_REQUEST['k'] == '' ) {
    if (User::isAdmin()) {
        $sql = "SELECT DISTINCT t1.login, t1.user_id as id
              FROM users AS t1 INNER JOIN orders AS t2 ON t1.user_id = t2.user_id
              ORDER BY t1.login";
        $stmt = $GLOBALS['DB']->query($sql);
        $smarty->assign("users", $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    enqueue_scripts( array(
    "/assets/global/plugins/datatables/datatables.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
    "/assets/global/plugins/uniform/jquery.uniform.min.js",
    "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
    "/assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js",
    "/assets/global/plugins/select2/js/select2.min.js",
    "/misc/js/page-level/orders.js"));
    $smarty->display( 'admin' . DS . 'orders' . DS . 'index.tpl' );
} else {
    enqueue_scripts(array("/misc/js/page-level/singleOrder.js"));
}

?>
