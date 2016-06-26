<?php

class TicketMessage {

	private $id;
	private $ticket_id;
	private $message;
	private $created;
	private $seen;
	private $from_uid;
	private $to_uid;

	private $attachments = [];

	public function __construct($data) {
		$this->id = ($data['id']) ? $data['id'] : 0;
		$this->ticket_id = ($data['ticket_id']) ? $data['ticket_id'] : 0;
		$this->message = ($data['message']) ? $data['message'] : 0;
		$this->seen = ($data['seen']) ? $data['seen'] : 0;
		$this->from_uid = ($data['from_uid']) ? $data['from_uid'] : 0;
		$this->to_uid = ($data['to_uid']) ? $data['to_uid'] : 0;
		$this->created = ($data['created']) ? $data['created'] : time();

		$this->fetchAttachments();
	}

	private function fetchAttachments(){
		if ($this->id > 0) {
			$query = "SELECT `path` FROM tickets_attachments WHERE tm_id = " . $this->id;
			$stmt = $GLOBALS['DB']->query($query);
			$this->attachments = $stmt->fetchAll(PDO::FETCH_COLUMN);
		}
	}

	public function save(){
		if ($this->ticket_id <= 0) {
			return false;
		}

		if ($this->id == 0 && $this->ticket_id > 0) {
			// save

			$query = "INSERT INTO tickets_messages (ticket_id, message, from_uid, to_uid, created, seen)
					  VALUES (:ticket_id, :message, :from_uid, :to_uid, :created, :seen)";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":ticket_id", $this->ticket_id, PDO::PARAM_INT);
			$stmt->bindParam(":message", $this->message, PDO::PARAM_STR);
			$stmt->bindParam(":from_uid", $this->from_uid, PDO::PARAM_INT);
			$stmt->bindParam(":to_uid", $this->to_uid, PDO::PARAM_INT);
			$stmt->bindParam(":created", $this->created, PDO::PARAM_INT);
			$stmt->bindParam(":seen", $this->seen, PDO::PARAM_INT);
			$r = $stmt->execute();

			if ($r) {
				$this->id = $GLOBALS['DB']->lastInsertId();
				$this->saveAttachments();


				$query = "UPDATE tickets
						  SET changed = {$this->created}, last_reply = {$this->from_uid}
						  WHERE ticket_id = {$this->ticket_id}";
				$GLOBALS['DB']->exec($query);
			}

			return $r;
		}
	}

	private function saveAttachments() {
		if (!empty($this->attachments)) {
			$query = "INSERT INTO tickets_attachments(tm_id, `path`) VALUES ";
			$values = [];
			foreach ($this->attachments as $a) {
				$values[] = "(" . $this->getId() . ", '" . $a . "')";
			}

			$query .= implode(",", $values);

			$GLOBALS['DB']->exec($query);
		}

	}

	public static function getAll($ticket_id) {
		$items = [];

		$query = "SELECT *, message_id as id
				  FROM tickets_messages
				  WHERE ticket_id = " . $ticket_id . "
				  ORDER BY created DESC";

		$stmt = $GLOBALS['DB']->prepare($query);
		$r = $stmt->execute();

		if ($r) {
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$items[] = new self($row);
			}
		}

		return $items;
	}

	public function setTicketId($ticket_id){
		$this->ticket_id = $ticket_id;
	}

	public function setSeen(){
		// set seen to 1
		$stmt = $GLOBALS['DB']->prepare("UPDATE tickets_messages SET seen = 1 WHERE message_id = ?");
		$stmt->execute([
			$this->id
		]);
	}

	public function getTicketId(){
		return $this->ticket_id;
	}

	public function getId(){
		return $this->id;
	}

	public function getMessage(){
		return $this->message;
	}

	public function getCreated($format = false){
		if ($format) {
			return date("d.m.Y H:i", $this->created);
		}

		return $this->created;
	}

	public function seen(){
		return $this->seen == 1;
	}

	public function getSenderId(){
		return $this->from_uid;
	}

	public function sentBySupport(){
		return $this->from_uid == 0;
	}

	public function isSender($user_id){
		if ($user_id == $this->from_uid) {
			return true;
		}

		if ((User::isAdmin($user_id) || User::isSupport($user_id)) && $this->from_uid == 0) {
			return true;
		}

		return false;
	}

	public function getSenderName(){
		if ($this->sentBySupport()) {
			return "Служба поддержки";
		} else {
			if ($this->getSenderId() == User::get_current_user_id()) {
				return "Вы";
			}
		}

		$query = "SELECT login FROM users WHERE user_id = ?";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([$this->from_uid]);
		if ($stmt->rowCount()) {
			return $stmt->fetchColumn();
		}
	}

	public function getRecipientId(){
		return $this->to_uid;
	}

	public function hasAttachments(){
		return count($this->attachments) > 0;
	}

	public function getAttachments(){
		return $this->attachments;
	}

	public function addAttachment($name){
		if (!empty($name) && !in_array($name, $this->attachments)) {
			$this->attachments[] = $name;
		}
	}

}

?>