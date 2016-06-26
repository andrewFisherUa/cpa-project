<?php

abstract class Goods {

	protected $gid; // ID товара по внешней базе
	protected $id;
	protected $name;
	protected $user_id;
	protected $status;
	protected $main_image;
	protected $created;
	protected $modified;
	protected $categories;
	protected $countries;
	protected $prices;
	protected $qty;
	protected $country_code;
	protected $options;
	protected $available_in_shop;
	protected $available_in_offers;
	protected $priority;

	const STATUS_MODERATION = "moderation";
	const STATUS_ACTIVE = "active";
	const STATUS_DISABLED = "disabled";
	const STATUS_ARCHIVE = "archive";

	/**
	 *
	 * @param data
	 * @param country_code
	 */
	public function __construct($data, $country_code = null) {
		$this->country_code = $country_code;
		$this->id = (isset($data['id'])) ? $data['id'] : 0;
		$this->gid = (isset($data['gid'])) ? $data['gid'] : 0;
		$this->name = (isset($data['name'])) ? $data['name'] : "";
		$this->user_id = (isset($data['user_id'])) ? $data['user_id'] : 0;
		$this->status = (isset($data['status'])) ? $data['status'] : self::STATUS_MODERATION;
		$this->created = (isset($data['created'])) ? $data['created'] : time();
		$this->modified = (isset($data['modified'])) ? $data['modified'] : time();
		$this->available_in_shop = (isset($data['available_in_shop'])) ? $data['available_in_shop'] : 1;
		$this->available_in_offers = (isset($data['available_in_offers'])) ? $data['available_in_offers'] : 1;
		$this->priority = (isset($data['priority'])) ? $data['priority'] : 0;
	}

	public function getStatusLabel(){
		switch ($this->status) {
			case self::STATUS_MODERATION : return "На модерации";
			case self::STATUS_ACTIVE : return "Активный";
			case self::STATUS_DISABLED : return "Отключен";
			case self::STATUS_ARCHIVE : return "Архив";
		}
	}

	public static function getStatusList(){
		return array(
			array("status" => self::STATUS_MODERATION, "label" => "На модерации"),
			array("status" => self::STATUS_ACTIVE, "label" => "Активный"),
			array("status" => self::STATUS_DISABLED, "label" => "Отключен"),
			array("status" => self::STATUS_ARCHIVE, "label" => "Архив"));
	}

	public function isAvailableInShop(){
		return $this->available_in_shop == 1;
	}

	public function isAvailableInOffers(){
		return $this->available_in_offers == 1;
	}

	public function setAvailableInShop($param){
		$this->available_in_shop = $param;
	}

	public function setAvailableInOffers($param){
		$this->available_in_offers = $param;
	}

	public function isTop(){
		return $this->priority == 1;
	}

	/**
	 * Возвращает ID товара
	 */
	public function getID() {
		return $this->id;
	}

	/**
	 * Возвращает ID товара по внешней базе
	 */
	public function getGID() {
		return $this->gid;
	}

	/**
	 * Возвращает ID рекламодателя, которому принадлежит товар
	 */
	public function getOwnerId(){
		return $this->user_id;
	}

	public function hasOwner($user_id){
		return $this->user_id == $user_id;
	}

	public function getName(){
		return $this->name;
	}

	/**
	 * Возвращает статус товара
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Возвращает дату создания товара
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * Возвращает дату редактирования товара
	 */
	public function getModified() {
		return $this->modified;
	}

	/**
	 * Устанавливает название товара
	 *
	 * @param name
	 */
	public function setName($param) {
		$this->name = $param;
	}

	/**
	 * Устанавливает главное изображение
	 *
	 * @param main_image
	 */
	public function setMainImage($param){
		$this->main_image = $param;
	}

	/**
	 * Устанавливает статус
	 *
	 * @param status
	 */
	public function setStatus($param){
		switch ($param) {
			case self::STATUS_MODERATION : $this->status = self::STATUS_MODERATION;
			                               break;
			case self::STATUS_ACTIVE     : $this->status = self::STATUS_ACTIVE;
			                               break;
			case self::STATUS_DISABLED   : $this->status = self::STATUS_DISABLED;
			                               break;
			case self::STATUS_ARCHIVE    : $this->status = self::STATUS_ARCHIVE;
			                               break;
			default: $this->status = self::STATUS_MODERATION;
		}
	}

	public function setOwnerId($id){
		$this->user_id = $id;
	}

	public function setGID($gid) {
		$this->gid = $gid;
	}

	/**
	 * Удаление товара
	 *
	 * @param id
	 */
	public static function delete($id){
		$stmt = $GLOBALS['DB']->prepare("DELETE FROM goods WHERE id = :id");
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		return $stmt->execute();
	}

	/**
	 * Задает значение параметра
	 *
	 * @param name
	 * @param value
	 */
	public function setOption($name, $value, $country_code){
		$this->getOptions()->set($name, $value, $country_code);
	}

	/**
	 *  Возвращает категории, к которым принадлежит товар
	 */
	public function getCategories(){
		$this->fetchCategories();
		return $this->categories;
	}

	/**
	 * Достает из БД категории, к которым принадлежит товар
	 */
	protected function fetchCategories(){
		if (!empty($this->categories)) {
			return false;
		}

		$query = "SELECT c.id, c.name FROM categories AS c INNER JOIN goods2categories AS gc ON c.id = gc.c_id WHERE gc.g_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
		$stmt->execute();
		$this->categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Возвращает список стран, в которых доступен товар
	 */
	public function getCountries(){
		$this->fetchCountries();
		return $this->countries;
	}

	/**
	 * Достает из БД список стран, в которых доступен товар
	 */
	protected function fetchCountries(){
		if (!empty($this->countries)) {
			return false;
		}

		if (!is_null($this->country_code)) {
			$this->countries = array($this->country_code);
		}

		$query = "SELECT c.code FROM goods2countries as gc INNER JOIN country as c ON c.code=gc.country_code AND gc.g_id = :id";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        	$this->countries[] = $row["code"];
        }
	}

	/**
	 * Возвращает цену товара
	 *
	 * @param country_code
	 */
	public function getPrice($country_code = null){
		if (is_null($country_code) && is_null($this->country_code)) {
			return false;
		}

		$this->fetchPrices();

		$code = (is_null($this->country_code)) ? $country_code : $this->country_code;
		return $this->prices[$code];
	}

	/**
	* Возвращает все цены товара
	*/
	public function getPrices(){
		$this->fetchPrices();
		return $this->prices;
	}

	/**
	 * Достает из БД цену товара
	 */
	protected function fetchPrices(){
		if (!empty($this->prices)) {
			return false;
		}

		$this->fetchCountries();
		if (!empty($this->countries)) {
			foreach ($this->countries as $code) {
				$this->prices[$code] = Price::getInstance($this->id, $code);
			}
		}
	}

	public function getPricesView( $view = "full" ){
        global $smarty;
        $smarty->assign('view', $view);
        $smarty->assign('countries', Country::getAll());
        $smarty->assign('prices', $this->getPrices());
        $smarty->assign('admin', User::isAdmin());
        return $smarty->fetch('admin' . DS . 'offers' . DS . 'ajax' . DS . 'test-target-table.tpl');
    }

	public function hasTarget($target_id, $country_code = null){
		if (!is_null($country_code)) {
			return $this->getPrice($country_code)->getTarget($target_id);
		}

		$this->fetchPrices();

		foreach ($this->prices as $price){
			if (!is_null($price->getTarget($target_id))) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Возвращает количество товара
	 *
	 * @param country_code
	 */
	public function getQty($country_code = null){
		if (is_null($country_code) && is_null($this->country_code)) {
			return false;
		}

		$this->fetchQty();

		$code = (is_null($this->country_code)) ? $country_code : $this->country_code;
		return $this->qty[$code];
	}

	public function setQty($param, $country_code = null){
		if (is_null($country_code) && is_null($this->country_code)) {
			return false;
		}

		$code = (is_null($this->country_code)) ? $country_code : $this->country_code;
		$this->qty[$code] = $param;
	}

	/**
	 * Достает из БД количество товара
	 */
	protected function fetchQty(){
		if (!empty($this->qty)) {
			return false;
		}

		$this->fetchCountries();
		foreach ($this->countries as &$code) {
			$query = "SELECT qty FROM goods2countries WHERE country_code = :code AND g_id = :id";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
			$stmt->bindParam(":code", $code, PDO::PARAM_INT);
			$stmt->execute();
			$this->qty[$code] = $stmt->fetchColumn();
		}
	}

	/**
	 * Проверяет принадлежит ли товар к категории
	 *
	 * @param id
	 */
	public function hasCategory($id){
		$this->fetchCategories();

		$ids = array_column($this->categories, "id");
		return in_array($id, $ids);
	}

	public function inCountry($code){
		$this->fetchCountries();
		return in_array($code, $this->countries);
	}

	/**
	 * Добавление страны
	 */
	public function addCountry($country_code){
		$this->fetchCountries();
		if (!in_array($country_code, $this->countries)) {
			$this->countries[] = $country_code;
		}
	}

	public function unsetCountry($country_code){
		$this->fetchCountries();
		unset($this->countries[$country_code]);
		unset($this->prices[$country_code]);
		unset($this->qty[$country_code]);
	}

	public function unsetCountries(){
		unset($this->countries);
		unset($this->prices);
		unset($this->qty);
	}

	public function addPrice($id, $value, $country_code){
		$good_id = $this->id;
		$data = compact("country_code", "id", "value", "good_id");
		$this->prices[$country_code] = new Price($data);

	}

	public function unsetPrice($country_code) {
		$this->fetchPrices();
		unset($this->prices[$country_code]);
	}

	private function _priceChanged(){

		$prices = [
			"old" => [],
			"new" => [],
		];

		// Старые цены
		$query = "SELECT t1.price, t2.comission_webmaster AS profit, t2.comission AS um_profit, t1.country_code, t2.t_id
				  FROM goods2countries AS t1, goods2targets AS t2
				  WHERE t1.g_id = t2.g_id AND t1.country_code = t2.country_code";
		
		$stmt = $GLOBALS["DB"]->query($query);
		while ($a = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if (!isset($prices["old"][$a["country_code"]])) {
				$prices["old"][$a["country_code"]] = [
					"price" => $a["price"],
					"targets" => []
				];
			}
			
			$prices["old"][$a["country_code"]]["targets"][$a["t_id"]] = [
				"profit" => $a["profit"],
				"um_profit" => $a["um_profit"]
			];
		}

		// Новые цены
		foreach ($this->prices as $price) {
			
			$prices["new"][$price->getCountryCode()] = [
				"price" => $price->getValue(),
				"targets" => []
			];

			foreach ($price->getTargets() as $target) {
				$prices["new"][$price->getCountryCode()]["targets"][$target->getId()] = [
					"profit" => $target->getWebmasterCommission(),
					"um_profit" => $target->getCommission()
				];
			}
		}

		return $prices["new"] != $prices["old"];
	}

	public function savePrices(){

		if ( $this->_priceChanged() ) {
			
			// Если цены изменились, добавляем пометку goods.price_upd = 1
			// goods.price_upd используется для обновления цен в потоках по крону

			$query = "UPDATE goods SET price_upd = 1 WHERE id = ?";
			$stmt = $GLOBALS["DB"]->prepare($query);
			$stmt->execute([
				$this->id
			]);
		}

		$query = "DELETE FROM goods2countries WHERE g_id = ?";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([
			$this->id
		]);

		$query = "DELETE FROM goods2targets WHERE g_id = ?";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([
			$this->id
		]);

		foreach ($this->prices as $price) {


			$price->save();
		}
		
		$this->saveQty();
	}

	private function saveQty(){
		$query = "UPDATE goods2countries SET qty = :qty WHERE country_code = :country_code AND g_id = :g_id";
		$stmt = $GLOBALS['DB']->prepare($query);
		foreach ($this->qty as $country_code => &$value) {
			$stmt->bindParam(":qty", $value, PDO::PARAM_INT);
			$stmt->bindParam(":g_id", $this->id, PDO::PARAM_INT);
			$stmt->bindParam(":country_code", $country_code, PDO::PARAM_STR);
			$stmt->execute();
		}
	}

	/**
	 * Возвращает главное изображение товра
	 */
	public function getMainImage() {
		$this->fetchMainImage();
		return $this->main_image;
	}

	/**
	 * Возвращает главное изображение товра
	 */
	public function getMainImagePath() {
		$img = $this->getMainImage();
		if (empty($img)) {
			return "/misc/images/images/placeholder.jpg";
		}
		return '/misc/images/goods/'. $img['name'];
	}

	abstract protected function fetchMainImage();

	/**
	 * Сохранение товара
	 */
	abstract public function save();

	public function getOptions(){
		$this->fetchOptions();
		return $this->options;
	}

	public function getOption($name, $country_code = null){
		if (!is_null($country_code)) {
			return $this->getOptions()->get($name, $country_code);
		}
		return $this->getOptions()->get($name);
	}

	/**
	 * Достает из БД параметры оффера
	 */
	protected function fetchOptions(){
		if (!empty($this->options)) {
			return false;
		}
		$this->options = new GoodsOptions($this->id);
	}


	/**
	 * Возвращает список всех товаров
	 */
	static public function getAll(){
		//
	}

	/**
	 * Возвращает список товаров, отфильтрованный согласно $filters
	 *
	 * @param filters
	 */
	public static function getFiltered($items, $filters){
		$filtered = array();
		for ($i=0; $i < count($items); $i++) {
			if (isset($filters['id']) && $items[$i]->getId() != $filters['id'] ) {
				continue;
			}
			if (isset($filters['country_code']) && !$items[$i]->inCountry($filters['country_code'])) {
		      continue;
		    }

		    if (isset($filters['target']) && !$items[$i]->hasTarget($filters['target'], $filters['country_code'])) {
		      continue;
		    }

		    if (isset($filters['category']) && !$items[$i]->hasCategory($filters['category'])) {
		        continue;
		    }

		    if (isset($filters['status']) && !($items[$i]->getStatus() == $filters['status'])) {
		        continue;
		    }
		    $filtered[] = $items[$i];
		}

		return $filtered;
	}

	static public function getDefaultRefprofits(){
		$stmt = $GLOBALS['DB']->query("SELECT * FROM default_refprofits");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		    $data[$row["country_code"]]["type"] = $row["type"];
		    $data[$row["country_code"]]["levels"][$row["level"]] = $row["value"];
		}

		return $data;
	}
}
?>