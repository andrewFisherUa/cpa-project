<?php

class GoodsOptions {

	private $offer_id;
	private $options = array();

	public function __construct( $offer_id = 0 ) {
		$this->offer_id = $offer_id;
		$this->fetchDefaults();
		$this->fetchOptions();
	}

	public function set($name, $value, $country_code = null, $id = null) {
		if (!is_null($id)) {
			$this->options[$name]["id"] = $id;
		}

		if ($country_code) {
			if (!is_array($this->options[$name]["value"])) {
				$this->options[$name]["value"] = array();
			}
			$this->options[$name]["value"][$country_code] = $value;
		} else {
			$this->options[$name]["value"] = $value;
		}
	}

	public function clear($name) {
		unset($this->options[$name]["value"]);
	}

	public function get($name, $country_code = null){
		if (!is_null($country_code)) {
			return $this->options[$name]["value"][$country_code];
		}
		return $this->options[$name]["value"];
	}

	private function fetchDefaults(){
		$stmt = $GLOBALS['DB']->query("SELECT option_name AS name, id, option_value as value FROM options WHERE option_group = 'goods_option' OR option_group = 'content_option'");
		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)){
			$this->set($data["name"], $data["value"], "", $data["id"]);
		}
	}

	/**
	* Получение параметров для конкретного товара
	*
	* @param integer $offer_id Если равен нулю, функция вернет параметры по умолчанию
	*/
	private function fetchOptions() {
		$query = "SELECT t2.option_name AS name, t2.id , t1.value, t1.country_code
			      FROM offer_option AS t1 INNER JOIN options AS t2 ON t1.option_id = t2.id
			      WHERE t1.offer_id = :offer_id";
		$stmt = $GLOBALS["DB"]->prepare( $query );
		$stmt->bindParam(":offer_id", $this->offer_id, PDO::PARAM_INT);
		$stmt->execute();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->set($row['name'], $row['value'], $row['country_code'], $row['id']);
		}
	}

	/**
	* Сохранение параметров товара
	*
	*/
	public function save() {
		$query = "DELETE FROM offer_option WHERE offer_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":id", $this->offer_id, PDO::PARAM_INT);
		$stmt->execute();

		$query = "INSERT INTO offer_option (offer_id, option_id, country_code, value)
		          VALUES (:offer_id, :option_id, :country_code, :value)";
		$stmt = $GLOBALS['DB']->prepare($query);

		foreach ($this->options as &$option) {
			if (is_array($option["value"])) {
				foreach ($option["value"] as $code=>$value) {
					$stmt->bindParam(":offer_id", $this->offer_id, PDO::PARAM_INT);
					$stmt->bindParam(":option_id", $option["id"], PDO::PARAM_INT);
					$stmt->bindParam(":country_code", $code, PDO::PARAM_STR);
					$stmt->bindParam(":value", $option["value"][$code], PDO::PARAM_STR);
					$stmt->execute();
				}
			} else {
				$code = "";
				$stmt->bindParam(":offer_id", $this->offer_id, PDO::PARAM_INT);
				$stmt->bindParam(":option_id", $option["id"], PDO::PARAM_INT);
				$stmt->bindParam(":country_code", $code, PDO::PARAM_STR);
				$stmt->bindParam(":value", $option["value"], PDO::PARAM_STR);
				$stmt->execute();
			}
		}
	}
}

?>