<?php

class Content {

	public $id;
	public $name;
	public $type;
	public $link;
	public $created;
	public $group;
	public $template_folder;
	public $assets_folder;
	public $status;
	const BLOGS_URL = "http://univer-mag.com/blogs/";
	const LANDINGS_URL = "http://univer-mag.com/landings/";

	function __construct( $data ) {
		if ( isset($data['c_id']) ) $data['id'] = $data['c_id'];
		$this->id = (isset($data['id'])) ? $data['id'] : 0;
		$this->name = (isset($data['name'])) ? $data['name'] : "";
		$this->link = (isset($data['link'])) ? $data['link'] : "";
		$this->type = (isset($data['type'])) ? $data['type'] : "";
		$this->created = (isset($data['created'])) ? $data['created'] : time();
		$this->assets_folder = "{$_SERVER['DOCUMENT_ROOT']}/content/assets/";
		$this->status = (isset($data['status'])) ? $data['status'] : 0;
	}

	public function getInstance($id) {
		$data = self::get_by_id($id);
		return new self($data);
	}

	// Save landing / blog
	public function save () {
		if ( $this->id == 0 ) {
			// Add new content
			$this->add();
		} else {
			// Update existing content
			$this->update();
		}
		//Preview_Generator::create_preview($this);
	}

	// Add new content
	protected function add() {
		$query = "INSERT INTO content ( name, type, link, created ) VALUES ( :name, :type, :link, :created )";
		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
		$stmt->bindParam(':type', $this->type, PDO::PARAM_STR);
		$stmt->bindParam(':link', $this->link, PDO::PARAM_STR);
		$stmt->bindParam(':created', $this->created, PDO::PARAM_INT);
		$r = $stmt->execute();
		if ( $r == false ) return false;
		$this->id = $GLOBALS['DB']->lastInsertId();
	}

	// Update content
	protected function update() {
		$query = "UPDATE content SET name = :name, link = :link WHERE c_id = :id";
		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
		$stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
		$stmt->bindParam(':link', $this->link, PDO::PARAM_STR);
		$stmt->execute();
	}

	public function check_field( $field_name, $field_val ) {
		$query = "SELECT * FROM content WHERE {$field_name} = :value AND type = :type AND c_id != :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam("value", $field_val, PDO::PARAM_STR);
		$stmt->bindParam(':type', $this->type, PDO::PARAM_STR);
		$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
		$stmt->execute();
		return (bool) $stmt->rowCount();
	}

	// Get content groups
	public static function get_groups() {
		$stmt = $GLOBALS['DB']->query("SELECT * FROM groups ORDER BY name");
		return $stmt->fetchAll( PDO::FETCH_ASSOC );
	}

	// Get content group by id
	public static function get_group ( $id ) {
		$query = "SELECT * FROM groups WHERE g_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute( array( 'id' => $id) );
		return $stmt->fetch( PDO::FETCH_ASSOC );
	}

	// Add content group
	public static function add_group ( $name ) {
		if ( self::get_group_by_name($name) != false ) return false;

		$query = "INSERT INTO groups (name) VALUES (:name)";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->execute();
		return $GLOBALS['DB']->lastInsertId();
	}

	// Update content group
	public static function upd_group ( $id, $name ) {
		$r = self::get_group_by_name($name);
		if ( $r['g_id'] != $id && $r != false ) return false;

		$query = "UPDATE groups SET name = :name WHERE g_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		return $stmt->execute();
	}

	// Get content group by name or return false if group doesn't exist
	public static function get_group_by_name ( $name ) {
		$query = "SELECT * FROM groups WHERE name = :name";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		if ( $stmt->execute() ) return $stmt->fetch( PDO::FETCH_ASSOC );
		return false;
	}

	// Remove content group
	public static function delete_group ( $id ) {
		$query = "DELETE FROM groups WHERE g_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		return $stmt->execute( array( 'id' => $id) );
	}

	// Get group
	public static function get_content_group ( $id ) {
		$query = "SELECT g.* FROM groups g INNER JOIN content_group gc ON g.g_id = gc.g_id AND gc.c_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute( array( 'id' => $id) );
		return $stmt->fetch( PDO::FETCH_ASSOC );
	}

	// Get content group s
	public static function get_content_groups ( $id ) {
		$query = "SELECT g.* FROM groups g INNER JOIN content_group gc ON g.g_id = gc.g_id AND gc.c_id = :id";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute( array( 'id' => $id) );
		return $stmt->fetchAll( PDO::FETCH_ASSOC );
	}

	// Get content by id
	public static function get_by_id ( $id ) {
	  	$query = "SELECT *, c_id as id FROM content WHERE c_id = :id";
	  	$stmt = $GLOBALS['DB']->prepare( $query );
	  	$stmt->execute( array( ':id' => $id ) );
	  	return $stmt->fetch( PDO::FETCH_ASSOC );
	}

	// Get content link by id
	public static function get_link ( $id ) {
		$instance = self::get_by_id( $id );
		return $instance["link"];
	}

	/**
	* Получение ссылки на превью контента
	*
	* @var integer $id ID контента
	*/
	public static function get_preview_link($content_id, $offer_id) {
		$filename = (empty($offer_id)) ? "index" : $offer_id;
		/*
		if (!file_exists("{$_SERVER['DOCUMENT_ROOT']}/content/preview/{$content_id}/{$filename}.html")) {
			Preview_Generator::create_preview(Content::getInstance($content_id), $offer_id);
		}
		*/
		Preview_Generator::create_preview(Content::getInstance($content_id), $offer_id);
		return "/content/preview/{$content_id}/{$filename}.html";
	}

	// Get content name by id
	public static function get_name ( $id ) {
		$instance = self::get_by_id( $id );
		return $instance["name"];
	}

	// Удалить папку при удалении контента
  	public static function remove_folder( $id ) {
	    $data = self::get_by_id( $id );
	    $folder = $data["type"] . "s";
	    $cfolder = "{$_SERVER['DOCUMENT_ROOT']}/content/{$folder}/" . $data["link"];
	    $afolder = "{$_SERVER['DOCUMENT_ROOT']}/content/assets/" . $data["type"] . "_" . $data["link"];

	    if ( is_dir( $cfolder ) ) rmdir_recursive( $cfolder );
	    if ( is_dir( $afolder ) ) rmdir_recursive( $afolder );
	}

}