<?php
/*
 * Class for sending mail
 */
class u_mail extends PHPMailer{

    public function __construct($exceptions = false) {
        parent::__construct($exceptions);
    }

    public function sendmail($fromname, $from, $to, $subject, $message){
        try { 
            $this->CharSet = 'UTF-8';
            $this->Sender = $from;
            $this->ReturnPath = $from;
            $this->SetFrom($from, $fromname);
            $this->AddAddress($to, "");
            $this->Subject = $subject; 
            $this->msgHTML(iconv("UTF-8", "WINDOWS-1251", $message));
            $r = $this->send();
            if (!$r) {
                die('ERROR:'.$this->ErrorInfo);
            } else {
                $this->ClearAllRecipients();   
                return $r;
            }
        } catch (phpmailerException $e) {
             die($e->errorMessage());
        }
    }

}


?>
