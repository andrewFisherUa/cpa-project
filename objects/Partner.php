<?php

/**
* Класс Partner
*
* Наследует класс User, хранит данные о партнерах ( вебмастерах и рекламодателях )
*
* @author Sorochan Elena
* @version 1.0
*/

class Partner extends User {
  public $sub;
  public $dns;
  public $name;
  public $last_name;
  public $phone;
  public $skype;
  public $activation;
  public $cards;

  function __construct( $data ) {
     parent::__construct($data);
     $this->sub = (isset($data['sub'])) ? $data['sub'] : 0;
     $this->name = (isset($data['name'])) ? $data['name'] : "";
     $this->dns = (isset($data['dns'])) ? $data['dns'] : "";
     $this->phone = (isset($data['phone'])) ? $data['phone'] : "";
     $this->skype = (isset($data['skype'])) ? $data['skype'] : "";
  }


  public static function getShops($user_id=null){
    if (is_null($user_id)) {
      $stmt = $GLOBALS['DB']->query("SELECT *, shop_id AS id FROM shops ORDER BY created DESC");
    } else {
      $stmt = $GLOBALS['DB']->prepare("SELECT *, shop_id AS id FROM shops WHERE user_id = :user_id ORDER BY created DESC");
      $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
      $stmt->execute();
    }

    $shops = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $shops[] = new Shop($row);
    }
    return $shops;
  }

  public static function getShop($user_id) {
    $stmt = $GLOBALS['DB']->prepare("SELECT *, shop_id AS id FROM shops WHERE user_id = :user_id ORDER BY created DESC");
    $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
      return null;
    }
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return new Shop($row);
  }

  public static function hasShop($user_id) {
    $shops = self::getShops($user_id);
    return !empty($shops);
  }

  /**
  * Возвращает список всех пользователей
  *
  * @return array Список пользователей
  **/
  public static function get_all( $filters = array() ){
    $query = "SELECT u.*, p.*
          FROM users as u INNER JOIN partners as p ON p.id = u.user_id ";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll( PDO::FETCH_ASSOC );
  }


  /**
  * Возвращает данные партнера по его ID
  *
  * @var integer $user_id ID партнера
  * @return array Список пользователей
  **/
  public static function getInstance( $user_id ){
    $query = "SELECT u.*, p.*
          FROM users as u INNER JOIN partners as p ON p.id = u.user_id WHERE u.user_id = ?";
    $stmt = $GLOBALS['DB']->prepare( $query );
    $stmt->execute([
      ":user_id" => $user_id
    ]);
    
    $data = $stmt->fetch( PDO::FETCH_ASSOC );
    return new self($data);
  }

  /**
  * Сохраняет данные партнера
  *
  **/
  public function save() {
    $is_new = $this->id == 0;

    parent::save();

    if ( $is_new ) {
      $query = "INSERT INTO partners ( id, sub, name, domen, phone, activation, skype)
                VALUES (:id, :sub, :name, :dns, :phone, :activation, :skype)";
    } else {
      $query = "UPDATE partners SET sub = :sub, name = :name, domen = :dns, phone = :phone, skype = :skype WHERE id = :id";
    }

    $stmt = $GLOBALS['DB']->prepare( $query );
    $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
    $stmt->bindParam(':dns', $this->dns, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $this->phone, PDO::PARAM_STR);
    $stmt->bindParam(':skype', $this->skype, PDO::PARAM_STR);
    $stmt->bindParam(':sub', $this->sub, PDO::PARAM_INT);
    if ( $is_new ) {
        $stmt->bindParam(':activation', $this->activation, PDO::PARAM_STR);
    }

    $stmt->execute();

    if ($this->id == 0) {
      for($i=1; $i<=6; $i++){
        if($i == 2 || $i == 3 || $i == 4 || $i == 6){
          $val = 1;
        } else {
          $val = 0;
        }
        $query = "INSERT INTO user_option (user_id, uoption, value) VALUES (:uid, :uoption, :val)";
        $stmt = $GLOBALS['DB']->prepare( $query );
        $stmt->bindParam(':uid', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':uoption', $i, PDO::PARAM_INT);
        $stmt->bindParam(':val', $val, PDO::PARAM_INT);
        $stmt->execute();
      }
    }
  }

  /**
  * Проверяет подключил ли пользователь оффер
  *
  * @var integer $u_id ID пользователя
  * @var integer $g_id ID оффера
  *
  * @return boolean Возвращает true если оффер подключен, и false - если не подключен
  **/
  public static function has_good( $u_id, $g_id ) {
    $query = "SELECT u_id FROM users2goods WHERE u_id = :u_id AND g_id = :g_id ";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->bindParam(':u_id', $u_id, PDO::PARAM_INT);
    $stmt->bindParam(':g_id', $g_id, PDO::PARAM_INT);
    $stmt->execute();
    $r = $stmt->fetchAll();
    if ( count($r) ) return true;
    return false;
  }

  /**
  * Возвращает имя пользователя по его ID
  *
  * @var integer $user_id ID пользователя
  *
  * @return string Если найдено имя, возвращает строку - Имя + Фамилия, если нет - возвращает логин
  **/
  public static function getUsername($user_id) {
    $query = "SELECT p.name, p.last_name, u.login FROM users AS u INNER JOIN partners AS p ON p.id = u.user_id WHERE u.user_id = :user_id";
    $stmt = $GLOBALS['DB']->prepare( $query );
    $stmt->execute( array(":user_id" => $user_id) );
    $r = $stmt->fetch( PDO::FETCH_ASSOC );
    if ( $r['name'] != '' ) {
        return $r['last_name'] . " " . $r['name'];
    }
    return $r['login'];
  }


  /**
  * Возвращает рефералов 1-го уровня партнера
  *
  * @var integer $parentID ID партнера
  * @return array Список рефералов партнера
  **/
  static public function getSub($ids = array()){
    $result = array();
    if (!empty($ids)) {
      $stmt = $GLOBALS['DB']->query('SELECT id FROM partners WHERE sub IN (' . implode(",", $ids) . ')');
      if ($stmt->rowCount()) {
        while ($id = $stmt->fetchColumn()) {
          $result[] = $id;
        }
      }
    }
    return $result;
  }

  /**
  * Возвращает рефералов всех трех уровней для партнера
  *
  * Функция предназначена для подсчета прибыли от каждого реферала
  *
  * @var integer $parentID ID партнера
  * @return array Список рефералов
  **/
  static public function getRefLevels($parentID){
    $refs = array(); $temp = null; $enabled = array();
    $temp = Partners::getSub([$parentID]);

    if ( !is_null($temp) ) {
      $refs['1'] = $temp;
      foreach ($refs['1'] as &$ref) {
        $ref['profit'] = 0;
        $temp = Partners::getSub([$ref['id']]);
        if ( !is_null($temp) ) {
          $refs['2'] = $temp;
          foreach ($refs['2'] as &$ref2) {
            $temp = Partners::getSub([$ref2['id']]);
            $ref2['profit'] = 0;
            if (!is_null($temp)) {
              $refs['3'] = $temp;
              foreach( $refs['3'] as &$ref3 ) {
                  $ref3['profit'] = 0;
              }
            }
          }
        }
      }
    }
    return $refs;
  }

  static public function getRefCount($user_id, $level = null){
    $count = 0;
    $level1 = Partners::getSub([$user_id]);
    if (!is_null($level1)) {
      $count += count($level1);
      if ($level == 1) {
        return count($level1);
      }
      $level2 = Partners::getSub($level1);
      if (!is_null($level2)) {
        $count += count($level2);
        if ($level == 2) {
          return count($level2);
        }
        $level3 = Partners::getSub($level2);
        if (!is_null($level3)) {
          $count += count($level3);
          if ($level == 3) {
            return count($level3);
          }
        }
      }
    }
    return $count;
  }

  /**
  * Возвращает данные партнера, для которого партнера с ID $sub является рефералом
  *
  * @var integer $sub ID партнера
  * @return array Данные партнера
  **/
  static public function getParent($sub){
    $query = "SELECT id, sub FROM partners WHERE id = ?";
    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->execute([
      $sub
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  static public function getReferer($id) {
    if ($id == 0) {
      return false;
    }
    $stmt = $GLOBALS['DB']->prepare("SELECT sub FROM partners WHERE id = ?");
    $stmt->execute(array($id));
    if ($stmt->rowCount()) {
      return $stmt->fetchColumn();
    }
    return false;
  }

  static public function getByField( $field, $value, $user_id = 0){
    $query = "SELECT * FROM partners WHERE {$field} = :value";
    if ( $user_id > 0 ) {
      $query .= " AND id != :id";
    }

    $stmt = $GLOBALS['DB']->prepare( $query );
    $paramType = ( is_int($value) ) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt->bindParam(':value', $value, $paramType );
    if ( $user_id > 0 ) {
      $stmt->bindParam(':id', $user_id, PDO::PARAM_INT );
    }
    $stmt->execute();
    return $stmt->fetch( PDO::FETCH_ASSOC );
  }

}

?>