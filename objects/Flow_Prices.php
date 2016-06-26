<?php
/**
* Класс Flow_Prices
*
* @author Sorochan Elena
* @version 1.0
*/

class Flow_Prices {

	/**
	*
	* @var Flow  $flow    Обьект типа flow
	* @var array $prices  Массив цен
	**/

	private $flow_id;
	private $offer;
	private $prices;

	public function __construct($flow_id, $offer_id){
		$this->flow_id = $flow_id;
		$this->offer = Offer::getInstance($offer_id);
		$this->fetch();
	}

	/*
	* Возвращает цены по офферу
	*/
	private function fetchBasic() {
		foreach ($this->offer->getCountries() as $country) {
			$price = $this->offer->getPrice($country)->getValue();
			foreach ($this->offer->getPrice($country)->getTargets() as $t) {
				$target_id = $t->getId();
				$webmaster_commission = $t->getWebmasterCommission();
				$max_price = $t->getMaxPrice();
				break;
			}
			$this->prices[$country] = array(
				"target_id" => $target_id,
				"price" => $price,
				"recommended" => $price,
				"webmaster_commission" => $webmaster_commission,
				"max_profit" => $max_price - ($price - $webmaster_commission),
				"default" => $price - $webmaster_commission,
				"profit" => $webmaster_commission);
		}
	}

	/*
	* Возвращает цены потока
	*/
	private function fetch(){
		$query = "SELECT t1.*, t2.comission_webmaster, t2.max_price
		          FROM flow_prices AS t1 INNER JOIN goods2targets AS t2 ON t1.offer_id = t2.g_id
		          WHERE t1.country_code = t2.country_code AND t1.target_id = t2.t_id AND flow_id = :flow_id
		          ORDER BY t1.created DESC, t1.country_code ASC";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute(array(":flow_id"=>$this->flow_id));

		if ( $stmt->rowCount() == 0 ) {
			return $this->fetchBasic();
		}

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			//if ( isset($this->prices[$row["country_code"]]) )  break;

			$this->prices[$row["country_code"]] = array(
				"target_id" => $row["target_id"],
				"price" => $row["price"],
				"recommended" => $row["recommended"],
				"webmaster_commission" => $row["comission_webmaster"],
				"max_profit" => $row["max_price"] - ($row["recommended"] - $row["comission_webmaster"]),
				"default" => $row["price"] - $row["profit"],
				"profit" => $row["profit"]);
		}

	}

	/*
	* Возвращает таблицу для редактирования цен
	*/
	public function getTable(){
		global $smarty;

		$query = "SELECT * FROM user_target WHERE offer_id = :offer_id AND user_id = :user_id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":offer_id", $this->offer->getId(), PDO::PARAM_INT);
		$stmt->bindParam(":user_id", User::get_current_user_id(), PDO::PARAM_INT);
		$stmt->execute();
		$canEdit = $stmt->rowCount() > 0;

		foreach ($this->prices as $country_code=>&$price) {
			$offer_price = $this->offer->getPrice($country_code);
			$price["country_name"] = Country::getName($country_code);
			$price["currency"] = Country::getCurrencyCode($country_code);
			$price["targets"] = $offer_price->getTargets();
		}

		$smarty->assign('canEdit', $canEdit);
		$smarty->assign('prices', $this->prices);
		return $smarty->fetch('admin' . DS . 'flows' . DS . 'ajax' . DS . 'prices.tpl');
	}

	public function getArray(){
		return $this->prices;
	}

	/**
	*
	* Задание цен
	**/
	public function setProfit($profit, $country_code) {
		$price = $this->prices[$country_code];
		$this->prices[$country_code]["profit"] = $profit;
		$this->prices[$country_code]["price"] = $price["recommended"] -$price['webmaster_commission']+ $profit;
	}

	/**
	*
	* Задание цен
	**/
	public function setTarget($target_id, $country_code) {
		$target = $this->offer->getPrice($country_code)->getTarget($target_id);
		$this->prices[$country_code]['webmaster_commission'] = (is_null($target)) ? 0 : $target->getWebmasterCommission();
		$this->prices[$country_code]["target_id"] = $target_id;
	}

	/*
	* Сохранение цен потока
	*/
	public function save() {
		$created = time();

		$query = "INSERT INTO flow_prices (flow_id, country_code, recommended, price, profit, created, price_id, offer_id, target_id)
		          VALUES (:flow_id, :country_code, :recommended, :price, :profit, :created, :price_id, :offer_id, :target_id)";
		$stmt = $GLOBALS['DB']->prepare($query);
		foreach ( $this->prices as $k=>&$v ) {
			$price_id = $this->offer->getPrice($k)->getId();
			$offer_id = $this->offer->getId();
			$stmt->bindParam(':flow_id', $this->flow_id, PDO::PARAM_INT);
			$stmt->bindParam(':country_code', $k, PDO::PARAM_STR);
			$stmt->bindParam(':recommended', $v['recommended'], PDO::PARAM_INT);
			$stmt->bindParam(':price', $v['price'], PDO::PARAM_INT);
			$stmt->bindParam(':target_id', $v['target_id'], PDO::PARAM_INT);
			$stmt->bindParam(':profit', $v['profit'], PDO::PARAM_INT);
			$stmt->bindParam(':created', $created, PDO::PARAM_INT);
			$stmt->bindParam(':price_id', $price_id, PDO::PARAM_INT);
			$stmt->bindParam(':offer_id', $offer_id, PDO::PARAM_INT);
			$stmt->execute();
		}
	}


	/*
	* Возвращает минимальный размер комиссии по стране
	*/
	private static function getMinComission( $country ){
		$min = array('ua'=>1, 'by'=>100, 'ru'=>1, 'kz'=>1);
		return $min[$country];
	}

	/*
	* Обновление цен потока
	*
	* Цены обновляются если была изменена цена оффера или комиссия
	*/
	public function update(){
		$basic = $this->getBasic();
		$current = $this->getPrices();

		foreach ( $basic as $key=>$value ) {
			if ( $value['recommended'] != $current[$key]['recommended'] ) {
				$current[$key]['recommended'] = $value['recommended'];
				$current[$key]['price'] = $current[$key]['recommended'] - $value['profit'] + $current[$key]['profit'];
			}
		}

		$this->set($current);
		$this->save();
	}
}