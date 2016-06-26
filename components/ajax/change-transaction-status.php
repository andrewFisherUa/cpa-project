<?php

  $id = $_POST['id'];
  $status = $_POST['value'];

  Transaction::changeStatus($id, $status);

  echo $_POST['value'];

?>