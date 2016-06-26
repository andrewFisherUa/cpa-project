<?php

class Api_Stream {

	private $id;
	private $key;
	private $name;
	private $user_id;
	private $offer_id;
	private $created;
	private $changed;
	private $prices;
	private $db;
	
	public function __construct($db, $data = []) {
		$this->db = $db;
		$this->id = (isset($data["id"])) ? $data['id'] : 0;
		$this->name = (isset($data["name"])) ? $data['name']: '';
		$this->user_id = (isset($data["user_id"])) ? $data['user_id'] : 0;
		$this->offer_id = (isset($data["offer_id"])) ? $data['offer_id'] : 0;
		$this->key = (isset($data["key"])) ? $data['key'] : "";
		$this->created = (isset($data["created"])) ? $data['created'] : time();
		$this->changed = (isset($data["changed"])) ? $data['changed'] : time();
		$this->_getPrices();
	}

	public static function getById($db, $id) {
		$stmt = $db->prepare("SELECT * FROM api_streams WHERE id = ?");
		$stmt->execute([
			$id
		]);

		if ($stmt->rowCount()) {
			return new self($db, $stmt->fetch(PDO::FETCH_ASSOC));
		}

		return false;
	}

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($param){
		$this->name = $param;
	}

	public function getUserId(){
		return $this->user_id;
	}

	public function getOfferId(){
		return $this->offer_id;
	}

	public function getOfferName(){
		$query = "SELECT name FROM goods WHERE id = ?";
		$stmt = $this->db->prepare($query);
		$stmt->execute([
			$this->offer_id
		]);

		return $stmt->fetchColumn();
	}

	public function getKey(){
		return $this->key;
	}

	public function getChanged(){
		return $this->changed;
	}

	public function getCreated(){
		return $this->created;
	}

	public function getPrices(){
		if (empty($this->prices)) {
			$this->_getPrices();
		}

		return $this->prices;
	}

	public function unsetPrices(){
		$this->prices = [];
	}

	public function setProfit($country_code, $target_id, $amount) {
		if ($this->offer_id == 0) {
			return false;
		}

		$query = "SELECT t1.price, t2.comission_webmaster as webmaster_profit, t2.comission as um_profit, t2.max_price, t3.name AS target_name, t4.currency_code as currency
			  	  FROM goods2countries AS t1, goods2targets AS t2, targets AS t3, country as t4
			      WHERE t1.country_code = t2.country_code AND 
					t1.g_id = t2.g_id AND 
					t2.t_id = t3.target_id AND
					t1.country_code = t4.code AND
					t1.g_id = :oid AND 
					t3.target_id = :target_id AND 
					t1.country_code = :country_code";

		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":oid", $this->offer_id, PDO::PARAM_INT);
		$stmt->bindParam(":target_id", $target_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_code", $country_code, PDO::PARAM_STR);
		$r = $stmt->execute();

		if ($stmt->rowCount() == 0) {
			return false;
		}

		$r = $stmt->fetch(PDO::FETCH_ASSOC);

		// Проверяем комиссию. Она должна быть равна или меньше максимально допустимой
		$max_profit = $r["max_price"] - $r["price"] + $r["webmaster_profit"];
		if ($amount > $max_profit) {
			$amount = $max_profit;
		}

		$this->prices[$country_code] = [
			"target_id" => $target_id,
			"target_name" => $r["target_name"],
			"um_profit" => $r["um_profit"],
			"webmaster_profit" => $r["webmaster_profit"],
			"price" => ($r["price"] - $r["webmaster_profit"]) + $amount,
			"recommended" => $r["price"] + $r["webmaster_profit"],
			"currency" => $r["currency"]
		];
	}

	private function _savePrices(){

		if (empty($this->prices)) {
			return false;
		}

		// Удаляем старые цены
		$query = "DELETE FROM api_streams_prices WHERE stream_id = ?";
		$stmt = $this->db->prepare($query);
		$stmt->execute([
			$this->id
		]);

		// Записываем новые цены
		$query = "INSERT INTO api_streams_prices(offer_id, country_code, currency, target_id, target_name, um_profit, webmaster_profit, price, recommended, stream_id)
				  VALUES (:offer_id, :country_code, :currency, :target_id, :target_name, :um_profit, :webmaster_profit, :price, :recommended, :stream_id)";
		$stmt = $this->db->prepare($query);
		foreach ($this->prices as $country_code => &$v) {
			$stmt->bindParam(":country_code", $country_code, PDO::PARAM_STR);
			$stmt->bindParam(":currency", $v["currency"], PDO::PARAM_STR);
			$stmt->bindParam(":target_id", $v["target_id"], PDO::PARAM_INT);
			$stmt->bindParam(":target_name", $v["target_name"], PDO::PARAM_STR);
			$stmt->bindParam(":um_profit", $v["um_profit"], PDO::PARAM_INT);
			$stmt->bindParam(":webmaster_profit", $v["webmaster_profit"], PDO::PARAM_INT);
			$stmt->bindParam(":price", $v["price"], PDO::PARAM_INT);
			$stmt->bindParam(":recommended", $v["recommended"], PDO::PARAM_INT);
			$stmt->bindParam(":stream_id", $this->id, PDO::PARAM_INT);
			$stmt->bindParam(":offer_id", $this->offer_id, PDO::PARAM_INT);
			$stmt->execute();
		}
 	}

	private function _getPrices(){

		$this->prices = [];

		if ($this->id == 0) {
			return false;
		}

		$query = "SELECT * FROM api_streams_prices WHERE stream_id = ?";
		$stmt = $this->db->prepare($query);
		$stmt->execute([
			$this->id
		]);

		while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$country = $a['country_code'];
			unset($a['country_code']);

			$this->prices[$country] = $a;
		}
	}

	private function _saveKey(){
		$this->key = crypt($this->id, blowfishSalt(5));

		$query = "UPDATE api_streams SET `key` = :key WHERE id = :id";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
		$stmt->bindParam(':key', $this->key, PDO::PARAM_STR);
		$stmt->execute();
	}

	private function _isValid(){
		$errors = [];

		if (empty($this->user_id)) {
			$errors[] = "Empty user_id";
		} else {
			// Проверяем существует ли вебмастер с таким id
			$query = "SELECT user_id FROM user_role WHERE role_id = 2 AND user_id = ?";
			$stmt = $this->db->prepare($query);
			$stmt->execute([
				$this->user_id
			]);

			if ($stmt->rowCount() == 0) {
				$errors[] = "User `{$this->user_id}` is not exist";
			}
		}

		if (empty($this->offer_id)) {
			$errors[] = "Empty offer_id";
		} else {
			// Проверяем доступен ли вебмастеру оффер
			$query = "SELECT id FROM goods WHERE id = ? AND offer_status = 'active'";
			$stmt = $this->db->prepare($query);
			$stmt->execute([
				$this->offer_id
			]);

			if ($stmt->rowCount() == 0) {
				$errors[] = "Offer `{$this->offer_id}` is not available";
			}
		}

		if (empty($errors)) {
			return true;
		}

		return $errors;
	}

	public function save() {
		$r = $this->_isValid();

		if ($r === true) {

			if ($this->id == 0) {
				$this->_create();
			} else {
				$this->_update();
			}

			$this->_savePrices();
		} 

		return $r;
	}

	private function _create(){
		$time = time();

		$query = "INSERT INTO api_streams (user_id, name, offer_id, created, changed)
			      VALUES (:user_id, :name, :offer_id, {$time}, {$time})";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(':offer_id', $this->offer_id, PDO::PARAM_INT);
		$stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
		$stmt->execute();

		$this->id = $this->db->lastInsertID();
		
		$this->_saveKey();
	}

	private function _update(){
		$query = "UPDATE api_streams SET name = :name, changed = " . time() . " WHERE id = :id";
		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':id', $this->id, PDO::PARAM_INT );
		$stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
		$stmt->execute();
	}
}