<?php

/**
* Класс Landing_Generator
*
* Наследует класс Content_Generator, предназначен для генерирования лендинга.
*
* @author Sorochan Elena
* @version 1.0
*/


class Landing_Generator extends Content_Generator {

	/**
	* Конструктор класса
	*
	* Инициализация значений Content_Generator::$id, Content_Generator::$assets_folder, Content_Generator::$template_folder
	*/
	public function __construct($flow, $alias = null) {
		parent::__construct($flow, $alias);
		$this->id = $flow->getLandingId();
		$link = Content::get_link( $this->id );
		$this->assets_folder = "{$this->domen}/content/assets/landing_{$link}/";
		$this->template_folder = $_SERVER['DOCUMENT_ROOT'] . "/content/landings/";
		$this->template_url = get_site_url() . "/content/landings/" . Content::get_link($this->id);
	}

	private function getDefaultCountry($codes) {
		$p = ["ru", "ua", "kz", "by"];

		foreach ($p as $a) {
			if (in_array($a, $codes)) {
				return $a;
			}
		}
	}

	public function generate(){
		parent::generate();
		$i = 0;
		$offer = Offer::getInstance($this->flow->getOfferId());
		$key = $this->flow->getKey();
		$prices = $this->flow->getPrices()->getArray();

		$d = [
			"ua" => "1-3",
			"ru" => "5-15",
			"by" => "4-8",
			"kz" => "3-8",
		];

		$default_country = $this->getDefaultCountry(array_keys($prices));

		foreach ($prices as $code => $item) {
			$this->template_data['country_code'] = $code;
			$this->template_data['currency'] = Country::getCurrency($code);
			$this->template_data['price'] = $item['price'];
			$this->template_data['phone'] = $offer->getOption("phone", $code);
			$this->template_data['address'] = $offer->getOption("address", $code);
			//$this->template_data['delivery_time'] = $offer->getOption("delivery_time", $code);
			$this->template_data['delivery_time'] = $d[$code];

			$this->template_data['form'] =  $this->get_form(array("country_code" => $code,
														          "key" => $key));
			$this->smarty->assign('data', $this->template_data);
			$this->html = $this->smarty->fetch($this->template_link); // заменить все переменные
			$this->save_content($code);
			if (!$this->flow->hasTrafficback() && $code == $default_country) {
				$this->save_content();
			}
			$i++;
		}
		return $this->link;
	}

	/**
	* Формирует и возвращает форму заказа, которая подставляется в лендинги
	*
	* @param array $offer Массив с данными о ценах оффера
	*
	* @return string
	**/
	protected function get_form ($data) {
		return '<form class="order-form" action="' .ORDERS_HANDLER_URL .'" method="POST" accepted-charset="utf-8">
		         <input type="hidden" name="country" value="' . $data['country_code'] . '">
		         <input type="hidden" name="key" value="'.$data['key'].'">
		         <div class="form-group">
					<label for="name_last" class="control-label">Фамилия:</label>
					<input type="text" class="form-control" name="name_last" id="name_last" required>
				</div>
				<div class="form-group">
					<label for="name_first" class="control-label">Имя:</label>
					<input type="text" class="form-control" name="name_first" id="name_first" required>
				</div>
				<div class="form-group">
					<label for="phone" class="control-label">Телефон:</label>
					<input type="text" class="form-control" name="phone" id="phone" required>
				</div>
				<div class="form-group">
					<button type="submit" name="submit">Отправить заказ</button>
				</div></form>';
	}
}

?>