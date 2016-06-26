<?php

// новые пользователи
// запросы на выплату
// запросы на изменение валюты
// источники трафика

/*
Notification::push([
	"section" => "users_on_moderation",
	"replace" => TRUE,
	"message" => "Пользователи на модерации - {counter}",
	"counter_sub" => 1,
	"users" => [
		20, 21, 69
	],
]);
*/

require_once PATH_ROOT . "/templates/admin/notifications/index.php";

enqueue_scripts(array(
	"/assets/global/plugins/datatables/datatables.min.js",
	"/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js",
    "/assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js",
    "/assets/global/scripts/datatable.js",
	"/misc/js/page-level/notifications.js"
));

?>