<?php

   $msg = '';

   $query = "SELECT p.id, u.login FROM users AS u INNER JOIN partners AS p ON u.user_id = p.id WHERE u.status = 0 AND p.activation = :activation";
   $stmt = $GLOBALS['DB']->prepare($query);
   $stmt->bindParam( ":activation", $activation, PDO::PARAM_STR );
   $stmt->execute();

   if ( $stmt->rowCount() ) {
      $row = $stmt->fetch( PDO::FETCH_ASSOC );
      User::upd_status($row['id'], 1 );
      $msg = array(
         "class" => "success",
         "text" => "Ваш email успешно подтвержден! <br/>Администрация сайта рассмотрит аккаунт в течении часа"
      );

      Audit::addRecord([
         "group" => "email_activation",
         "action" => "Подтверждение email. Успешно.",
         "details" => $row
      ]);
      
   } else {
      $msg = array(
         "class" => "fail",
         "text" => "Неверный код активации"
      );

      Audit::addRecord([
         "group" => "email_activation",
         "action" => "Подтверждение email. Неверный код активации",
         "details" => [
            "key" => $activation
         ]
      ]);
   }

   $smarty->assign( 'message', $msg );
   $page_content = $smarty->fetch('email_activation.tpl');
   $smarty->assign('page_content', $page_content);
   $smarty->display("index.tpl");
?>