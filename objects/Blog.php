<?php

class Blog extends Content {

	public $landings;

	function __construct( $data ) {
		parent::__construct( $data );
		$this->landings = (isset($data['landings'])) ? $data['landings'] : array();
		$this->template_folder = "{$_SERVER['DOCUMENT_ROOT']}/content/blogs/";
	}

	// Save content
  	public function save() {
  		parent::save();
  		return $this->set_landings();
  	}

  	public function set_landings() {
  		$stmt = $GLOBALS['DB']->prepare("DELETE FROM landing_blog WHERE blog_id = :blog_id");
  		$stmt->execute( array( ":blog_id" => $this->id ) );

  		$query = "INSERT INTO landing_blog ( blog_id, landing_id ) VALUES ( :blog_id, :landing_id )";
		$stmt = $GLOBALS['DB']->prepare( $query );
		foreach ( $this->landings as $landing_id  ) {
	      $stmt->execute( array( ":landing_id" => $landing_id, ":blog_id" => $this->id ) );
	    }
  	}

  	public static function get_all () {
	  	$stmt = $GLOBALS['DB']->query( "SELECT * FROM content WHERE type = 'blog' ORDER BY created DESC" );
	  	return $stmt->fetchAll( PDO::FETCH_ASSOC );
	}

	public static function get_by_landing( $landing_id ) {
  		$query = "SELECT c.* FROM content as c INNER JOIN landing_blog as lb ON c.c_id = lb.blog_id AND lb.landing_id = :landing_id";
  		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->execute( array(":landing_id" => $landing_id) );
		return $stmt->fetchAll( PDO::FETCH_ASSOC );
  	}

	public static function get_landings( $blog_id ) {
		$query = "SELECT c.* FROM landing_blog as lb INNER JOIN content as c ON c.c_id = lb.landing_id WHERE lb.blog_id = :blog_id";
		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->execute( array(":blog_id" => $blog_id) );
		return $stmt->fetchAll( PDO::FETCH_ASSOC );
	}

	public static function delete( $id ){
		parent::remove_folder( $id );
	    $query = "DELETE FROM content WHERE c_id = :id;
	              DELETE FROM landing_blog WHERE c_id = :id;
	              DELETE FROM offer_content WHERE blog_id = :id";
	    $stmt = $GLOBALS['DB']->prepare($query);
	    $stmt->execute( array( 'id' => $id) );
	}

   // Get blog link by id
	public static function get_link ( $id ) {
		return self::BLOGS_URL . parent::get_link ( $id );
	}

}

?>