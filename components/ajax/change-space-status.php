<?php

  Space::setStatus($_POST['id'], $_POST['value']);

  switch ($_POST['value']) {
  	case Space::STATUS_PROCESSING: echo "success"; break;
  	case Space::STATUS_MODERATION: echo "warning"; break;
  	default: echo $_POST['value'];
  }

?>