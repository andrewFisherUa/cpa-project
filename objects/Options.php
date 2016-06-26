<?php

/**
* Класс Options отвечает за параметры
*
* @var array $option     Список параметров
*/

class Options {

	protected $options = array();

	/**
	* Конструктор класса вызывает метод Options::get_all() для получения всех параметров
	*
	* Параметры записываются в массив Options::$options
	*
	* @param string $opt_group Если указан в $options будут только параметры определенной группы
	* @return void
	*/
	public function __construct( $opt_group = '' ) {
		$query = "SELECT * FROM options";
		if ( $opt_group ) {
			$query .= " WHERE option_group = :option_group";
		}
		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->bindParam(":option_group", $opt_group, PDO::PARAM_STR);
		$stmt->execute();
		while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
			$this->set_option( $row['option_name'],
							   array( 'value' => $row['option_value'],
							   	      'id'    => $row['id'] ));
		}
	}

	/**
	* Возвращает обьект-наследник Options по значению $type
	*
	* @var string $type Тип параметров, например $type = 'offer' создаст обьект Offer_Options/
	* Если тип не указан, создастся обьект типа Options
	*
	* @return Options Возвращает обьект типа Options
	*/
	public static function get_options( $type = "", $id = 0 ) {
		switch ( $type ) {
			case "goods" : return new GoodsOptions( $id );
						   break;
			case "offer" : return new Offer_Options( $id );
						   break;
			case "user"  : return new User_Options( $id );
						   break;
			default      : return new self();
		}
	}

	/**
	* Добавление параметра в массив Options::$options
	*
	* @param string $option_name Название параметра
	* @param string $option_value Значение параметра
	* @return void
	*/
	public function set_option( $option_name, $option_value ) {
		if ( !is_array( $option_value ) ) {
			$this->options[$option_name]['value'] = $option_value;
		} else $this->options[$option_name] = $option_value;
	}

	/**
	* Получение значения параметра по названию
	*
	* Возвращает элемент массива Options::$options
	*
	* @param string $option_name Название параметра
	* @return boolean|string Возвращает значение искомого параметра, или FALSE если параметр с указанным именем не найден
	*/
	public function get_option( $option_name ) {
		if ( isset( $this->options[$option_name] ) ) {
			return $this->options[$option_name]["value"];
		}
		return false;
	}

	public function getId($option_name) {
		return $this->options[$option_name]["id"];
	}

	/**
	* Сохранение нового параметра в базу данных
	*
	* @param string $option_name  Название параметра
	* @param string $option_value Значение параметра
	* @param string $option_group Название группы параметров
	* @return void
	*/
	public static function add( $option_name, $option_value, $option_group = "", $option_desc = "" ) {
		// Проверяем есть ли уже в базе параметр с таким именем и в той же группе
		$query = "SELECT id FROM options WHERE option_name = :option_name AND option_group = :option_group";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam( ":option_name", $option_name, PDO::PARAM_STR );
		$stmt->bindParam( ":option_group", $option_group, PDO::PARAM_STR );
		$stmt->execute();
		if ( $stmt->rowCount() > 0 ) {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			return $row['id'];
		}

		// Если параметра с таким названием нет, создаем его
		$query = "INSERT INTO options ( option_name, option_value, option_group, option_desc ) VALUES ( :option_name, :option_value, :option_group, :option_desc )";
		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->bindParam( ":option_name", $option_name, PDO::PARAM_STR );
		$stmt->bindParam( ":option_value", $option_value, PDO::PARAM_STR );
		$stmt->bindParam( ":option_group", $option_group, PDO::PARAM_STR );
		$stmt->bindParam( ":option_desc", $option_desc, PDO::PARAM_STR );
		$stmt->execute();
		return $GLOBALS['DB']->lastInsertId();
	}

	/**
	* Сохранение всех параметров из массива Offers::$options в базу данных
	*
	* @return void
	*/
	public function save() {
		$query = "UPDATE options SET option_value = :option_value WHERE option_name = :option_name";
		$stmt = $GLOBALS['DB']->prepare( $query );
		foreach ( $this->options as $option_name =>&$option_value ) {
			$stmt->bindParam( ":option_name", $option_name, PDO::PARAM_STR );
			$value = ( is_array($option_value) ) ? $option_value['value'] : $option_value;
			$stmt->bindParam( ":option_value", $value, PDO::PARAM_STR );
			$stmt->execute();
		}
	}
}

?>