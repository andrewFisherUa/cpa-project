<?php


if (empty($_REQUEST["k"])) {
	require_once "part/payments/index.php";
} else {
	require_once "part/payments/single.php";
}

?>
