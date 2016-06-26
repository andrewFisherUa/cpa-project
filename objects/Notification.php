<?php

class Notification {

	private $id;
	private $type;
	private $section;
	private $message;
	private $priority;
	private $created;
	private $status;
	private $counter;
	private $users = [];

	const STATUS_ACTIVE = "active";
	const STATUS_ARCHIVE = "archive";

	const HIGH_PRIORITY = "high";
	const MEDIUM_PRIORITY = "medium";
	const LOW_PRIORITY = "low";

	public function __construct($data) {
		$this->id = (isset($data['id'])) ? $data['id'] : 0;
		$this->section = (isset($data['section'])) ? $data['section'] : "";
		$this->message = (isset($data['message'])) ? $data['message'] : "";
		$this->priority = (isset($data['priority'])) ? $data['priority'] : self::LOW_PRIORITY;
		$this->status = (isset($data['status'])) ? $data['status'] : self::STATUS_ACTIVE;
		$this->users = isset($data['users']) ? $this->addUsers($data['users']) : [];
		$this->counter = (isset($data['counter'])) ? $data['counter'] : 0; 
		$this->created = (isset($data['created'])) ? $data['created'] : time(); 

		if (isset($data["counter_add"])) {
			$this->counter += $data["counter_add"];
		}

		if (isset($data["counter_sub"])) {
			$this->counter -= $data["counter_sub"];
		}
	}

	public function getId(){
		return $this->id;
	}

	public function getCounter(){
		return $this->counter;
	}

	public function getPriority(){
		return $this->priority;
	}

	public function getSection(){
		return $this->section;
	}

	public function getType(){
		return $this->type;
	}

	public function getCreated(){
		return $this->created;
	}

	public function getStatus(){
		return $this->status;
	}

	public function getMessage(){
		return str_replace("{counter}", $this->counter, $this->message);
	}

	public function getUsers(){
		return $this->users;
	}

	public function addUsers($params = []){
		if (!empty($params["ids"])) {
			return $params["ids"];
		}
	}

	public static function push($data){
		if ($data["replace"] == TRUE) {

			// получаем counter
			$stmt = $GLOBALS["DB"]->prepare("SELECT counter FROM notifications WHERE section = ?");
			$stmt->execute([
				$data["section"]
			]);

			$data["counter"] = $stmt->fetchColumn();

			self::delete($data["section"]);
		}

		$record = new self($data);
		$record->save();
	}


	// Заменить уведомление группы. Например, уведомление "Пользователи на модерации - ?"
	public static function delete($section){
		$query = "DELETE FROM notifications WHERE section = ?";
		$stmt = $GLOBALS["DB"]->prepare($query);
		$stmt->execute([
			$section
		]);
	}

	public static function sendGroupToArchive($group){
		$query = "UPDATE orders SET status = " . self::STATUS_ARCHIVE . " WHERE group = ?";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([
			$group
		]);
	}

	public static function sendToArchive($notification_id){
		$query = "UPDATE orders SET status = " . self::STATUS_ARCHIVE . " WHERE notification_id = ?";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([
			$notification_id
		]);
	}

	public static function setSeen($parameters) {

		$where = [];

		if (isset($parameters["id"])) {
			$where[] = "notification_id = " . $parameters["id"];
		}

		if (isset($parameters["user_id"])) {
			$where[] = "user_id = " . $parameters["user_id"];
		}

		if (isset($parameters["section"])) {
			$where[] = "notification_id IN (SELECT notification_id FROM notifications WHERE notification_id = '" . $parameters["section"] . "')";
		}


		$query = "UPDATE user_notification SET seen = " . time() . " WHERE " . implode(" AND ", $where);
		$GLOBALS["DB"]->exec($query);
	}

	public function save(){	

		$query = "INSERT INTO notifications(`section`, message, created, priority, status, counter)
		          VALUES (:p1, :p2, :p3, :p4, :p5, :p6)";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":p1", $this->section, PDO::PARAM_STR);
		$stmt->bindParam(":p2", $this->message, PDO::PARAM_STR);
		$stmt->bindParam(":p3", $this->created, PDO::PARAM_INT);
		$stmt->bindParam(":p4", $this->priority, PDO::PARAM_STR);
		$stmt->bindParam(":p5", $this->status, PDO::PARAM_STR);
		$stmt->bindParam(":p6", $this->counter, PDO::PARAM_INT);
		
		if ($stmt->execute()) {
			$this->id = $GLOBALS['DB']->lastInsertId();
			$this->saveUsers();
		}		
	}

	private function saveUsers(){
		$values = [];
		foreach ($this->users as $id) {
			$values[] = "({$this->id}, {$id})";
		}

		if (!empty($values)) {
			$query = "INSERT INTO user_notification (notification_id, user_id) VALUES " . implode(",", $values);
			$GLOBALS['DB']->exec($query);
		}
	}
}

?>