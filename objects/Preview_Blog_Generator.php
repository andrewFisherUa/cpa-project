<?php

/**
* Класс Preview_Blog_Generator
*
* Наследует класс Preview_Generator, предназначен для генерирования блога.
*
* @author Sorochan Elena
* @version 1.0
*/

class Preview_Blog_Generator extends Preview_Generator {

	/**
	* @var string $target_url Ссылка на тестовый лендинг
	*/
	private $target_url;

	/**
	* Конструктор класса
	*
	* Инициализация значений Content_Generator::$id, Content_Generator::$assets_folder, Content_Generator::$template_folder и Content_Generator::$target_url
	*
	* @param string $target_url Ссылка на лендинг
	*
	* @return void
	*/
	public function __construct($content, $offer_id = 266, $target_url = "") {
		parent::__construct( $content );
		$this->template_folder = "{$_SERVER['DOCUMENT_ROOT']}/content/blogs/";
		$this->assets_folder = "{$this->domen}/content/assets/blog_{$this->content->link}/";
		$this->target_url = (empty($target_url)) ? "{$this->domen}/content/preview/80" : $target_url;
		$this->offer_id = $offer_id;
		$this->template_url = get_site_url() . "/content/blogs/" . $this->content->link;
		$this->generate();
	}

	/**
	* Генерирует блог
	*
	* Через Preview_Generator::$smarty заменяются ссылки на лендинг в шаблоне блога.
	* Обработанный шаблон записывается в переменную Preview_Generator::$html
	*
	* @return void
	**/
	protected function generate() {
		parent::generate();
		$this->template_data['target_url'] = $this->target_url;

		//$this->smarty->assign('comebacker', $this->target_url );
		$this->smarty->assign('data', $this->template_data);
		$this->html = $this->smarty->fetch( $this->template_link );
		$filename = ($this->offer_id == 266) ? "" : "{$this->offer_id}";
		$this->save_content($filename);
	}
}

?>