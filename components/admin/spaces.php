<?php

$k = $_REQUEST['k'];
$c = "main";

if ($k == "validate") {
  $c = "validate";
} else if ($k != ""){
  $c = "edit";
}

require_once "part/spaces/{$c}.php";