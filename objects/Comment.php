<?php

class Comment {
	protected $id;
	protected $shop_id;
	protected $good_id;
	protected $country_code;
	protected $score;
	protected $content;
	protected $reply;
	protected $name;
	protected $status;
	protected $created;
	protected $viewed;

	const STATUS_MODERATION = 1;
	const STATUS_PUBLISHED = 2;
	const STATUS_ARCHIVE = 3;
	const STATUS_REJECTED = 4;

	public function __construct($data = array()) {
		$this->id = (isset($data['id'])) ? $data['id'] : 0;
		$this->shop_id = (isset($data['shop_id'])) ? $data['shop_id'] : 0;
		$this->good_id = (isset($data['good_id'])) ? $data['good_id'] : 0;
		$this->country_code = (isset($data['country_code'])) ? $data['country_code'] : '';
		$this->score = (isset($data['score'])) ? $data['score'] : 1;
		$this->content = (isset($data['content'])) ? $data['content'] : '';
		$this->reply = (isset($data['reply'])) ? $data['reply'] : '';
		$this->name = (isset($data['name'])) ? $data['name'] : '';
		$this->viewed = (isset($data['viewed'])) ? $data['viewed'] : 0;
		$this->status = (isset($data['status'])) ? $data['status'] : self::STATUS_MODERATION;
		$this->created = (isset($data['created'])) ? $data['created'] : time();
	}

	public function getId(){
		return $this->id;
	}

	public function getShopId(){
		return $this->shop_id;
	}

	public function getShopDomen(){
		$stmt = $GLOBALS['DB']->prepare("SELECT domen FROM shops WHERE shop_id = :id");
		$stmt->bindParam(":id", $this->shop_id, PDO::PARAM_INT);
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data['domen'];
	}

	public function getGoodName(){
		$stmt = $GLOBALS['DB']->prepare("SELECT name FROM goods WHERE id = :id");
		$stmt->bindParam(":id", $this->good_id, PDO::PARAM_INT);
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data['name'];
	}

	public function getGoodId(){
		return $this->good_id;
	}

	public function getCountryCode(){
		return $this->country_code;
	}

	public function getScore(){
		return $this->score;
	}

	public function getContent(){
		return $this->content;
	}

	public function getReply(){
		return $this->reply;
	}

	public function getName(){
		return $this->name;
	}

	public function getStatus(){
		return $this->status;
	}

	public function hasReply() {
		return ($this->reply != '');
	}
	public function isViewed() {
		return $this->viewed == 1;
	}

	public function getRatingHTML(){
		$html = '<select id="comment-rating'.$this->id.'" class="star-rating" style="display:none"><option value=""></option>';
		for ($i=1; $i < 6; $i++) {
			$selected = ($this->score == $i) ? "selected" : "";
			$html .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
		}
		return $html .= "</select>";
	}

	public static function setViewed($id) {
		$stmt = $GLOBALS['DB']->prepare("UPDATE comments SET viewed = 1 WHERE comment_id = :id");
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
	}

	public function getCreated($format=false){
		if ( $format == false ) return $this->created;
		return date("d/m/Y", $this->created);
	}

	public static function getStatusList(){
		return array(
			array("label" => "На модерации", "status" => self::STATUS_MODERATION),
			array("label" => "Опубликован", "status" => self::STATUS_PUBLISHED),
			array("label" => "Архив", "status" => self::STATUS_ARCHIVE),
			array("label" => "Отклонен", "status" => self::STATUS_REJECTED));
	}

	public function setShopId($param){
		$this->shop_id = $param;
	}

	public function setGoodId($param){
		$this->good_id = $param;
	}

	public function setCountryCode($param){
		$this->country_code = $param;
	}

	public function setScore($param){
		$this->score = $param;
	}

	public function setContent($param){
		$this->content = $param;
	}

	public function setReply($param){
		$this->reply = $param;
	}

	public function setName($param){
		$this->name = $param;
	}

	public function setStatus($param){
		$this->status = $param;
	}

	public function save() {

		if ($this->id == 0) {
			$query = "INSERT INTO comments(shop_id, good_id, country_code, score, content, reply, name, status, created)
			          VALUES (:shop_id, :good_id, :country_code, :score, :content, :reply, :name, :status, :created)";
		} else {
			$query = "UPDATE comments SET score = :score,
										  content = :content,
										  reply = :reply,
										  name = :name,
										  status = :status
					  WHERE comment_id = :id";
		}

		$stmt = $GLOBALS['DB']->prepare($query);

		$stmt->bindParam(':score', $this->score, PDO::PARAM_INT);
		$stmt->bindParam(':content', $this->content, PDO::PARAM_STR);
		$stmt->bindParam(':reply', $this->reply, PDO::PARAM_STR);
		$stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
		$stmt->bindParam(':status', $this->status, PDO::PARAM_INT);
		if ($this->id == 0) {
			$stmt->bindParam(':created', $this->created, PDO::PARAM_INT);
			$stmt->bindParam(':shop_id', $this->shop_id, PDO::PARAM_INT);
			$stmt->bindParam(':good_id', $this->good_id, PDO::PARAM_INT);
			$stmt->bindParam(':country_code', $this->country_code, PDO::PARAM_STR);
		} else {
			$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
		}

		$stmt->execute();

		if ($this->id == 0) {
			$this->id = $GLOBALS['DB']->lastInsertId();
		}
	}

	public static function updStatus($id, $status) {
		$stmt = $GLOBALS['DB']->prepare("UPDATE comments SET status = :status WHERE comment_id = :id");
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->bindParam(":status", $status, PDO::PARAM_INT);
		$stmt->execute();
	}

	public static function getInstance($id){
		$stmt = $GLOBALS['DB']->prepare("SELECT *, comment_id as id FROM comments WHERE comment_id = :id");
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return new self($data);
	}

	public static function getAll() {
		$stmt = $GLOBALS['DB']->query("SELECT *, comment_id as id FROM comments ORDER BY created DESC");
		$items = array();
		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$items[] = new self($data);
		}
		return $items;
	}

	public static function getFiltered($filters = array(), $order = "DESC") {
		$query = "SELECT *, comment_id AS id FROM comments";
		$where = array();
		$cond = array();

		if (isset($filters['country_code']) && $filters['country_code'] != -1) {
			$where[] = "country_code = :country_code";
			$cond[] = array(":country_code", $filters['country_code'], PDO::PARAM_STR);
		}

		if (!empty($filters['date_from'])) {
			$where[] = "created > :date_from";
			$cond[] = array(":date_from", $filters['date_from'], PDO::PARAM_INT);
		}

		if (!empty($filters['date_to'])) {
			$where[] = "created < :date_to";
			$cond[] = array(":date_to", $filters['date_to'], PDO::PARAM_INT);
		}

		if (isset($filters['name']) && $filters['name'] != '') {
			$where[] = "name LIKE :name";
			$temp = "%".$filters['name']."%";
			$cond[] = array(":name", $temp, PDO::PARAM_STR);
		}

		if (isset($filters['content']) && $filters['content'] != '') {
			$where[] = "content LIKE :content";
			$temp = "%".$filters['content']."%";
			$cond[] = array(":content", $temp, PDO::PARAM_STR);
		}

		if (isset($filters['shop_id']) && $filters['shop_id'] != -1) {
			$where[] = "shop_id = :shop_id";
			$cond[] = array(":shop_id", $filters['shop_id'], PDO::PARAM_INT);
		}

		if (isset($filters['good_id']) && $filters['good_id'] != -1) {
			$where[] = "good_id = :good_id";
			$cond[] = array(":good_id", $filters['good_id'], PDO::PARAM_INT);
		}

		if (isset($filters['score']) && $filters['score'] != -1) {
			$where[] = "score = :score";
			$cond[] = array(":score", $filters['score'], PDO::PARAM_INT);
		}

		if (isset($filters['status']) && $filters['status'] != -1) {
			$where[] = "status = :status";
			$cond[] = array(":status", $filters['status'], PDO::PARAM_INT);
		}

		if (!empty($where)) {
			$query .= " WHERE " . implode(" AND ", $where);
		}

		$query .= " ORDER BY viewed, created {$order} ";

		$stmt = $GLOBALS['DB']->prepare($query);

		foreach ($cond as &$c) {
			$stmt->bindParam($c[0], $c[1], $c[2]);
		}

		$stmt->execute();
		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$items[] = new self($data);
		}
		return $items;
	}

	public static function delete($id) {
		$stmt = $GLOBALS['DB']->prepare("DELETE FROM comments WHERE comment_id = :id");
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
	}
}
?>