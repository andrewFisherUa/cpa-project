<?php

/***************************
login
logout
login_as
logout_as
offer | change_status
user | change_status
user | view_profile
user | change_password
user | save_proile
pass_recovery_request | success
pass_recovery_request | fail
pass_recovery | success
pass_recovery | fail
***************************/

class Audit {

	private $id;
	private $group;
	private $subgroup;
	private $action;
	private $user_id;
	private $admin_id;
	private $ip;
	private $timestamp;
	private $priority;
	private $details = [];

	const HIGH_PRIORITY = "high";
	const MEDIUM_PRIORITY = "medium";
	const LOW_PRIORITY = "low";

	public function __construct($data) {
		$this->id = (isset($data['id'])) ? $data['id'] : 0;
		$this->group = (isset($data['group'])) ? $data['group'] : "";
		$this->subgroup = (isset($data['subgroup'])) ? $data['subgroup'] : "";
		$this->action = (isset($data['action'])) ? $data['action'] : "";
		$this->user_id = (isset($data['user_id'])) ? $data['user_id'] : 0;
		$this->admin_id = (isset($data['admin_id'])) ? $data['admin_id'] : 0;
		$this->ip = (isset($data['ip'])) ? $data['ip'] : "";
		$this->timestamp = (isset($data['timestamp'])) ? $data['timestamp'] : time();
		$this->priority = (isset($data['priority'])) ? $data['priority'] : self::LOW_PRIORITY;
		$this->details = (isset($data['details'])) ? $data['details'] : [];
	}

	public static function getTotalCount() {
		$stmt = $GLOBALS['DB']->query("SELECT count(*) FROM audit");
		return $stmt->fetchColumn();
	}

	public static function getAll($from = 0, $count = -1){
		$items = [];

		$query = "SELECT * FROM audit ORDER BY `timestamp` DESC";

		if ($count != -1) {
			$query .= " LIMIT " . $from . "," . $count;
		}

		$stmt = $GLOBALS['DB']->query($query);
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$items[] = new self($row);
		}

		return $items;
	}

	public function getPriority(){
		return $this->priority;
	}

	public function getUserId(){
		return $this->user_id;
	}

	public function getAction(){
		return $this->action;
	}

	public function getAdminId(){
		return $this->admin_id;
	}

	public function getTimestamp($format = false){
		if ($format) {
			return date("d-m-Y H:i", $this->timestamp);
		}
		return $this->timestamp;
	}

	public function getIp(){
		return $this->ip;
	}

	public function getId(){
		return $this->id;
	}

	public function addDetails($details = []){
		$this->details = $details;
	}

	public static function addRecord($data){
		$record = new self($data);
		$record->save();
	}

	public function save(){
		$this->ip = $_SERVER['REMOTE_ADDR'];

		if ($this->user_id == 0) {
			$this->user_id = User::get_current_user_id();
			$this->admin_id = $_SESSION['was_admin'];
		}
		
		$this->timestamp = time();

		$query = "INSERT INTO audit(id, `group`, subgroup, action, user_id, admin_id, ip, `timestamp`, priority)
		          VALUES (:p1, :p2, :p3, :p4, :p5, :p6, :p7, :p8, :p9)";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":p1", $this->id, PDO::PARAM_INT);
		$stmt->bindParam(":p2", $this->group, PDO::PARAM_STR);
		$stmt->bindParam(":p3", $this->subgroup, PDO::PARAM_STR);
		$stmt->bindParam(":p4", $this->action, PDO::PARAM_STR);
		$stmt->bindParam(":p5", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":p6", $this->admin_id, PDO::PARAM_INT);
		$stmt->bindParam(":p7", $this->ip, PDO::PARAM_STR);
		$stmt->bindParam(":p8", $this->timestamp, PDO::PARAM_INT);
		$stmt->bindParam(":p9", $this->priority, PDO::PARAM_STR);
		$r = $stmt->execute();

		if ($r) {
			$this->id = $GLOBALS['DB']->lastInsertId();
			$this->saveDetails();
		}		
	}

	private function saveDetails(){
		if (!empty($this->details)) {
			foreach ($this->details as $key=>&$value) {
				$query = "INSERT INTO audit_details(audit_key, audit_value, aid) VALUES (:p1, :p2, :p3)";
				$stmt = $GLOBALS['DB']->prepare($query);
				$stmt->bindParam(":p1", $key, PDO::PARAM_STR);
				$stmt->bindParam(":p2", $value, PDO::PARAM_STR);
				$stmt->bindParam(":p3", $this->id, PDO::PARAM_INT);
				$stmt->execute();
			}
		}
	}

	public static function getActionsList(){
		return [
			"edit_navigation"	=> "Редактирование навигации",
			"edit_offer" => "Редактирование оффера",
			"login" => "Вход в кабинет",
			"login_as" => "Вход в кабинет пользователя",
			"login_fail" => "Ошибка авторизации",
			"logot" => "Выход из кабинета",
			"logout_as" => "Вход в кабинет пользователя",
			"offer+change_status" => "Изменение статуса оффера",
			"offer+connect" => "Подключение оффера",
			"offer+disconnect" => "Отключение оффера",
			"order+view" => "Просмотр заказа",
			"payment+approve" => "Одобрение выплаты",
			"payment+cancel" => "Отклонение выплаты",
			"payment+low_balance" => "Недостаточно средств для выплаты",
			"stat+view_admin_stat" => "Просмотр админской статистики",
			"stream+save" => "Сохранение потока",
			"user+ask_for_payment" => "Запрос выплаты",
			"user+change_status" => "Изменение статуса пользователя",
			"user+new_wallet" => "Создание кошелька",
			"user+view_profile" => "Просмотр профиля",
			"view_page" => "Просмотр страницы"
		];
	}
}

?>