<?php

class Postback {

	private $id;
	private $url;
	private $user_id;
	private $stream_id;
	private $send_on_create;
	private $send_on_confirm;
	private $send_on_cancel;

	private function __construct($data = array()) {
		$this->id = isset($data['id']) ? $data['id'] : 0;
		$this->url = isset($data['url']) ? $data['url'] : "";
		$this->user_id = isset($data['user_id']) ? $data['user_id'] : 0;
		$this->stream_id = isset($data['stream_id']) ? $data['stream_id'] : 0;
		$this->send_on_create = isset($data['send_on_create']) ? $data['send_on_create'] : 0;
		$this->send_on_confirm = isset($data['send_on_confirm']) ? $data['send_on_confirm'] : 0;
		$this->send_on_cancel = isset($data['send_on_cancel']) ? $data['send_on_cancel'] : 0;
	}

	public function getUserId(){
		return $this->user_id;
	}

	public function getUrl(){
		return $this->url;
	}

	public function getOfferId(){
		return $this->url;
	}

	public function isGlobal(){
		return $this->stream_id == 0;
	}

	public function sendOnCreate(){
		return $this->send_on_create;
	}

	public function sendOnCancel(){
		return $this->send_on_cancel;
	}

	public function sendOnConfirm(){
		return $this->send_on_confirm;
	}

	private function linkIsValid(){
		return true;
	}

	private function isValid(){
		$errors = [];

		if ($this->user_id == 0) {
			$errors[] = "invalid user_id";
		}

		if (empty($this->url)) {
			$errors[] = "Ссылка не может быть пустой";
		}

		if (!empty($this->url)) {
			$test = $this->linkIsValid();
			if ($test !== true) {
				$errors[] = $test;
			}
		}

		if (!$this->send_on_create &&
			!$this->send_on_cancel &&
			!$this->send_on_confirm) {
			$errors[] = "Необходимо выбрать вариант отправки запроса";
		}

		if (count($errors)) {
			return $errors;
		}

		return true;
	}

	public function create($data){
		$data['id'] = 0;
		$data['stream_id'] = (isset($data['stream_id'])) ? $data['stream_id'] : 0;

		$p = new self($data);
		$check = $p->isValid();

		if ($check === true) {
			$p->save();
			return $p;
		}

		return $check;
	}

	public function save(){
		self::remove($this->user_id, $this->stream_id);

		$query = "INSERT INTO postback (user_id, url, send_on_create, send_on_confirm, send_on_cancel, stream_id)
				  VALUES (:p1, :p2, :p3, :p4, :p5, :p6)";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":p1", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":p2", $this->url, PDO::PARAM_STR);
		$stmt->bindParam(":p3", $this->send_on_create, PDO::PARAM_INT);
		$stmt->bindParam(":p4", $this->send_on_confirm, PDO::PARAM_INT);
		$stmt->bindParam(":p5", $this->send_on_cancel, PDO::PARAM_INT);
		$stmt->bindParam(":p6", $this->stream_id, PDO::PARAM_INT);
		return $stmt->execute();
	}

	public static function getInstance($user_id = 0, $stream_id = 0) {
		$stmt = $GLOBALS['DB']->prepare("SELECT * FROM postback WHERE user_id = ? AND stream_id = ?");
		$stmt->execute([
			$user_id,
			$stream_id
		]);

		if ($stmt->rowCount()) {
			return new self($stmt->fetch(PDO::FETCH_ASSOC));
		}

		return new self();
	}

	public static function hasGlobal($user_id){
		$stmt = $GLOBALS['DB']->prepare("SELECT * FROM postback WHERE user_id = ? AND stream_id = 0");
		$stmt->execute([
			$user_id
		]);

		return $stmt->rowCount() == 1;
	}

	public static function remove($user_id, $stream_id) {
		$stmt = $GLOBALS['DB']->prepare("DELETE FROM postback WHERE user_id = ? AND stream_id = ?");
		$stmt->execute([
			$user_id,
			$stream_id
		]);
	}

	public static function checkUrl($subject){
	    $accepted = [
	        "{lead1_time}",
	        "{lead2_time}",
	        "{sub1}",
	        "{sub2}",
	        "{sub3}",
	        "{order_id}",
	        "{status}",
	        "{offer_id}",
	        "{offer_name}",
	        "{link_id}",
	        "{link_name}",
	        "{webtotal}",
	        "{currency}",
	    ];

	    $pattern = '/{(.*?)}/';
	    preg_match_all($pattern, $subject, $matches);

	    $macros = $matches[0];
	    $errors = [];

	    foreach ( $macros as $m) {
	        if (!in_array($m, $accepted)) {
	            $errors[] = $m;
	        }
	    }


	    if (count($errors)) {
	        return $errors;
	    }

	    return true;
	}

	// action - create | confirm | cancel
	public static function trigger($action, $data = array()){

		$query = "SELECT url FROM postback
				  WHERE user_id = ? AND
				  		send_on_{$action} = 1 AND
				  		(stream_id = 0 OR stream_id = ?)
				  ORDER BY stream_id DESC
				  LIMIT 0, 1";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([
			$data['user_id'],
			$data['stream_id']
		]);

		if ($stmt->rowCount()) {
			// trigger link

			$link = $stmt->fetchColumn();

			$currency = [];
			$stmt0 = $GLOBALS['DB']->query("SELECT code, currency_code FROM country");
			while ($r = $stmt0->fetch(PDO::FETCH_ASSOC)) {
				$currency[$r['code']] = $r['currency_code'];
			}

			$query = "SELECT t1.id AS order_id, t1.user_id, t1.created AS lead1_time, t1.target_time AS lead2_time, t1.webmaster_commission AS webtotal, t1.country_code,
			                 t2.subid1 AS sub1, t2.subid2 AS sub2, t2.subid3 AS sub3,
			                 t3.product_name AS offer_name, t3.good_id AS offer_id,
			                 t4.f_id AS link_id, t4.name AS link_name
			          FROM orders AS t1 INNER JOIN order_subid as t2 ON t1.id = t2.order_id
			                            INNER JOIN order_goods AS t3 ON t1.id = t3.order_id
			                            INNER JOIN flows AS t4 ON t1.source_id = t4.f_id
			          WHERE t1.id = ?";

			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->execute([
				$data["order_id"]
			]);

			$data = $stmt->fetch(PDO::FETCH_ASSOC);

			$macros = [
				"{lead1_time}" => $data['lead1_time'],
		        "{lead2_time}" => $data['lead2_time'],
		        "{sub1}" => $data['sub1'],
		        "{sub2}" => $data['sub2'],
		        "{sub3}" => $data['sub3'],
		        "{order_id}" => $data['order_id'],
		        "{offer_id}" => $data['offer_id'],
		        "{offer_name}" => $data['offer_name'],
		        "{link_id}" => $data['link_id'],
		        "{link_name}" => $data['link_name'],
		        "{webtotal}" => $data['webtotal'],
		        "{currency}" => $currency[$data['country_code']],
			];

			switch ($action) {
				case "create" : $macros['{status}'] = "new"; break;
				case "confirm" : $macros['{status}'] = "approve"; break;
				case "cancel" : $macros['{status}'] = "decline"; break;
			}

			$query = "SELECT currency_code, code FROM country";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":param", $param, PDO::PARAM_INT);
			$stmt->execute();

			foreach ($macros as $macros_name => $macros_value) {
				$link = str_replace($macros_name, $macros_value, $link);
			}

			$link = str_replace(' ', '%20', $link);

			$context = stream_context_create(array(
			    'http' => array(
			        'timeout' => 1
			        )
			    )
			);

			file_get_contents($link, 0, $context);

			return $link;
		}

		return false;
	}
}

?>