<?php

class User {
	public $id;
	public $login;
	public $password;
	public $email;
	public $created;
	private $role_name;
	private $country_code;
	private $country_name;

	const STATUS_NEW = 0;
	const STATUS_MODERATION = 1;
	const STATUS_ACTIVE = 2;
	const STATUS_BLOCKED = 3;

	public function __construct( $data = array()) {
		$this->id = (isset($data['id'])) ? $data['id'] : "";
		$this->login = (isset($data['login'])) ? $data['login'] : "";
		$this->email = (isset($data['email'])) ? $data['email'] : "";
		$this->password = (isset($data['password'])) ? $data['password'] : "";
		$this->country_code = (isset($data['country_code'])) ? $data['country_code'] : "";
		$this->country_name = (isset($data['country_name'])) ? $data['country_name'] : "";
		$this->created = (isset($data['created'])) ? $data['created'] : time();
	}

	public function getCountryCode(){
		return $this->country_code;
	}

	public function getCountryName(){
		return $this->country_name;
	}

	public static function getInstance($id) {
		$stmt = $GLOBALS['DB']->prepare("SELECT user_id as id, users.* FROM users WHERE user_id = :id");
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return new self($data);
	}

	public function getLogin(){
		return $this->login;
	}

    public function getId(){
        return $this->id;
    }

	public function getRoleName(){
		if (empty($this->role_name)) {
			$query = "SELECT r.role_name FROM roles as r INNER JOIN user_role as ur WHERE r.role_id = ur.role_id AND ur.user_id = :id";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
			$stmt->execute();
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->role_name = $data["role_name"];
		}
		return $this->role_name;
	}

	/**
	* Возвращает отфильтрованный список пользователей
	*
	* @var array $filters Массив фильтров
	* @return boolean | array Возвращает массив, если записи найдены, и false - если записей в таблице нет
	*/
	public static function get_all( $filters = array() ) {
		$stmt = $GLOBALS['DB']->query("SELECT * FROM users ORDER BY created DESC");
		return $stmt->fetchAll( PDO::FETCH_ASSOC );
	}

	public static function get_online() {
		$stmt = $GLOBALS['DB']->query("SELECT user_id FROM users_online");
		$rows = $stmt->fetchAll( PDO::FETCH_ASSOC );
		return array_column( $rows, "user_id" );
	}

	public static function upd_status( $user_id, $status ) {
		$query = "UPDATE users SET status = :status WHERE user_id = :user_id;
		          UPDATE partners SET status = :status WHERE id = :user_id";
	    $stmt = $GLOBALS['DB']->prepare( $query );
	    return $stmt->execute( array( ':status' => $status, ':user_id' => $user_id ) );
	}

	public static function changePassword($user_id, $pass){
		$new_pass = crypt($pass, blowfishSalt());
		$stmt = $GLOBALS['DB']->prepare("UPDATE users SET password = :new_pass WHERE user_id = :user_id");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":new_pass", $new_pass, PDO::PARAM_STR);
		return $stmt->execute();
	}

	public function save(){
		$email_check = self::get_by_email($this->email);
		if ($email_check["user_id"] === $this->id) return false;
		if ($this->password) {
			$this->password =  crypt($this->password, blowfishSalt());
		}

		if ( !$this->id ) {
			return $this->add();
		} else {
			$query = "UPDATE users SET email = :email";
			if ( $this->password ) {
				$query .= ", password = :password";
			}
			$query .= " WHERE user_id = :user_id";
	    	$stmt = $GLOBALS['DB']->prepare( $query );
	    	if ( $this->password ) {
	    		$stmt->bindParam(':password', $this->password, PDO::PARAM_STR );
	    	}
	    	$stmt->bindParam(':email', $this->email, PDO::PARAM_STR );
	    	$stmt->bindParam(':user_id', $this->id, PDO::PARAM_INT);
	    	return $stmt->execute();
		}
	}

	public function add(){
		$query = "INSERT INTO users (login, email, password, created, country_code, country_name) 
				  VALUES ( :login, :email, :password, :created, :country_code, :country_name )";
        $stmt = $GLOBALS['DB']->prepare( $query );
    	$stmt->bindParam(':login', $this->login, PDO::PARAM_STR );
    	$stmt->bindParam(':email', $this->email, PDO::PARAM_STR );
    	$stmt->bindParam(':country_code', $this->country_code, PDO::PARAM_STR );
    	$stmt->bindParam(':country_name', $this->country_name, PDO::PARAM_STR );
    	$stmt->bindParam(':password', $this->password, PDO::PARAM_STR );
    	$stmt->bindParam(':created', $this->created, PDO::PARAM_INT);
    	$r = $stmt->execute();
    	$this->id = $GLOBALS['DB']->lastInsertId();
    	return $r;
	}

	public static function set_role( $user_id, $role_id ) {
		if (self::has_role($user_id, $role_id)) return true;

		$query = "INSERT INTO user_role (user_id, role_id) VALUES (:user_id, :role_id )";
    	$stmt = $GLOBALS['DB']->prepare( $query );
    	return $stmt->execute(array(':role_id' => $role_id,':user_id' => $user_id));
	}

	public static function is_partner( $user_id ) {
		return self::has_role( $user_id, 2 ) || self::has_role( $user_id, 3 );
	}

	public static function has_role( $user_id, $role_id ){
		$query = "SELECT user_id FROM user_role WHERE user_id = :user_id AND role_id = :role_id";
    	$stmt = $GLOBALS['DB']->prepare( $query );
    	$stmt->execute( array( ':role_id' => $role_id, ':user_id' => $user_id ) );
    	if ( $stmt->rowCount() > 0 ) return true;
    	return false;
	}

	// Возвращает true если текущий пользователь является администратором, иначе - false
	public static function isAdmin($user_id = 0){

	    if ($user_id == 0){
	      $user_id = self::get_current_user_id();
	    }

	    if ($user_id > 0) {
	    	return self::has_role( $user_id, 20 );
	    }

	    return false;
	}

	public static function isSupport($user_id = 0){
	    if ($user_id == 0){
	    	$user_id = self::get_current_user_id();
	    }

	    if ($user_id > 0) {
	    	return self::has_role( $user_id, 21 );
	    }

	    return false;
	}

	public static function isPartner($user_id = 0){

	    if ($user_id == 0){
	      $user_id = self::get_current_user_id();
	    }

	    if ($user_id > 0) {
	    	$query = "SELECT role_id FROM user_role WHERE user_id = ? AND role_id IN (2, 3)";
	    	$stmt = $GLOBALS['DB']->prepare($query);
	    	$stmt->execute([
	    		$user_id
	    	]);

	    	return $stmt->rowCount() > 0;
	    }

	    return false;
	}

	public static function isBoss($id = null){
		if (!is_null($id)) {
			return $id == 69;
		}

	    if(isset($_SESSION['user']['user_id'])){
	      return ( $_SESSION['user']['user_id'] == 69 );
	    }

	    return false;
	}

	// Авторизация пользователя
	public static function check( $email, $password ) {
		$query = "SELECT u.*, r.*
		          FROM users as u, user_role as ur, roles as r
			      WHERE u.user_id = ur.user_id AND ur.role_id = r.role_id AND u.email = :email";

		$stmt = $GLOBALS['DB']->prepare( $query );
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		$stmt->execute();
		$row = $stmt->fetch( PDO::FETCH_ASSOC );

		$hash = $row['password'];

		if ( $hash == crypt($password, $hash) ) {
			return $row;
		}

		return false;
	}

	// Возвращает ID текущего пользователя
	public static function get_current_user_id() {
		if (isset($_SESSION['user']['user_id'])) {
			return $_SESSION['user']['user_id'];	
		}

		return -1;
	}

	// Возвращает роль текущего пользователя
	public static function get_current_user_role() {
		return $_SESSION['user']['role_name'];
	}

	// Возвращает данные пользователя по ID
    public static function get_by_id( $user_id ){
    	$query = "SELECT u.* FROM users as u LEFT JOIN partners as p ON p.id = u.user_id WHERE u.user_id = ?";
    	$stmt = $GLOBALS['DB']->prepare( $query );
    	$stmt->execute( array($user_id) );
    	return $stmt->fetch( PDO::FETCH_ASSOC );
    }

    // Возвращает данные пользователя по login
    public static function get_by_login( $login ){
    	$query = "SELECT * FROM users WHERE login = :login";
    	$stmt = $GLOBALS['DB']->prepare( $query );
    	$stmt->bindParam(':login', $login, PDO::PARAM_STR );
    	$stmt->execute();
    	return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    // Возвращает данные пользователя по email
    public static function get_by_email( $email ){
    	$query = "SELECT * FROM users WHERE email = :email";
    	$stmt = $GLOBALS['DB']->prepare( $query );
    	$stmt->bindParam(':email', $email, PDO::PARAM_STR );
    	$stmt->execute();
    	return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    public static function get_by_role_name( $role_name ) {
    	$query = "SELECT u.*, u.user_id as id FROM users as u, roles as r, user_role as ur WHERE u.user_id = ur.user_id AND r.role_id = ur.role_id AND r.role_name = :role_name";
    	$stmt = $GLOBALS['DB']->prepare( $query );
    	$stmt->bindParam(':role_name', $role_name, PDO::PARAM_STR );
    	$stmt->execute();
    	return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    public static function set_password( $user_id, $password ) {
    	$password =  crypt( $password, blowfishSalt() );
    	$query = "UPDATE users SET password = :password WHERE user_id = :user_id";
    	$stmt = $GLOBALS['DB']->prepare( $query );
    	$stmt->bindParam(':password', $password, PDO::PARAM_STR );
    	$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    	return $stmt->execute();
    }

    public static function delete( $user_id ) {
    	$query = "DELETE FROM users WHERE user_id = :user_id;
    	          DELETE FROM user_role WHERE user_id = :user_id;";
    	$stmt = $GLOBALS['DB']->prepare( $query );
    	$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    	return $stmt->execute();
    }

    public static function get_by_field( $field, $value, $user_id = 0){
      $query = "SELECT * FROM users WHERE {$field} = :value";
      if ( $user_id > 0 ) {
        $query .= " AND user_id != :user_id";
      }

      $stmt = $GLOBALS['DB']->prepare( $query );
      $paramType = ( is_int($value) ) ? PDO::PARAM_INT : PDO::PARAM_STR;
      $stmt->bindParam(':value', $value, $paramType );
      if ( $user_id > 0 ) {
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT );
      }
      $stmt->execute();
      return $stmt->fetch( PDO::FETCH_ASSOC );
    }

    // Проверяет существует ли пользователь с ID $user_id
    public static function exists($user_id){
        $query = "SELECT u.* FROM users as u LEFT JOIN partners as p ON p.id = u.user_id WHERE u.user_id = ?";
        $stmt = $GLOBALS['DB']->prepare($query);
        $stmt->execute([$user_id]);
        return ($stmt->rowCount() > 0);
    }


}