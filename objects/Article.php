<?php

/**
* Класс Article
*
* Класс предназначен для управления статьями и категориями в разделе FAQ
*
* @author Sorochan Elena
* @version 1.0
*/

class Article {

	/**
    * @var integer $id          ID статьи
    * @var string  $title       Заголовок статьи
    * @var string  $content     Содержимое статьи
    * @var integer $status      Статус статьи - одно из значений Article::$STATUS_LIST
    * @var integer $modified    Дата редактирования
    * @var integer $rubric      ID рубрики статьи
    * @var array   $status_list Массив всех возможных статусов статей
    */

	public $id;
	public $title;
	public $content;
	public $status;
	public $modified;
	public $rubric;

	public static $status_list = array( 1 => "Модерация", 2 => "Активно", 3 => "Архив" );

	public function __construct( $data ) {
		$this->id = ( isset($data['article_id']) ) ? $data['article_id'] : 0;
        $this->title = ( isset($data['title']) ) ? $data['title'] : 'Без названия';
        $this->content = ( isset($data['content']) ) ? $data['content'] : '';
        $this->status = ( isset($data['status']) ) ? $data['status'] : 1;
        $this->modified = ( isset($data['modified']) ) ? $data['modified'] : time();
        $this->rubric = ( isset($data['rubric_id']) ) ? $data['rubric_id'] : 0;
	}

	/**
	* Возвращает html-разметку в зависимости от статуса
	*
	* @param integer $status - Числовое прдставление статуса
	* @return string html-представление статуса
	*/
	public static function get_status_html( $status ) {
		$class = array( 1=> 'label label-default label-sm',
		    			2=> 'label label-success label-sm',
		                3=> 'label label-warning label-sm' );
		return '<span class="' . $class[$status] . '">' . self::$status_list[$status] . '</span>';
	}

	/**
	* Удаляет статью по ID
	*
	* @param integer $id - ID статьи
	* @return integer Возвращает результат выполнения операции удаления
	*/
	public static function remove( $id ) {
		$stmt = $GLOBALS['DB']->prepare("DELETE FROM articles WHERE article_id = :id");
		return $stmt->execute( array( ":id" => $id ) );
	}

	/**
	* Удаляет рубрику по ID
	*
	* @param integer $id - ID статьи
	* @return integer Возвращает результат выполнения операции удаления
	*/
	public static function remove_rubric( $id ) {
		$stmt = $GLOBALS['DB']->prepare("DELETE FROM rubrics WHERE rubric_id = :id; UPDATE articles SET rubric_id = 0 WHERE rubric_id = :id;");
		return $stmt->execute( array( ":id" => $id ) );
	}

	/**
	* Возвращает список всех рубрик
	*
	* @return array Список рубрик
	*/
	public static function get_rubrics() {
		$stmt = $GLOBALS['DB']->query("SELECT * FROM rubrics ORDER BY weight ASC");
		return $stmt->fetchAll( PDO::FETCH_ASSOC );
	}

	/**
	* Возвращает рубрику по ID
	*
	* @return array Данные рубрики
	*/
	public static function get_rubric( $id ) {
		if ( $id == 0 ) {
			return array( "name" => "Без рубрики" );
		}
		$stmt = $GLOBALS['DB']->prepare("SELECT * FROM rubrics WHERE rubric_id = :id");
		$stmt->execute( array( ":id" => $id ) );
		return $stmt->fetch( PDO::FETCH_ASSOC );
	}

	/**
	* Сохранение статьи
	*
	* @return integer Возвращает результат выполнения операции сохранения
	*/
	public function save() {
		$this->modified = time();

		if ( $this->id > 0 ) {
			$query = "UPDATE articles SET title = :title, content = :content, status = :status, modified = :modified, rubric_id = :rubric_id WHERE article_id = :id";
		} else {
			$query = "INSERT INTO articles ( title, content, status, modified, rubric_id )
		              VALUES ( :title, :content, :status, :modified, :rubric_id )";
		}

		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->bindParam( ":title", $this->title, PDO::PARAM_STR );
		$stmt->bindParam( ":content", $this->content, PDO::PARAM_STR );
		$stmt->bindParam( ":status", $this->status, PDO::PARAM_INT );
		$stmt->bindParam( ":modified", $this->modified, PDO::PARAM_INT );
		$stmt->bindParam( ":rubric_id", $this->rubric, PDO::PARAM_INT );
		if ( $this->id > 0 ) {
			$stmt->bindParam( ":id", $this->id, PDO::PARAM_INT );
		}

		$r = $stmt->execute();
		if ( $this->id > 0 ) {
			$this->id = $GLOBALS['DB']->lastInsertId();
		}
		return $r;
	}

	public static function save_rubric( $data ) {
		if ( $data['id'] > 0 ) {
			$query = "UPDATE rubrics SET name = :name, css = :css, weight = :weight WHERE rubric_id = :id";
		} else {
			$query = "INSERT INTO rubrics (name, css, weight) VALUES ( :name, :css, :weight )";
		}

		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->bindParam( ":name", $data['name'], PDO::PARAM_STR );
		$stmt->bindParam( ":css", $data['css'], PDO::PARAM_STR );
		$stmt->bindParam( ":weight", $data['weight'], PDO::PARAM_INT );

		if ( $data['id'] > 0 ) {
			$stmt->bindParam( ":id", $data['id'], PDO::PARAM_INT );
		}

		return $stmt->execute();
	}

	/**
	* Возвращает список всех статей
	*
	* @return array Список статей
	*/
	public static function get_all( $filters = array() ) {
		$query = "SELECT * FROM articles";
		$where = array();

		if ( isset($filters['date_from']) ) {
			$where[] = "modified > :date_from";
		}

		if ( isset($filters['date_to']) ) {
			$where[] = "modified < :date_to";
		}

		if ( isset($filters['title']) ) {
			$filters['title'] = "%" . $filters['title'] . "%";
			$where[] = "title LIKE :title";
		}

		if ( isset($filters['status']) ) {
			$where[] = "status = :status";
		}

		if ( isset($filters['rubric_id']) ) {
			$where[] = "rubric_id = :rubric_id";
		}

		if ( count($where) ) {
			$query .= " WHERE " . implode(" AND ", $where);
		}

		$query .= " ORDER BY modified DESC";
		$stmt = $GLOBALS['DB']->prepare($query);

		foreach ( $filters as $name=>&$filter ) {
			$paramType = ( is_int($filter) ) ? PDO::PARAM_INT : PDO::PARAM_STR;
			$stmt->bindParam(":{$name}", $filter, $paramType);
		}

		$stmt->execute();
		return $stmt->fetchAll( PDO::FETCH_ASSOC );
	}

	/**
	* Возвращает статью по ID
	*
	* @param integer $id ID статьи
	* @return array Данные по статье
	*/
	public static function get_by_id( $id ) {
		$stmt = $GLOBALS['DB']->prepare("SELECT * FROM articles WHERE article_id = :id");
		$stmt->execute( array( ":id" => $id ) );
		return $stmt->fetch( PDO::FETCH_ASSOC );
	}

}

?>