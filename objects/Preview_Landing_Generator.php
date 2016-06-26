<?php

/**
* Класс Preview_Landing_Generator
*
* Наследует класс Preview_Generator, предназначен для генерирования лендинга.
*
* @author Sorochan Elena
* @version 1.0
*/

class Preview_Landing_Generator extends Preview_Generator {

	/**
	* Конструктор класса
	*
	* Инициализация значений Preview_Generator::$assets_folder, Preview_Generator::$template_folder
	*/
	public function __construct( $content, $offer_id = 266 ) {
		parent::__construct( $content );
		$this->offer_id = is_null($offer_id) ? 266 : $offer_id;
		$this->template_folder = "{$_SERVER['DOCUMENT_ROOT']}/content/landings/";
		$this->template_url = get_site_url() . "/content/landings/" . $this->content->link;
		$this->generate();
	}

	/**
	* Генерирует лендинг
	*
	* Функция загружает все данные, необходимые для создания лендинга ( цены и коды цен, телефоны, адреса, сроки доставки, метрику), отсортированные по странам.
	* Через Preview_Generator::$smarty данные подставляются в шаблон лендинга.
	* Обработанный шаблон записывается в переменную Preview_Generator::$html
	*
	* @return void
	**/
	protected function generate() {
		parent::generate();
		$offer = Offer::getInstance($this->offer_id);
		$stream = new Flow(array("offer_id"=>$this->offer_id));
		$key = "";
		$prices = $stream->getPrices()->getArray();
		foreach ($prices as $code => $item) {
			$this->template_data['country_code'] = $code;
			$this->template_data['currency'] = Country::getCurrency($code);
			$this->template_data['price'] = $item['price'];
			$this->template_data['phone'] = $offer->getOption("phone", $code);
			$this->template_data['address'] = $offer->getOption("address", $code);
			$this->template_data['delivery_time'] = $offer->getOption("delivery_time", $code);
			$this->template_data['form'] =  $this->get_form(array("country_code" => $code,
														          "key" => $key));
			$this->smarty->assign('data', $this->template_data);
			$this->html = $this->smarty->fetch($this->template_link); // заменить все переменные
			$filename = ($this->offer_id == 266) ? "" : "{$this->offer_id}";
			$this->save_content($filename);
			break;
		}
	}

	/**
	* Формирует и возвращает форму заказа, которая подставляется в лендинги
	*
	* @param array $offer Массив с данными о ценах оффера
	*
	* @return string
	**/
	protected function get_form ($data) {
		return '<form class="order-form">
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

	/**
	* Формирует список стран для формы
	*
	* @param array $offer Массив с данными оффера
	*
	* @return string
	**/
	protected function get_country_select( $offer ) {
		$select = '<div class="form-group">
						<select name="country" class="form-group">';
		foreach ( $offer as $country ) {
			$select .= "<option value='".$country['country_code']."'>" . $country['name'] . "</option>";
		}
		return $select . '</select> </div>';
	}

}

?>