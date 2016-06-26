<?php

/**
* Класс Blog_Generator
*
* Наследует класс Content_Generator, предназначен для генерирования блога.
*
* @author Sorochan Elena
* @version 1.0
*/

class Blog_Generator extends Content_Generator {

	/**
	* @var string $target_url Ссылка на лендинг, на который осуществляется переход с блога
	*/
	public $target_url;

	/**
	* Конструктор класса
	*
	* Инициализация значений Content_Generator::$id, Content_Generator::$assets_folder, Content_Generator::$template_folder и Content_Generator::$target_url
	*
	* @param string $target_url Ссылка на лендинг
	*
	* @return void
	*/
	public function __construct($flow, $target_url, $alias = null) {
		parent::__construct($flow, $alias);
		$this->id = $flow->getBlogId();
		$this->template_folder = "{$_SERVER['DOCUMENT_ROOT']}/content/blogs/";
		$link = Content::get_link( $this->id );
		$this->assets_folder = "{$this->domen}/content/assets/blog_{$link}/";
		$this->target_url = STREAMS_URL . "/{$target_url}";
		$this->template_url = get_site_url() . "/content/blogs/" . Content::get_link($this->id);
	}

	/**
	* Генерирует лендинг
	*
	* Через Content_Generator::$smarty заменяются ссылки на лендинг в шаблоне блога.
	* Обработанный шаблон записывается в переменную Content_Generator::$html
	*
	* @return void
	**/
	public function generate() {
		parent::generate();
		$this->template_data['required'] = "<script src='" . $this->template_data['global_url'] . "/js/subid.min.js'></script>";
		$this->template_data['global_url'] = "http://s.umseller.com/";
		$this->template_data['target_url'] = $this->target_url;
		$this->smarty->assign('data', $this->template_data);
		$this->html = $this->smarty->fetch($this->template_link);
		$this->save_content();
		return $this->link;
	}
}

?>