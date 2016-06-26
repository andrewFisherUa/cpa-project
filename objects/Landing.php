<?php

class Landing extends Content {

  public $groups = array();

  function __construct( $data ) {
    parent::__construct( $data );
    $this->groups = (isset($data['groups'])) ? $data['groups'] : array();
    $this->template_folder = "{$_SERVER['DOCUMENT_ROOT']}/content/landings/";
  }

  // Save content
  public function save() {
  	parent::save();
    $this->set_group();
  }

  // Set content type
  public function set_group(){
  	$GLOBALS['DB']->exec("DELETE FROM content_group WHERE c_id = :c_id");

    $query = "INSERT INTO content_group ( c_id, g_id ) VALUES ( :c_id, :g_id )";
    $stmt = $GLOBALS['DB']->prepare( $query );
    foreach ( $this->groups as $g_id  ) {
      $stmt->execute( array( ":g_id" => $g_id, ":c_id" => $this->id ) );
    }
  }

  // Get all landings
  public static function get_all() {
  	$stmt = $GLOBALS['DB']->query( "SELECT * FROM content WHERE type = 'landing' ORDER BY created DESC" );
  	return $stmt->fetchAll( PDO::FETCH_ASSOC );
  }

  // Get landings by group
  public static function get_by_group ( $id ) {
  	$query = "SELECT c.* FROM content c INNER JOIN content_group gc ON c.c_id = gc.c_id AND gc.g_id = :id";
  	$stmt = $GLOBALS['DB']->prepare($query);
  	$stmt->execute( array( 'id' => $id) );
  	return $stmt->fetchAll( PDO::FETCH_ASSOC );
  }

  public static function delete( $id ){
    parent::remove_folder( $id );
    $query = "DELETE FROM content WHERE c_id = :id;
              DELETE FROM content_group WHERE c_id = :id;
              DELETE FROM landing_blog WHERE c_id = :id;
              DELETE FROM offer_content WHERE landing_id = :id";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute( array( 'id' => $id) );
  }

  // Get blog link by id
  public static function get_link ( $id ) {
    return self::LANDINGS_URL . parent::get_link ( $id );
  }

}

?>