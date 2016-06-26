<?php

$type = Balance::getDefaultCurrency(User::get_current_user_id());
$response = Converter::getConvert($_POST['from'], $_POST['to'], $_POST['amount'], $type);
echo json_encode($response);

?>