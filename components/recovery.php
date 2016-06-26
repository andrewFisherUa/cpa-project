<?php
    require_once( PATH_ROOT . DS . 'misc'. DS . 'php' . DS . 'functions.php' );
    require_once( PATH_ROOT .'/misc/plugins/php/mail/PHPMailer/PHPMailerAutoload.php');
    require_once( PATH_ROOT .'/misc/plugins/php/mail/u_mail.php');

    if ( $_GET['k'] ) {
        $smarty->assign('key', $_GET['k']);
    }

    $smarty->display('admin/recovery/index.tpl');

?>
