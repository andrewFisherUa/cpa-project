<?php

require_once( PATH_ROOT .'/misc/plugins/php/mail/PHPMailer/PHPMailerAutoload.php');
require_once( PATH_ROOT .'/misc/plugins/php/mail/u_mail.php');

/***

'Уведомления связанные с новыми офферами',
'Уведомления связанные с приостановкой оффера',
'Уведомления связанные с изменениями офферов',
'Уведомления связанные с новыми лендингами',
'Уведомления связанные с новостями системы',
'Уведомления связанные с важными новостями',

case 1: 'Модерация'
case 2: 'Активно'
case 3: 'Архив'

sent в таблице user_news:
0 - расслыка на email не нужна
1 - письмо отправлено
-1 - письмо не отправлено

***/

class News {

	private $id;
	private $title; // name
	private $content; // text
	private $type;
	private $created; // date
	private $status;
	private $good_id;
	private $activate_time; // время рассылки
	private $db;

	const TYPE_NEW_OFFER = 1;
	const TYPE_STOP_OFFER = 2;
	const TYPE_CHANGE_OFFER = 3;
	const TYPE_NEW_CONTENT = 4;
	const TYPE_SYSTEM = 5;
	const TYPE_IMPORTANT = 6;

	const STATUS_MODERATION = 1;
	const STATUS_ACTIVE = 2;
	const STATUS_ARCHIVE = 3;
	const STATUS_DONE = 4;

	private function __construct($db, $data){
		$this->id = isset($data['id']) ? $data['id'] : 0;
		$this->title = isset($data['title']) ? $data['title'] : "";
		$this->content = isset($data['content']) ? $data['content'] : "";
		$this->created = isset($data['created']) ? $data['created'] : time();
		$this->activate_time = isset($data['activate_time']) ? $data['activate_time'] : 0;
		$this->type = isset($data['type']) ? $data['type'] : 0;
		$this->status = isset($data['status']) ? $data['status'] : self::STATUS_MODERATION;
		$this->good_id = isset($data['good_id']) ? $data['good_id'] : 0;
		$this->db = $db;
	}

	public static function save($db, $data){
		if ($data['id'] == 0){
			$item = new self($db, $data);
		} else {
			$item = self::getById($db, $data['id']);
			$item->setStatus($data['status']);
			$item->setActivateTime($data['activate_time']);
			$item->setTitle($data['title']);
			$item->setContent($data['content']);
		}

		return $item->saveNews();
	}

	// date("d", $this->created) ." ". $this->getMonthLabel() ." ". date("Y", $this->created);
	public function getCreated($time_elapsed = false){
		if ($time_elapsed){
			return time_elapsed_string($this->created);
		}
		return $this->created;
	}

	public function setStatus($param){
		if ($this->status != self::STATUS_DONE) {
			$this->status = $param;
		}
	}

	public function setActivateTime($param){
		$this->activate_time = $param;
	}

	public function getActivateTime($time_elapsed = false){
		if ($time_elapsed){
			return time_elapsed_string($this->activate_time);
		}
		return $this->activate_time;
	}

	public function getId(){
		return $this->id;
	}

	public function setTitle($param){
		$this->title = $param;
	}

	public function setContent($param){
		$this->content = $param;
	}

	public function getTitle(){
		return $this->title;
	}

	public function getContent(){
		return $this->content;
	}

	public function getType(){
		return $this->type;
	}

	public function getStatus(){
		return $this->status;
	}

	public function getGoodId(){
		return $this->good_id;
	}

	private function saveNews(){
		if ($this->id == 0) {
			$query = "INSERT INTO news (created, title, content, type, status, good_id, activate_time)
	    		      VALUES (" . time() . ", :title, :content, :type, :status, :good_id, :activate_time)";

	    	if ($this->type == self::TYPE_SYSTEM || $this->type == self::TYPE_IMPORTANT) {
	    		$this->good_id = 0;
	    	}

		} else {
			$query = "UPDATE news SET title = :title,
									  content = :content,
									  created = " . time() . ",
									  status = :status";

			if ($this->status != self::STATUS_DONE) {
				$query .= ", activate_time = " . $this->activate_time;
			}

			$query .= " WHERE id = :id";
		}

	    $stmt = $this->db->prepare($query);

	    $stmt->bindParam(':title', $this->title, PDO::PARAM_STR);
	    $stmt->bindParam(':content', $this->content, PDO::PARAM_STR);
	    $stmt->bindParam(':status', $this->status, PDO::PARAM_INT);

	    if ($this->id == 0) {
	    	$stmt->bindParam(':type', $this->type, PDO::PARAM_INT);
	    	$stmt->bindParam(':activate_time', $this->activate_time, PDO::PARAM_INT);
	    	$stmt->bindParam(':good_id', $this->good_id, PDO::PARAM_INT);
	    } else {
	    	$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
	    }

	    $stmt->execute();

	    if ($this->id == 0) {
	    	$this->id = $this->db->lastInsertId();
	    }

	//    $this->activate();

	    return $this->id;
	}

	public function activate(){
		if ($this->status == self::STATUS_ACTIVE && $this->activate_time <= time()) {

			// Делаем рассылку
    		$ids = [];
			if ($this->good_id > 0) {
				$stmt = $this->db->query("SELECT u_id FROM users2goods WHERE g_id = " . $this->good_id);
				$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
			}

			$query = "SELECT u.user_id as id, u.email, uo.value as send_email
					  FROM users AS u INNER JOIN user_role AS r ON u.user_id = r.user_id
					                  INNER JOIN user_option AS uo ON u.user_id = uo.user_id
					  WHERE r.role_id = 2 AND u.status = 2 AND uo.uoption = {$this->type}";
		    $stmt = $this->db->query($query);

		    $values = [];
		    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		    	$sent = 0;
		    	$important = (int) in_array($row['id'], $ids);

		    	if ($row['send_email'] == 1){
		    		if ($this->send_email($row['email']) === true) {
		    			$sent = 1;
		    		} else {
		    			$sent = -1;
		    		}
		    	}

		    	$values[] = "(" . $row['id'] . ", " . $this->id . ", 0," . $important . ", " . $sent . ")";
		    }

		    if (!empty($values)) {
				$this->db->exec("INSERT INTO user_news (user_id, news_id, viewed, important, sent) VALUES " . implode(",", $values));
		    }

		    // Сохраняем статус новости как DONE
		    $this->db->exec("UPDATE news SET status = " . self::STATUS_DONE . " WHERE id = " . $this->id);
    	}
	}

	private function send_email($email){ /* Функция отправки сообщения пользователям */
	    global $smarty;

	    $smarty->assign("good_id", $this->good_id);
	    $smarty->assign("content", $this->content);
	    $smarty->assign("site_url", get_site_url());
		$message = $smarty->fetch('email_templates' . DS . 'news.tpl');

        $mail = new u_mail(true);
        return $mail->sendmail("Univer-mag.com", "no-reply@univer-mag.com", $email, $this->title, $message);
	}

	public function getGoodName(){
		$stmt = $this->db->query("SELECT name FROM goods WHERE id = " . $this->good_id);
		if ($stmt->rowCount()) {
			return $stmt->fetchColumn();
		}
	}

	public function getTypeAlias(){
		switch($this->type){
			case self::TYPE_NEW_OFFER: return 'new-offer';
			case self::TYPE_STOP_OFFER: return 'stop-offer';
			case self::TYPE_CHANGE_OFFER: return 'change-offer';
			case self::TYPE_NEW_CONTENT: return 'new-content';
			case self::TYPE_SYSTEM: return 'system';
			case self::TYPE_IMPORTANT: return 'important';
	    }
	}

	public function getIcon(){
    	$types = [
			self::TYPE_NEW_OFFER => [ "color"=> "default", "icon" => "fa fa-shopping-cart" ],
			self::TYPE_STOP_OFFER => [ "color"=> "primary", "icon" => "fa fa-pencil" ],
			self::TYPE_CHANGE_OFFER => [ "color"=> "info", "icon" => "fa fa-exclamation" ],
			self::TYPE_NEW_CONTENT => [ "color"=> "success", "icon" => "fa fa-bell" ],
			self::TYPE_SYSTEM => [ "color"=> "danger", "icon" => "glyphicon glyphicon-picture" ],
			self::TYPE_IMPORTANT => [ "color"=> "warning", "icon" => "glyphicon glyphicon-info-sign" ]];

		return  '<span class="label label-sm label-'.$types[$this->type]['color'].'"><i class="'.$types[$this->type]['icon'].'"></i></span>';
	}

	public static function getTypeList(){
	    return [
			self::TYPE_NEW_OFFER => 'Уведомления связанные с новыми офферами',
			self::TYPE_STOP_OFFER => 'Уведомления связанные с приостановкой оффера',
			self::TYPE_CHANGE_OFFER => 'Уведомления связанные с изменениями офферов',
			self::TYPE_NEW_CONTENT => 'Уведомления связанные с новыми лендингами',
			self::TYPE_SYSTEM => 'Уведомления связанные с новостями системы',
			self::TYPE_IMPORTANT => 'Уведомления связанные с важными новостями',
	    ];
    }

    public static function saveUserOptions($db, $user_id, $options = []){

    	if (empty($options)) {
    		$options = [
    		self::TYPE_NEW_OFFER => 0,
				self::TYPE_STOP_OFFER => 1,
				self::TYPE_CHANGE_OFFER => 1,
				self::TYPE_NEW_CONTENT => 1,
				self::TYPE_SYSTEM => 0,
				self::TYPE_IMPORTANT => 1,
    		];
    	}

    	$query = "DELETE FROM user_options WHERE user_id = " . $user_id;
    	$db->exec($query);

    	$values = [];

    	foreach ($options as $k=>$v) {
    		$values[] = "({$user_id}, {$k}, {$v})";
    	}

    	if (!empty($values)) {
    		$query = "INSERT INTO user_option(user_id, uoption, value) VALUES " . implode(",", $values);
    		$db->exec($query);
    	}
	}

	public static function getUserOptions($user_id){
		$query = "SELECT uoption, value FROM user_options WHERE user_id = ?";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([$user_id]);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function delete($db, $id){
		$stmt = $db->prepare("DELETE FROM news WHERE id = ?");
		$stmt->execute([$id]);
	}
	public function getTypeLabel(){
	    switch($this->type){
			case self::TYPE_NEW_OFFER: return 'Новый оффер';
			case self::TYPE_STOP_OFFER: return 'Приостановка оффера';
			case self::TYPE_CHANGE_OFFER: return 'Изменение оффера';
			case self::TYPE_NEW_CONTENT: return 'Новые лендинги';
			case self::TYPE_SYSTEM: return 'Новости системы';
			case self::TYPE_IMPORTANT: return 'Важное';
	    }
	}

  //the initialization ID type News
  public function getTypeId(){
      switch($this->type){
      case self::TYPE_NEW_OFFER: return '1';
      case self::TYPE_STOP_OFFER: return '2';
      case self::TYPE_CHANGE_OFFER: return '3';
      case self::TYPE_NEW_CONTENT: return '4';
      case self::TYPE_SYSTEM: return '5';
      case self::TYPE_IMPORTANT: return '6';
      }
  }

	public function getStatusLabel(){
		switch($this->status){
			case self::STATUS_MODERATION: return 'Модерация';
			case self::STATUS_ACTIVE: return 'Активно';
			case self::STATUS_ARCHIVE: return 'Архив';
			case self::STATUS_DONE: return 'Выполнено';
	    }
	}

	public static function getById($db, $id){
		$query = "SELECT * FROM news WHERE id = ?";
		$stmt = $db->prepare($query);
		if ($stmt->execute([$id])){
			return new self($db, $stmt->fetch(PDO::FETCH_ASSOC));
		}
	}

	public static function setViewed($db, $user_id, $news_id){
		$query = "UPDATE user_news SET viewed = 1 WHERE news_id = ? AND user_id = ?";
		$stmt = $db->prepare($query);
		$stmt->execute([
			$news_id,
			$user_id
		]);
	}

	public static function getAll($db, $params = [], $from = 0, $limit = -1){
		$query = "SELECT n.* FROM news AS n";

		if (array_key_exists('user_id', $params) ||
			array_key_exists('viewed', $params) ||
			array_key_exists('important', $params) ||
			array_key_exists('good_id', $params)){

			$query .= " INNER JOIN user_news AS un ON n.id = un.news_id ";
		}

		$v = [];

		if (array_key_exists('user_id', $params)) {
			$v[] = "un.user_id = " . $params['user_id'];
		}

		if (array_key_exists('good_id', $params)) {
			$v[] = "un.good_id = " . $params['good_id'];
		}

		if (array_key_exists('viewed', $params)) {
			$v[] = "un.viewed = " . (int) $params['viewed'];
		}

		if (array_key_exists('type', $params)) {
			$v[] = "n.type = " . $params['type'];
		}

		if (array_key_exists('status', $params)) {
			$v[] = "n.status = " . $params['status'];
		}

		if (array_key_exists('from_activate_time', $params)) {
			$v[] = "n.activate_time > " . $params['from_activate_time'];
		}

		if (array_key_exists('to_activate_time', $params)) {
			$v[] = "n.activate_time < " . $params['to_activate_time'];
		}

		if (array_key_exists('from_date', $params)) {
			$v[] = "n.created > " . $params['from_date'];
		}

		if (array_key_exists('to_date', $params)) {
			$v[] = "n.created < " . $params['to_date'];
		}

		if (array_key_exists('title', $params)) {
			$v[] = "n.title LIKE '%" . $params['title'] . "%'";
		}

		if (array_key_exists('important', $params)) {
			$v[] = "un.important = " . $params['important'];
		}

		if (array_key_exists('exclude', $params)) {
			$v[] = "n.id != " . $params['exclude'];
		}

		if (!empty($v)) {
			$query .= " WHERE " . implode(" AND ", $v);
		}

		$query .= " ORDER BY n.created DESC";

		if ($limit > 0) {
			$query .= " LIMIT " . $from . ", " . $limit;
		}

		$items = [];
		$stmt = $db->query($query);
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$items[] = new self($db, $row);
		}

		return $items;
	}

	public function getMonthLabel(){
	    $months = [
	      'января',
	      'февраля',
	      'марта',
	      'апреля',
	      'мая',
	      'июня',
	      'июля',
	      'августа',
	      'сентября',
	      'октября',
	      'ноября',
	      'декабря'
	    ];
	    return $months[date('m', $this->created)+1];
	  }
}

?>
