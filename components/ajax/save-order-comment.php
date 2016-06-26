<?php

if (isset($_POST['id'])) {
    Order::updateComment($_POST['id'], $_POST["comment"]);
}
?>