<?php

class Price {

	private $id;
	private $value;
	private $discount;
	private $targets = array();
	private $selectedTarget;
	private $currency;
	private $country_code;
	private $good_id;

	/**
	 *
	 * @param data
	 */
	public function __construct($data){
		$this->id = (isset($data["id"])) ? $data["id"] : 0;
		$this->value = (isset($data["value"])) ? $data["value"] : "";
		$this->discount = (isset($data["discount"])) ? $data["discount"] : 0;
		$this->currency = (isset($data["currency"])) ? $data["currency"] : "";
		$this->country_code = (isset($data["country_code"])) ? $data["country_code"] : "";
		$this->good_id = (isset($data["good_id"])) ? $data["good_id"] : 0;
	}

	/**
	 * Возвращает ID цены
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Возвращает ID товара
	 */
	public function getGoodId(){
		return $this->good_id;
	}

	/**
	 * Возвращает значение цены
	 */
	public function getValue(){
		return $this->value;
	}

	/**
	 * Возвращает значение валюты
	 */
	public function getCurrency(){
		return $this->currency;
	}

	/**
	 * Возвращает код страны
	 */
	public function getCountryCode(){
		return $this->country_code;
	}

	/**
	 * Возвращает значение скидки
	 */
	public function getDiscount(){
		return $this->discount;
	}

	/**
	 * Возвращает цену без скидки
	 */
	public function getRegularPrice(){
		return ceil( ($this->value * 100 ) / (100 - $this->discount) );
	}

	/**
	 * Возвращает цели, привязанные к цене
	 */
	public function getTargets(){
		$this->fetchTargets();
		return $this->targets;
	}

	public function getTargetsCount(){
		return count($this->getTargets());
	}

	/**
	 * Возвращает цель по ID
	 *
	 * @param id
	 */
	public function getTarget($id){
		$this->fetchTargets();
		return $this->targets[$id];
	}

	/**
	* Достает из БД цели, привязанные к цене
	*/
	private function fetchTargets(){
		if (!empty($this->targets)) {
			return false;
		}

		$this->targets = Target::getByPrice($this);
	}


	/**
	 * Устанавливает значение скидки
	 *
	 * @param discount
	 */
	public function setDiscount($discount){
		$this->discount = $discount;
	}

	public function setSelectedTarget($target_id){
		$this->selected_target = $target_id;
	}

	public function getSelectedTarget() {
		$this->fetchTargets();
		return $this->targets[$this->selected_target];
	}

	/**
	 * Добавление цели
	 *
	 * @param target
	 */
	public function addTarget($data){
		$this->fetchTargets();
		$target = new Target($data, $this);
		$this->targets[$target->getId()] = $target;
	}

	/**
	 * Удаление цели
	 *
	 * @param id
	 */
	public function deleteTarget($id){
		$this->fetchTargets();
		unset($this->targets[$id]);
	}

	/**
	 * Задает значение цены
	 *
	 * @param value
	 */
	public function setValue($value){
		$this->value = $value;
	}

	public static function getGoodsPrices($good_id) {
		$items = array();
		$query = "SELECT t1.country_code, t1.price_id AS id, t1.price AS value, t2.currency, t1.g_id as good_id
		          FROM goods2countries AS t1 INNER JOIN country as t2 ON t1.country_code = t2.code
		          WHERE t1.g_id = :g_id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":g_id", $good_id, PDO::PARAM_INT);
		$stmt->execute();
		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$items[$data["country_code"]] = new self($data);
		}
		return $items;
	}

	/**
	 *
	 * @param good_id
	 * @param country_code
	 */
	public static function getInstance($good_id, $country_code){
		$query = "SELECT t1.country_code, t1.price_id AS id, t1.price AS value, t2.currency, t1.g_id as good_id
		          FROM goods2countries AS t1 INNER JOIN country as t2 ON t1.country_code = t2.code
		          WHERE t1.g_id = :g_id AND t2.code = :code";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":g_id", $good_id, PDO::PARAM_INT);
		$stmt->bindParam(":code", $country_code, PDO::PARAM_STR);
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return new self($data);
	}

	/**
	 * Сохранение цены
	 */
	public function save(){
		$query = "INSERT INTO goods2countries (g_id, country_id, country_code, price, price_id)
		          VALUES (:g_id, :country_id, :country_code, :price, :price_id)";
		$stmt = $GLOBALS['DB']->prepare($query);
		$country_id = Country::getId($this->country_code);
		$stmt->bindParam(":g_id", $this->good_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_id", $country_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->bindParam(":price", $this->value, PDO::PARAM_INT);
		$stmt->bindParam(":price_id", $this->id, PDO::PARAM_STR);
		$stmt->execute();

		foreach ($this->getTargets() as $target) {
			$target->save();
		}
	}

}
?>