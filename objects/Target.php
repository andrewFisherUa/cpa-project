<?php

class Target {

	private $id;
	private $name;
	private $commission;
	private $webmaster_commission;
	private $max_price;
	private $max_profit;
	private $good_id;
	private $country_code;
	private $price_id;
	private $currency;

	public function __construct($data = array(), Price $price = null){
		if (is_null($price)) {
			return false;
		}

		$this->id = (isset($data["id"])) ? $data["id"] : 0;
		$this->name = (isset($data["name"])) ? $data["name"] : "";
		$this->commission = (isset($data["commission"])) ? $data["commission"] : 0;
		$this->max_price = (isset($data["max_price"])) ? $data["max_price"] : $price->getValue();
		$this->webmaster_commission = (isset($data["webmaster_commission"])) ? $data["webmaster_commission"] : 0;
		$this->good_id = $price->getGoodId();
		$this->country_code = $price->getCountryCode();
		$this->price_id = $price->getId();
		$this->currency = $price->getCurrency();
		$this->max_profit = $this->max_price - ($price->getValue() - $this->webmaster_commission);
	}

	private function fetchName(){
		if ($this->name != "") {
			return false;
		}
		$query = "SELECT name FROM targets WHERE target_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->name = $data["name"];
	}

	/**
	 *
	 * @param id
	 * @param price_id
	 */
	public static function getInstance($id, $price){
		$good_id = $price->getGoodId();
		$country_code = $price->getCountryCode();

		$query = "SELECT t1.t_id as id, t2.name, t1.comission as commission, t1.comission_webmaster as webmaster_commission, t1.max_price
				  FROM goods2targets AS t1 INNER JOIN targets AS t2 ON t1.t_id = t2.target_id
		          WHERE t1.g_id = :g_id AND t1.country_code = :code AND t.t_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->bindParam(":g_id", $good_id, PDO::PARAM_INT);
		$stmt->bindParam(":code", $country_code, PDO::PARAM_STR);
		$stmt->execute();

		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return new self($data, $price);
	}

	public static function getByPrice($price){
		$good_id = $price->getGoodId();
		$country_code = $price->getCountryCode();

		$query = "SELECT t1.t_id as id, t2.name, t1.comission as commission, t1.comission_webmaster as webmaster_commission, t1.max_price
				  FROM goods2targets AS t1 INNER JOIN targets AS t2 ON t1.t_id = t2.target_id
		          WHERE t1.g_id = :g_id AND t1.country_code = :code ORDER BY t1.t_id DESC";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":g_id", $good_id, PDO::PARAM_INT);
		$stmt->bindParam(":code", $country_code, PDO::PARAM_STR);
		$stmt->execute();

		$items = array();

		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$temp = new self($data, $price);
			$items[$temp->getId()] = $temp;
		}

		return $items;
	}

	public static function getAll(){
		return $GLOBALS['DB']->query("SELECT target_id AS id, name FROM targets")->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	* Возвращает ID цели
	*/
	public function getId(){
		return $this->id;
	}

	public function getName(){
		$this->fetchName();
		return $this->name;
	}

	/**
	* Возвращает комиссию UM
	*/
	public function getCommission(){
		return $this->commission;
	}

	/**
	* Возвращает комиссию вебмастера
	*/
	public function getWebmasterCommission(){
		return $this->webmaster_commission;
	}

	public function getMaxPrice(){
		return $this->max_price;
	}

	public function getMaxProfit(){
		return $this->max_profit;
	}

	/**
	 * Возвращает значение валюты
	 */
	public function getCurrency(){
		return $this->currency;
	}

	/**
	* Устанавливает комиссию UM
	*/
	public function setCommission($param){
		$this->commission = $param;
	}

	/**
	* Устанавливает комиссию вебмастера
	*/
	public function setWebmasterCommission($param){
		$this->webmaster_commission = $param;
	}

	public function setMaxPrice($param) {
		$this->max_price = $param;
	}

	/**
	 * Добавление новой цели
	 *
	 * @param name
	 * @param price_id
	 */
	public static function add($id, $name){
		$query = "INSERT INTO targets(target_id, name) VALUES(:id, :name)";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":name", $name, PDO::PARAM_STR);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
	}

	/**
	* Сохранение цели
	*/
	public function save(){
		$query = "INSERT INTO goods2targets (g_id, t_id, country_code, comission, comission_webmaster, max_price)
		          VALUES (:g_id, :t_id, :country_code, :comission, :comission_webmaster, :max_price)";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":t_id", $this->id, PDO::PARAM_INT);
		$stmt->bindParam(":g_id", $this->good_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->bindParam(":comission", $this->commission, PDO::PARAM_INT);
		$stmt->bindParam(":comission_webmaster", $this->webmaster_commission, PDO::PARAM_INT);
		$stmt->bindParam(":max_price", $this->max_price, PDO::PARAM_INT);
		$stmt->execute();
	}

}
?>