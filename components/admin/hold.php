<?php

$targets = Target::getAll();
$countries = Country::getAll();
$webmasters = User::get_by_role_name("webmaster");

$hold = new Hold();
$defaults = $hold->getValues();

require_once(PATH_ROOT . '/templates/admin/users/hold.php');

enqueue_scripts(array(
	"/assets/global/plugins/select2/js/select2.min.js",
	"/misc/js/page-level/hold.js"
));

?>