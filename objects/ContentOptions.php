<?php

/**
* Класс ContentOptions отвечает за параметры контента
*
* @var integer $offer_id ID товара
*/

class ContentOptions {
	protected $options = array();

	public function __construct() {
		$this->fetchOptions();
	}

	public function set($name, $country_code, $value, $id = null){
		if (!is_null($id)) {
			$this->options[$name]["id"] = $id;
		}
		$this->options[$name]["values"][$country_code] = $value;
 	}

 	public function get($name, $country_code){
 		return $this->options[$name][$country_code];
 	}

 	private function fetchDefaults(){
 		$countries = Country::getAll();
 		$sql = "SELECT id, option_name as name, option_value as value FROM options WHERE option_group = 'content_option'";
		$stmt = $GLOBALS['DB']->query($sql);
		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
			foreach ($countries as $c) {
				$this->set($data['name'], $c["code"], $data["value"], $data["id"]);
			}
		}
 	}

	/**
	* Получение параметров
	*
	* @param integer $offer_id Если равен нулю, функция вернет параметры по умолчанию
	*/
	protected function fetchOptions() {
		$query = "SELECT t1.option_name as name, t2.country_code, t2.option_id as id, t2.value
		          FROM options AS t1 INNER JOIN content_option AS t2 ON t1.id = t2.option_id
		          WHERE t1.option_group = 'content_group'";
		$stmt = $GLOBALS['DB']->query($query);

		if ($stmt->rowCount() == 0) {
			$this->fetchDefaults();
		} else {
			while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$this->set($data['name'], $data['country_code'], $data["value"], $data["id"]);
			}
		}
	}

	/**
	* Сохранение параметров
	*
	*/
	public function save() {
		// Удалить старые, записать новые
	}
}

?>