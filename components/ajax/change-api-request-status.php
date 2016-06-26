<?php

$filter = new Filter;

$status = $filter->sanitize($_REQUEST['value'], ["string", "striptags"]);
$uid = $filter->sanitize($_REQUEST['uid'], "int!");

$v = change_api_key_status($uid, $status);

if ($v !== true) {
	echo implode("<br/>", $v);
}

?>