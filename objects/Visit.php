<?php

class Visit {
	
	private $user_id;
	private $ip;
	private $user_agent;
	private $timestamp;
	private $status;
	private $country_code;
	private $country_name;
	private $changed; // время изменения статуса
	private $changed_by; // id пользователя, изменившего статус
	private $db;

	const STATUS_NEW = "new";
	const STATUS_SUSPICIOUS = "suspicious";
	const STATUS_CHECKED = "checked";
	const STATUS_MALICIOUS = "malicious";

	public function __construct($db, $data) {
		$this->db = $db;
		$this->user_id = isset($data['user_id']) ? $data['user_id'] : 0;
		$this->ip = isset($data['ip']) ? $data['ip'] : "";
		$this->user_agent = isset($data['user_agent']) ? $data['user_agent'] : "";
		$this->country_code = isset($data['country_code']) ? $data['country_code'] : "";
		$this->country_name = isset($data['country_name']) ? $data['country_name'] : "";
		$this->created = isset($data['created']) ? $data['created'] : time();
		$this->status = isset($data['status']) ? $data['status'] : self::STATUS_NEW;
		$this->changed = isset($data['changed']) ? $data['changed'] : time();
		$this->changed_by = isset($data['changed_by']) ? $data['changed_by'] : 0;
	}

	public function getIp(){
		return $this->ip;
	}

	public function getBrowser(){
		return $this->user_agent;
	}

	public function getLocation(){
		//return geoip_country_name_by_name ($this->ip);
	}

	public function getCreated(){
		return $this->created;
	}

	public function getStatus(){
		return $this->status;
	}

	public function getCountryCode(){
		return $this->country_code;
	}

	public function getCountryName(){
		return $this->country_name;
	}

	public function addRecord($db, $data) {
		$v = new self($db, $data);
		$v->save();
	}

	private function _getStatus(){
		$data = [
			"status" => "",
			"comment" => ""
		];

		// Проверяем заходили ли другие пользователи с таким ip
		$query = "SELECT user_id, status FROM user_audit WHERE (ip = :ip AND (user_id != :uid  OR status = 'suspicious'))";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":uid", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":ip", $this->ip, PDO::PARAM_STR);
		$stmt->execute();

		if ($stmt->rowCount() > 0) {
			$ids = [];
			while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
				if ($a['status'] == self::STATUS_SUSPICIOUS) {
					$data["status"] = self::STATUS_SUSPICIOUS;
					$data["comment"] = "Подозрительный IP. ";
				}

				$ids[] = $a['user_id'];
			}

			$data["comment"] .= "Пользователи с этим IP: " . implode(",", $ids);

			return $data;
		}

		if (in_array($this->country_code, ["ru", "by", "kz", "ua"])) {
			$data["status"] = self::STATUS_NEW;
		} else {
			$data["status"] = self::STATUS_SUSPICIOUS;
		}
		
		return $data;
	}

	public function save(){
		$query = "SELECT user_id FROM user_audit WHERE user_id = :uid AND ip = :ip";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(":uid", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":ip", $this->ip, PDO::PARAM_STR);
		$stmt->execute();

		$time = time();

		if ($stmt->rowCount() > 0) {
			$query = "UPDATE user_audit SET created = {$time}, user_agent = :ua, counter = counter + 1 WHERE user_id = :uid AND ip = :ip";
			$stmt = $this->db->prepare($query);
			$stmt->bindParam(":uid", $this->user_id, PDO::PARAM_INT);
			$stmt->bindParam(":ip", $this->ip, PDO::PARAM_STR);
			$stmt->bindParam(":ua", $this->user_agent, PDO::PARAM_STR);
			$stmt->execute();
		} else {

			$this->country_name = "Ukraine";//geoip_country_name_by_name($this->ip);
    		$this->country_code = "ua";//strtolower(geoip_country_code_by_name($this->ip));

    		$a = $this->_getStatus();

    		$this->status = $a['status'];

			$query = "INSERT INTO user_audit (user_id, ip, created, changed, user_agent, status, country_code, country_name)
					  VALUES (:uid, :ip, {$time}, {$time}, :ua, :s, :code, :country )";
			$stmt = $this->db->prepare($query);
			$stmt->bindParam(":uid", $this->user_id, PDO::PARAM_INT);
			$stmt->bindParam(":ip", $this->ip, PDO::PARAM_STR);
			$stmt->bindParam(":ua", $this->user_agent, PDO::PARAM_STR);
			$stmt->bindParam(":s", $this->status, PDO::PARAM_STR);
			$stmt->bindParam(":code", $this->country_code, PDO::PARAM_STR);
			$stmt->bindParam(":country", $this->country_name, PDO::PARAM_STR);
			$stmt->execute();

			$this->send_email($a["comment"]);
		}
	}

	private function send_email($m = ""){
		require_once( $_SERVER['DOCUMENT_ROOT'] .'/misc/plugins/php/mail/PHPMailer/PHPMailerAutoload.php');
		require_once( $_SERVER['DOCUMENT_ROOT'] .'/misc/plugins/php/mail/u_mail.php');

		$message = "<p><strong>UID:</strong> {$this->user_id}</p>";
		$message .= "<p><strong>Country:</strong> {$this->country_name}</p>";
		$message .= "<p><strong>IP: </strong>{$this->ip}</p>";
		if ($m) {
			$message .= "<p><strong>{$m}</strong></p>";
		}

		$to = "sorochan.e.a@gmail.com";
		$subject = "Вход в админку";

		$mail = new u_mail(true);
    	$mail->sendmail($_SERVER['HTTP_HOST'], 'support@' . $_SERVER['HTTP_HOST'], $to, $subject, $message);
	}

	public function changeStatus($db, $ip, $status)  {
		$query = "UPDATE user_audit SET changed = " . time() . ", status = :s WHERE ip = :ip";
		$stmt = $db->prepare($query);
		$stmt->bindParam(":ip", $ip, PDO::PARAM_STR);
		$stmt->bindParam(":s", $status, PDO::PARAM_STR);
		$stmt->execute();
	}
}

?>