<?php

if ($_REQUEST['k'] == "webmaster") {
  require_once "part/statistics/admin/webmaster.php";
} else {
  require_once "part/statistics/admin/admin.php";
}

?>