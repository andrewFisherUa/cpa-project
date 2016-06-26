<?php

 require_once( PATH_ROOT .'/misc/plugins/php/mail/PHPMailer/PHPMailerAutoload.php');
 require_once( PATH_ROOT .'/misc/plugins/php/mail/u_mail.php');

  $id = $_POST['id'];
  $status = $_POST['value'];

  $defs = array( 0 => array("class"=>"warning", "label" => "Не подтвержден" ),
                 1 => array("class"=>"info", "label" => "На модерации" ),
                 2 => array("class"=>"success", "label" => "Активирован" ),
                 3 => array("class"=>"danger", "label" => "Заблокирован" ));

  User::upd_status( $id, $status );

  $record = new Audit([
    "group" => "user",
    "subgroup" => "change_status",
    "action" => "Изменения статуса для пользователя с ID `{$id}`",
  ]);

  $record->addDetails([
    "status" => $status,
    "user_id" => $id,
  ]);

  $record->save();

  // Письмо активации

  if ($status == 2) {
  	$stmt = $GLOBALS['DB']->prepare("SELECT email FROM users WHERE user_id = ?");
  	$stmt->execute([
  			$id
  	]);

  	if ($stmt->rowCount()) {
  		$email = $stmt->fetchColumn();
  		$smarty->assign('id', $id);
      $smarty->assign("site_url", get_ste_url());
  		$msg = $smarty->fetch("email_templates" . DS . "email_activation.tpl");
  	}

  	$mail = new u_mail(true);
    $mail->sendmail($_SERVER['HTTP_HOST'], 'support@'.$_SERVER['HTTP_HOST'], $email, "Активация аккаунта", $msg);

    Notification::push([
      "section" => "users_on_moderation",
      "replace" => TRUE,
      "message" => "Пользователи на модерации - {counter}",
      "counter_sub" => 1,
      "users" => [
        20, 21, 69
      ],
    ]);
  }


  echo $defs[$status]['class'];
  die();

?>