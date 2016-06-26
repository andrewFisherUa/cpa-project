<?php

/**
* Preview_Generator
*
* Класс предназначен для создания превью контента
*
* @author Sorochan Elena
* @version 1.0
*/

abstract class Preview_Generator {

	/**
    * @var string $template_link   Ссылка на шаблон
    * @var string $template_folder Ссылка на папку с шаблоном
    * @var Smarty $smarty          Ссылка на Smarty
    * @var string $assets_folder   Путь к папке со стилями, картинками и js
    * @var string $domen           Домен на котором будут отображаться потоки
    */

	public $content;
	public $template_link;
	public $template_folder;
	public $preview_url;
	public $template_url;
	public $smarty;
	public $assets_folder;
	public $domen;
	public $offer_id;
	protected $template_data;

	/**
	* Конструктор класса
	*
	* Принимает обьект типа Flow
	*
	* @return void
	*/
	public function __construct($content) {
		$this->domen = "//$_SERVER[HTTP_HOST]";
		$this->smarty = $GLOBALS['smarty'];
		$this->content = $content;
		$this->preview_url = "{$_SERVER['DOCUMENT_ROOT']}/content/preview/{$this->content->id}";
		$this->template_data = array();
	}

	public static function create_preview($content, $offer_id = 266, $target_url = "") {
		switch ( $content->type ) {
			case 'landing' : return new Preview_Landing_Generator($content, $offer_id);
			                 break;
			case 'blog' :    return new Preview_Blog_Generator($content, $offer_id, $target_url);
							 break;
			default        : return false;
		}
	}


	/**
	* Функция генерирует контент
	*
	* Определяет данные, необходимые для подключения картинок, css и js к странице.
	*
	* @return void
	**/
	protected function generate() {

		$this->template_link = "file:" . $this->template_folder .  $this->content->link . '/index.tpl';
		$this->template_data["global_url"] = "{$this->domen}/content/assets/general";
		$this->template_data['template_url'] = $this->template_url;
	}

	/**
	* Сохранение сгенерированного контента
	*
	* Функция создает директорию с названием Flow::user_id и директорию для контента с названием $path.
	* В директорию $path в файл index.html записывается сгенерированный контент
	*
	* @return void
	**/
	protected function save_content($filename = "") {
		if (empty($filename)) {
			$filename = "index";
		}
		if (!file_exists( $this->preview_url )) {
			mkdir($this->preview_url);
		}
		$r = $output_file = fopen($this->preview_url . "/{$filename}.html" , "w");
		flock();
		fwrite($output_file, $this->html);
		fclose( $output_file );
	}
}

?>