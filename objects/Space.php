<?php

class Space {

  private $id;
  private $name;
  private $desc;
  private $url;
  private $source;
  private $created;
  private $changed;
  private $user_id;
  private $user_login;
  private $type;
  private $status;
  private $source_name = "";
  private $confirmed;
  private $comment;
  private $note;

  private $meta = []; // for site

  const STATUS_PROCESSING = "processing";
  const STATUS_MODERATION = "moderation";
  const STATUS_CANCELED = "canceled";
  const STATUS_APPROVED = "approved";

  const TYPE_SITE = "site";
  const TYPE_DOORWAY = "doorway";
  const TYPE_PUBLIC = "public";
  const TYPE_CONTEXT = "context";
  const TYPE_ARBITRAGE = "arbitrage";
  const TYPE_OTHER = "other";

  private function __construct($data){
    $this->id = (isset($data['id'])) ? $data['id'] : 0;
    $this->name = (isset($data['name'])) ? $data['name'] : "";
    $this->desc = (isset($data['desc'])) ? $data['desc'] : "";
    $this->comment = (isset($data['comment'])) ? $data['comment'] : "";
    $this->url = (isset($data['url'])) ? $data['url'] : "";
    $this->source = (isset($data['source'])) ? $data['source'] : "";
    $this->user_id = (isset($data['user_id'])) ? $data['user_id'] : 0;
    $this->user_login = (isset($data['user_login'])) ? $data['user_login'] : "";
    $this->comment = (isset($data['comment'])) ? $data['comment'] : "";
    $this->note = (isset($data['note'])) ? $data['note'] : "";
    $this->type = (isset($data['type'])) ? $data['type'] : self::TYPE_OTHER;
    $this->status = (isset($data['status'])) ? $data['status'] : self::STATUS_PROCESSING;
    $this->created = (isset($data['created'])) ? $data['created'] : time();
    $this->changed = (isset($data['changed'])) ? $data['changed'] : time();
    $this->confirmed = (isset($data['confirmed'])) ? $data['confirmed'] : 0;
    $this->meta = (isset($data['meta'])) ? $data['meta'] : [];
  }

  public static function getInstance($id) {
    $stmt = $GLOBALS['DB']->prepare("SELECT * FROM spaces WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount()){
      return new self($stmt->fetch(PDO::FETCH_ASSOC));
    }

    return new self();
  }

  private function fetchMeta(){
    $this->meta = [];

    $stmt = $GLOBALS['DB']->prepare("SELECT name, value FROM spaces_meta WHERE space_id = ?");
    $stmt->execute([$this->id]);
    $items = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $this->meta[$row['name']] = $row['value'];
      $items[$row['name']][] = $row['value'];
    }

    foreach ($items as $name=>$val) {
      $this->meta[$name] = (count($val) > 1) ? $val : $val[0];
    }
  }

  public function getMeta($name){
    if (empty($meta) && $this->id > 0) {
      $this->fetchMeta();
    }

    return $this->meta[$name];
  }

  public function getComment(){
    return $this->comment;
  }

  public function getNote(){
    return $this->note;
  }

  private function typeIsValid(){
    $values = [
      self::TYPE_OTHER,
      self::TYPE_SITE,
      self::TYPE_ARBITRAGE,
      self::TYPE_CONTEXT,
      self::TYPE_DOORWAY,
      self::TYPE_PUBLIC
    ];

    return in_array($this->type, $values);
  }

  public static function getTypeList(){
    return [
      ["alias" => "Другое", "value" => self::TYPE_OTHER],
      ["alias" => "Веб-сайт", "value" => self::TYPE_SITE],
      ["alias" => "Арбитраж", "value" => self::TYPE_ARBITRAGE],
      ["alias" => "Контекстная реклама", "value" => self::TYPE_CONTEXT],
      ["alias" => "Дорвей", "value" => self::TYPE_DOORWAY],
      ["alias" => "Социальная сеть", "value" => self::TYPE_PUBLIC],
    ];
  }

  public function getTypeAlias(){
    switch ($this->type) {
      case self::TYPE_OTHER: return "Другое";
      case self::TYPE_SITE: return "Сайт";
      case self::TYPE_ARBITRAGE: return "Арбитраж";
      case self::TYPE_CONTEXT: return "Контекстная реклама";
      case self::TYPE_DOORWAY: return "Дорвей";
      case self::TYPE_PUBLIC: return "Социальная сеть";
    }
  }

  public function getStatusClassName(){
    $s = $this->getStatus();
    switch ($s) {
      case self::STATUS_PROCESSING: return "success";
      case self::STATUS_MODERATION: return "warning";
      default: return $s;
    }
  }

  public function getStatusLabel(){
    switch ($this->getStatus()){
      case self::STATUS_PROCESSING : return "Не подтверждено";
      case self::STATUS_MODERATION : return "На модерации";
      case self::STATUS_CANCELED : return "Отклонен";
      case self::STATUS_APPROVED : return "Активный";
    }
  }

  private function check(){
    $errors = [];

    if (empty($this->name)) {
      $errors[] = "Название - обязательное поле";
    }

    /*
    if (empty($this->desc)) {
      $errors[] = "Описание - обязательное поле";
    }
    */

    if (empty($this->user_id)) {
      $errors[] = "User ID - обязательное поле";
    }

    if (!$this->typeIsValid()) {
      $errors[] = "Неверный тип источника";
    }

    if (empty($errors)) {
      return true;
    }

    return $errors;
  }

  public static function add($data){
    if ($data['type'] != self::TYPE_SITE) {
      $data['status'] = self::STATUS_APPROVED;
    }

    $item = new self($data);
    $valid = $item->check();
    if ($valid === true) {
      $item->save();
      return $item->getId();
    }
    return false;
  }

  public function save(){
    if ($this->id == 0) {
      //insert
      $query = "INSERT INTO spaces(name, `desc`, comment, url, source, created, changed, user_id, user_login, type, status)
                VALUES (:name, :desc, :comment, :url, :source, :created, :changed, :user_id, :login, :type, :status)";

    } else {
      //update
      $this->changed = time();
      $query = "UPDATE spaces SET name = :name,
                                  `desc` = :desc,
                                  comment = :comment,
                                  url = :url,
                                  source = :source,
                                  changed = :changed
                WHERE id = :id";
    }

    $stmt = $GLOBALS['DB']->prepare($query);
    $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
    $stmt->bindParam(":desc", $this->desc, PDO::PARAM_STR);
    $stmt->bindParam(":comment", $this->comment, PDO::PARAM_STR);
    $stmt->bindParam(":url", $this->url, PDO::PARAM_STR);
    $stmt->bindParam(":source", $this->source, PDO::PARAM_STR);
    $stmt->bindParam(":changed", $this->changed, PDO::PARAM_INT);

    if ($this->id == 0) {
      $stmt->bindParam(":created", $this->created, PDO::PARAM_INT);
      $stmt->bindParam(":type", $this->type, PDO::PARAM_STR);
      $stmt->bindParam(":status", $this->status, PDO::PARAM_STR);
      $stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);

      // fetch user_login
      $s = $GLOBALS['DB']->prepare("SELECT login FROM users WHERE user_id = ?");
      $s->execute([$this->user_id]);
      $this->user_login = $s->fetchColumn();
      $stmt->bindParam(":login", $this->user_login, PDO::PARAM_STR);
    } else {
      $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
    }

    $r = $stmt->execute();
    if ($r) {
      if ($this->id == 0) {
        $this->id = $GLOBALS['DB']->lastInsertId();
      }

      $this->saveMeta();
    }

    return $r;
  }

  private function saveMeta(){
    if (!empty($this->meta)) {
      // delete old meta
      $stmt = $GLOBALS['DB']->prepare("DELETE FROM spaces_meta WHERE space_id = ?");
      $stmt->execute([$this->id]);

      // save new meta
      $query = "INSERT INTO spaces_meta(space_id, name, value) VALUES ";
      $values = [];
      foreach ($this->meta as $k=>$v) {
        if (is_array($v)) {
          foreach ($v as $i) {
            $values[] = "({$this->id}, '{$k}', '{$i}')";
          }
        } else {
          $values[] = "({$this->id}, '{$k}', '{$v}')";
        }
      }

      $query .= implode(",", $values);
      $GLOBALS['DB']->exec($query);
    }
  }

  public static function getAll($filters = []){
    $query = "SELECT * FROM spaces";
    $where = [];

    if (!empty($filters)) {
      foreach ($filters as $fname=>$fval) {
        if ($fname == "changed_from") {
          $where[] = "changed > :{$fname}";
        } else if ($fname == "changed_to") {
          $where[] = "changed < :{$fname}";
        } else {
          $where[] = "{$fname} = :{$fname}";
        }
      }
    }

    if (!empty($where)) {
      $query .= " WHERE " . implode(" AND ", $where);
    }

    $query .= " ORDER BY id DESC";

    $stmt = $GLOBALS['DB']->prepare($query);

    if (!empty($filters)) {
      foreach ($filters as $fname=>&$fval) {
        $type = is_int($fval) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindParam(":{$fname}", $fval, $type);
      }
    }

    $stmt->execute();
    $items = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $items[] = new self($row);
    }

    return $items;
  }

  public function getId(){
    return $this->id;
  }

  public function getName(){
    return $this->name;
  }

  public function getDescription(){
    return $this->desc;
  }

  public function getUrl(){
    return $this->url;
  }

  public function getSource(){
    return $this->source;
  }

  public function getSourceName(){
    if (!empty($this->source) && empty($this->source_name)) {
      $stmt = $GLOBALS['DB']->prepare("SELECT name FROM spaces_traffic_sources WHERE id = ?");
      $stmt->execute([$this->source]);
      $this->source_name = $stmt->fetchColumn();
    }

    return $this->source_name;
  }

  public function getType(){
    return $this->type;
  }

  public function getStatus(){
    if ($this->status == self::STATUS_CANCELED || $this->type == self::TYPE_SITE) {
      return $this->status;
    }

    return self::STATUS_APPROVED;
  }

  public function getUserId(){
    return $this->user_id;
  }

  public function getUserLogin(){
    return $this->user_login;
  }

  public function getChanged(){
    return $this->changed;
  }

  public function setName($param){
    $this->name = $param;
  }

  public function setDescription($param){
    $this->desc = $param;
  }

  public function setComment($param){
    $this->comment = $param;
  }

  public function setUrl($param){
    $this->url = $param;
  }

  public function setSource($param){
    $this->source = $param;
  }

  public function setMeta($meta = []){
    $this->meta = $meta;
  }

  public function getCreated(){
    return $this->created;
  }

  public static function remove($id){
    $stmt = $GLOBALS['DB']->prepare("DELETE FROM spaces WHERE id = ?");
    $stmt->execute([$id]);
  }

  public static function getTrafficSources($type = null){
    if (is_null($type)) {
      $stmt = $GLOBALS['DB']->query("SELECT name,id FROM spaces_traffic_sources");
    } else {
      $stmt = $GLOBALS['DB']->prepare("SELECT name,id FROM spaces_traffic_sources WHERE space_type = :t");
      $stmt->bindParam(":t", $type, PDO::PARAM_STR);
      $stmt->execute();
    }

    $items = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $items[$row['id']] = $row['name'];
    }

    return $items;
  }

  public static function setStatus($id, $status){
    $stmt = $GLOBALS['DB']->prepare("UPDATE spaces SET status = :s WHERE id = :id");
    $stmt->bindParam(":s", $status, PDO::PARAM_STR);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
  }

  public static function confirmUrl($id){
    $s = $GLOBALS['DB']->prepare("UPDATE spaces SET confirmed = " . time() . ", status = '" . self::STATUS_MODERATION . "' WHERE id = ?");
    $stmt = $GLOBALS['DB']->prepare("SELECT url FROM spaces WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount()){
      $t = $stmt->fetchColumn();
      $k = "938d589d1dbaafd575830d5aa4efe196";

      // 1 способ - проверка мета тегов +
      $tags = get_meta_tags($t);
      if ($tags["univer-mag-site-verification"] == $k) {
        return $s->execute([$id]);
      }

      //2 способ - проверка существования файла
      $url = $t . "/" . $k . ".html";
      if (file_get_contents($url) === "") {
        return $s->execute([$id]);
      }
      return false;
    }
    return false;
  }

  public static function confirmUrlTest($id){
    $s = $GLOBALS['DB']->prepare("UPDATE spaces SET confirmed = " . time() . ", status = '" . self::STATUS_MODERATION . "' WHERE id = ?");
    $stmt = $GLOBALS['DB']->prepare("SELECT url FROM spaces WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount()){
      $t = $stmt->fetchColumn();
      $k = "938d589d1dbaafd575830d5aa4efe196";

      // 1 способ - проверка мета тегов +
      $tags = get_meta_tags($t);
      if ($tags["advertstar-site-verification"] == $k) {
        return $s->execute([$id]);
      }

      //2 способ - проверка существования файла
      $url = $t . "/" . $k . ".html";
      if (file_get_contents($url) === "") {
        return $s->execute([$id]);
      }
      return false;
    }
    return false;
  }

  public static function addNote($id, $note){
    $stmt = $GLOBALS['DB']->prepare("UPDATE spaces SET note = :note WHERE id = :id");
    $stmt->bindParam(":note", $note, PDO::PARAM_STR);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

}

?>