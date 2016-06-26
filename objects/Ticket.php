<?php

require_once( PATH_ROOT .'/misc/plugins/php/mail/PHPMailer/PHPMailerAutoload.php');
require_once( PATH_ROOT .'/misc/plugins/php/mail/u_mail.php');

class Ticket {

	private $id;
	private $user_id;
	private $subject;
	private $urgent; // срочность
	private $created;
	private $closed; // 0 || 1
	private $closed_time;

	private $messages = [];

	public function __construct($data) {
		$this->id = isset($data['id']) ? $data['id'] : 0;
		$this->user_id = isset($data['user_id']) ? $data['user_id'] : 0;
		$this->subject = isset($data['subject']) ? $data['subject'] : "";
		$this->urgent = isset($data['urgent']) ? $data['urgent'] : 0;
		$this->closed = isset($data['closed']) ? $data['closed'] : 0;
		$this->closed_time = isset($data['closed_time']) ? $data['closed_time'] : 0;
		$this->created = isset($data['created']) ? $data['created'] : time();
	}

	public function getAttachmentsFolder(){
		return get_site_url() . "/misc/uploads/tickets/" . $this->user_id . "/" . $this->id . "/";
	}

	public function getLastMessage(){
		$messages = $this->getMessages();
		return $messages[0];
	}

	public function getMessagesCount(){
		return count($this->getMessages());
	}

	private function hasErrors(){
		$errors = [];
		if ($this->user_id <= 0) {
			$errors[] = "user_id is not valid";
		}

		if ($this->subject == "") {
			$errors[] = "subject is not defined";
		}

		if (empty($errors)) {
			return false;
		}

		return $errors;
	}

	public function save(){

		$errors = $this->hasErrors();

		if ($errors !== false) {
			return $errors;
		}

		if ($this->id == 0) {
			$query = "INSERT INTO tickets (ticket_id, subject, urgent, created, user_id)
			          VALUES (:ticket_id, :subject, :urgent, :created, :user_id)";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":ticket_id", $this->id, PDO::PARAM_INT);
			$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
			$stmt->bindParam(":subject", $this->subject, PDO::PARAM_STR);
			$stmt->bindParam(":urgent", $this->urgent, PDO::PARAM_INT);
			$stmt->bindParam(":created", $this->created, PDO::PARAM_INT);
			$r = $stmt->execute();

			if ($r) {
				$this->id = $GLOBALS['DB']->lastInsertId();
			}
		}

		if ($r) {
			$this->saveMessages();
		}

		return $r;
	}

	public function addMessage(TicketMessage $message){
		array_unshift($this->messages, $message);
	}

	private function saveMessages(){
		if (!empty($this->messages)) {
			foreach ($this->messages as $message) {
				if ($message->getId() == 0) {
					$message->setTicketId($this->id);
					$message->save();
				}
			}
		}
	}

	public function getMessages(){
		if (empty($this->messages)) {
			$this->messages = TicketMessage::getAll($this->id);
		}

		return $this->messages;
	}

	public function getInstance($id) {
		$query = "SELECT *, ticket_id as id FROM tickets WHERE ticket_id = ?";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([$id]);
		if ($stmt->rowCount()) {
			return new self($stmt->fetch(PDO::FETCH_ASSOC));
		}

		return false;
	}

	public function getAll($user_id = 0, $filters = []){
		$items = []; $cond = [];

		$query = "SELECT *, ticket_id as id FROM tickets";
		if ($user_id > 0) {
			$cond[] = "user_id = " . $user_id;
		}

		if (!empty($filters)) {
			foreach($filters as $name=>$val) {
				if (is_string($val)) {
					$val = "'" . $val . "'";
				}

				$cond[] = $name ."=" . $val;
			}
		}

		if (!empty($cond)) {
			$query .= " WHERE " . implode(" AND ", $cond);
		}

		$stmt = $GLOBALS['DB']->query($query);

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$items[] = new self($row);
		}

		return $items;
	}

	public static function close($id) {
		$stmt = $GLOBALS['DB']->prepare("UPDATE tickets SET closed = 1, closed_time = " . time() . " WHERE ticket_id = ?");
		$stmt->execute([
			$id
		]);
	}

	public static function open($id) {
		$stmt = $GLOBALS['DB']->prepare("UPDATE tickets SET closed = 0, closed_time = 0 WHERE ticket_id = ?");
		$stmt->execute([
			$id
		]);
	}

	public function getSubject(){
		return $this->subject;
	}

	public function getId(){
		return $this->id;
	}

	public function getUserId(){
		return $this->user_id;
	}

	public function getUserLogin(){
		$query = "SELECT login FROM users WHERE user_id = " . $this->user_id;
		$stmt = $GLOBALS['DB']->query($query);
		$stmt->execute();
		return $stmt->fetchColumn();
	}

	public function getCreated(){
		return $this->created;
	}

	public function isUrgent(){
		return $this->urgent == 1;
	}

	public function isClosed(){
		return $this->closed == 1;
	}

	public function isOpened(){
		return $this->closed == 0;
	}

	public function isUnread($user_id){
		$query = "SELECT count(*) FROM tickets_messages WHERE ticket_id = ? AND to_uid = ? AND seen = 0";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([
			$this->id,
			$user_id
		]);

		$count = $stmt->fetchColumn();

		return $count > 0;
	}

	public function getClosedTime(){
		return $this->closed_time;
	}

	public static function getUnreadCount($user_id){
		// get tickets count with unread messages
	}

	public static function getUnreadMessagesCount($user_id, $ticket_id = 0){
		// get unread messages count
	}

	public static function setViewed($ticket_id, $user_id) {

		if (User::isAdmin($user_id)) {
			$user_id = 0;
		}

        $stmt = $GLOBALS['DB']->prepare("UPDATE tickets_messages SET seen = 1 WHERE ticket_id = ? AND to_uid = ?");
        $stmt->execute([
            $ticket_id,
            $user_id
        ]);
    }

    public function isViewed($user_id){
       $stmt = $GLOBALS['DB']->query("SELECT ticket_id FROM tickets_messages WHERE ticket_id = {$this->id} AND to_uid = {$user_id} AND seen = 0");
       return $stmt->rowCount() == 0;
    }

    public function isAvailableToUser($user_id){
        return User::isAdmin($user_id) || User::isSupport($user_id) || $user_id == $this->user_id;
    }

    // action - close_ticket, new_message
	public static function sendEmail($ticket, $action){
	    global $smarty;

	    $options = new Options();
	    $support_email = $options->get_option("support_email");

	    $query = "SELECT login, email FROM users WHERE user_id = " . $ticket->getUserId();
	    $stmt = $GLOBALS['DB']->query($query);
	    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

	    $data = [
	        "ticket_id" => $ticket->getId(),
	        "subject" => $ticket->getSubject(),
	        "username" => ""
	    ];

	    if ($action == "new_message" || $action == "new_support_ticket") {
	        $sent_by_support =  $ticket->getLastMessage()->sentBySupport();
	        $data["message"] = $ticket->getLastMessage()->getMessage();
	        $data["sent_by_support"] = $sent_by_support;
	        $data['username'] = $user_data['login'];

	        if ($action == "new_message") {
	        	$subject = "Новое сообщение по тикету `" . $ticket->getSubject() . "`";
	        } 

	        if ($action == "new_support_ticket") {
	        	$subject = "Новый тикет `" . $ticket->getSubject() . "`";
	        } 

	        if ($sent_by_support) {
	            $to = $user_data['email'];
	        } else {
	            $to = $support_email;
	        }
	    }

	    if ($action == "close_ticket") {
	        $subject = "Закрытие тикета `" . $ticket->getSubject() . "`";
	        $data['username'] = $user_data['login'];
	        $to = $support_email;
	    }

	    $smarty->assign('admin_url', get_admin_url());
	    $smarty->assign('data', $data);
	    $message = $smarty->fetch('email_templates' . DS . 'tickets' . DS . $action . '.tpl');

	    $mail = new u_mail(true);
	    $mail->sendmail($_SERVER['HTTP_HOST'], 'support@' . $_SERVER['HTTP_HOST'], $to, $subject, $message);
	}

}

?>