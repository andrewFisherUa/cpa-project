<?php

$options = new Options();
$support_email = $options->get_option("support_email");
$success = "";

$user_id = User::get_current_user_id();

// messages page
if ( isset($_REQUEST['k']) && $_REQUEST['k'] != '' ) {

    $filter = new Filter;
    $ticket_id = $filter->sanitize($_REQUEST['k'], "int");

    $ticket = Ticket::getInstance($ticket_id);

    if ( $ticket === false || !$ticket->isAvailableToUser($user_id) ) {
        echo "<div class='alert alert-danger'>Тикет не найден</div>";
    } else {

        if (User::isAdmin() || User::isSupport()) {
            $from_uid = 0;
            $to_uid = $ticket->getUserId();
        } else {
            $to_uid = 0;
            $from_uid = User::get_current_user_id();

            Ticket::setViewed($ticket->getId(), $from_uid);
        }

        require_once PATH_ROOT . "/templates/admin/tickets/messages.php";

        enqueue_scripts([
            "/misc/fancybox/lib/jquery.mousewheel-3.0.6.pack.js",
            "/misc/fancybox/source/jquery.fancybox.pack.js?v=2.1.5",
            "/misc/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"
        ]);
    }
} else {
    // tickets page

    $isSupport = User::isAdmin() || User::isSupport();

    $filters = [];
    $query = "SELECT DISTINCT u.user_id, u.login
              FROM users AS u INNER JOIN tickets AS t ON u.user_id = t.user_id
              ORDER BY u.login";
    $stmt = $GLOBALS['DB']->query($query);
    $filters['users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require_once PATH_ROOT . "/templates/admin/tickets/index.php";

    enqueue_scripts(array(
        "/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
        "/assets/global/plugins/datatables/datatables.min.js",
        "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
        "/assets/global/plugins/select2/js/select2.min.js",
        "/assets/global/scripts/datatable.js"));
}

enqueue_scripts( array(
    "/misc/js/page-level/tickets-1.js" ));

?>