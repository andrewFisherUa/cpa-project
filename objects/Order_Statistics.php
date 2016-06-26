<?php

class Order_Statistics {

	private $user_id;
	private $currency;
	private $range;
	private $orders;
	private $filters;
	private $page;
	private $page_length;
	private $order_by;
	private $count = 0;

	public function __construct($user_id, $range = []) {
		$this->user_id = $user_id;
		$this->page = 0;
		$this->page_length = 50;
		$this->count = 0;
		$this->order_by = "created";
		$this->getDefaultCurrency();
		$this->setRange($range);
		$this->filters = [];
	}

	public function setOrderBy($param){
		$valid_values = ["created", "status_problem_time"];

		if (in_array($param, $valid_values)) {
			$this->order_by = $param;
		}
	}

	private function getDefaultCurrency(){

		if ($user_id == 0) {
			$this->currency = "RUB";
		}

		$query = "SELECT t1.currency_code
				  FROM country as t1 inner join accounts as t2 on t1.code = t2.type
				  WHERE t2.user_id = ? AND t2.default = 1";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->execute([
			$this->user_id
		]);

		$this->currency = $stmt->fetchColumn();
	}

	public function addFilters($filters = []){
		$this->filters = $filters;
	}

	public function getOrdersCount(){
		return $this->count;
	}

	protected function getQuery(){

		$select = "SELECT t1.id, t1.oid, t1.created as `timestamp`, t3.good_id as offer_id, t3.product_name as offer_name, t1.status, t1.status2, t1.status2_name, t1.phone, t1.first_name, t1.last_name, t1.webmaster_commission as profit, t1.country_code, t1.target_close, t1.ip, t1.user_id, t1.status_problem_time ";

		$query = "FROM orders as t1 INNER JOIN order_goods as t3 ON t1.id = t3.order_id";

		$cond = [];

		if ($this->user_id > 0) {
	 		$cond[] = "t1.user_id = :user_id";
	 	}

		if (!empty($this->filters['landing']) ||
			!empty($this->filters['blog']) ||
			!empty($this->filters['source'])) {

			$query .= " INNER JOIN pfinder_keys as t4 ON t1.source_id = t4.stream_id ";
		}

        if (!empty($this->filters['subid'])) {

            for ($i=1;$i<6;$i++) {
                if (!empty($this->filters['subid']['subid'.$i])) {
                    $cond[] = "t5.subid{$i} IN (" . implode(",", $this->filters['subid']['subid'.$i]) . ")";
                }
            }

            $query .= " INNER JOIN order_subid as t5 ON t1.id = t5.order_id ";
        }

        // офферы
        if (!empty($this->filters['webmaster'])) {
            $cond[] = "t1.user_id IN ( " . implode(",", $this->filters['webmaster']) . ")";
        }

        // офферы
        if (!empty($this->filters['offer'])) {
            $cond[] = "t3.good_id IN ( " . implode(",", $this->filters['offer']) . ")";
        }

        // потоки
        if (!empty($this->filters['stream'])) {
            $cond[] = "t1.source_id IN (" . implode(",", $this->filters['stream']) . ")";
        }

        // лендинги
        if (!empty($this->filters['landing'])) {
            $cond[] = "t4.landing_id IN (" . implode(",", $this->filters['landing']) . ")";
        }

        // блоги
        if (!empty($this->filters['blog'])) {
            $cond[] = "t4.blog_id IN (" . implode(",", $this->filters['blog']) . ")";
        }

        // source_id
        if (!empty($this->filters['source'])) {
            $cond[] = "t4.traffic_source_id IN (" . implode(",", $this->filters['source']) . ")";
        }

        if (!empty($this->filters['status2_name'])) {
        	$cond[] = "t1.status2_name = '" . $this->filters['status2_name'] . "'";
        }

        if (
        	(is_int($this->filters['status']) && $this->filters['status'] >= 0) ||
        	(is_string($this->filters['status']) && $this->filters['status'] != '')
        	) {
        	switch ($this->filters['status']) {
        		case "processing" : $cond[] = "t1.status != " . Order::STATUS_CANCELED . " AND target_close = 0"; break;
        		case "canceled" : $cond[] = "(t1.status = " . Order::STATUS_CANCELED . " OR ( t1.status = " . Order::STATUS_RETURN." AND t1.target = 0))" ; break;
        		case "approved" : $cond[] = "t1.status != " . Order::STATUS_CANCELED . " AND target_close = 1"; break;
        		default: $cond[] = "t1.status = " . $this->filters['status']; break;
        	}
        }

        if ($this->filters['status_problem'] == TRUE) {
			$cond[] = "t1.status_problem_time > 1";
        }

        $query .= " WHERE t1." . $this->order_by . " > :from_time AND t1." . $this->order_by . " < :to_time ";

        if (!empty($cond)) {
        	$query .= " AND " . implode(" AND ", $cond);
        }

        // get orders count
        $stmt = $GLOBALS['DB']->prepare("SELECT count(*) " . $query);
		$stmt->bindParam(":from_time", $this->range['from'], PDO::PARAM_INT);
		$stmt->bindParam(":to_time", $this->range['to'], PDO::PARAM_INT);

		if ($this->user_id > 0) {
	 		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
	 	}

		$stmt->execute();
        $this->count = $stmt->fetchColumn();

        return $select . $query;
    }

	// Допустимые значения range - "today", "week", "month", диапазон дат в формате timestamp
	// По умолчанию = week
	private function setRange($range = []){

		if (empty($range)) {
			$this->range['from'] = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
		    $this->range['to'] = $this->range['from'] + 86400;
		    return $this->range;
		}

		$this->range['from'] = $range['from'];
		$this->range['to'] = $range['to'];

		return $this->range;
	}

	private function getStatus($order) {

		$s = $order['status'];

		$status = [];

		$status_list = [
			Order::STATUS_PROCESSING => [ "label" => "В обработке", "class" => "processing" ],
			Order::STATUS_CONFIRMED => [ "label" => "Подтвержден", "class" => "confirmed" ],
			Order::STATUS_DELIVERED => [ "label" => "Забран", "class" => "delivered" ],
			Order::STATUS_CANCELED => [ "label" => "Аннулирован", "class" => "canceled" ],
			Order::STATUS_RETURN => [ "label" => "Возврат", "class" => "return" ],
		];

		if ($this->user_id == 0) {

			return [
				"name" => $status_list[$s]["label"],
				"class" => $status_list[$s]["class"]
			];

		}

		if ($s == Order::STATUS_PROCESSING) {
			return ["name" => "В ожидании", "class" => "processing"];
		}

		if (($s == Order::STATUS_CONFIRMED || $s == Order::STATUS_DELIVERED) && $order['target_close'] == 0 ) {
			return ["name" => "В ожидании", "class" => "processing"];
		}

		if (($s == Order::STATUS_CONFIRMED || $s == Order::STATUS_DELIVERED) && $order['target_close'] == 1 ) {
			return ["name" => "Принято", "class" => "confirmed"];
		}

		if ($s == Order::STATUS_RETURN && $order['target'] == 1 && $order['target_close'] == 1) {
			return ["name" => "Принято", "class" => "confirmed"];
		}

		return ["name" => "Отклонено", "class" => "canceled"];
	}

	private function statusIsValid($status_name) {
		$status_list = [
			"Аннулирован",
			"Недозвон",
			"Некорректный телефон",
			"Отказ",
			"Перезвонить",
			"Повтор",
			"Нет в наличии",
			"Перезаказ",
			"Ошибочные данные",
			"Тест",
			"Некорректные данные",
			"Доставка невозможна",
			"Сервис",
			"На модерации",
			"Подтвержден",
			"В обработке",
		];

		if ($this->user_id > 0) {
			if (!in_array($status_name, $status_list)) {
				return "";
			}

			if (in_array($status_name, ['Перезаказ', 'Доставка невозможна', 'Нет в наличии'])) {
				return "";
			}
		}

		return $status_name;
	}

	private function addOrder($order){
		$temp = Converter::getConvert($order["currency"], $this->currency, $order["profit"], $this->currency);

		$order['profit'] = round($temp['amount']);
		$order['currency'] = $this->currency;

		$s = $this->getStatus($order);

		$order['status_name'] = $s['name'];
		$order['status_class'] = $s['class'];	
		$order['status2_name'] = $this->statusIsValid($order['status2_name']);

		$order['name'] = $this->replaceChars($order['last_name']) . " " . $this->replaceChars($order['first_name']);
		$order['phone'] = iconv_substr($order['phone'], 0, strlen($order['phone']) - 4, "UTF-8") . "****";
		$order['created'] = date("d.m.Y H:i:s", $order['timestamp']);
		$order['status_problem_time'] = ($order['status_problem_time'] > 0) ? date("d.m.Y H:i:s", $order['status_problem_time']) : "";

		$this->orders[] = $order;
	}

	private function replaceChars($string){
		

		$string = iconv('UTF-8','windows-1251',$string ); //Меняем кодировку на windows-1251

		$length = strlen($string);
		if ($length > 12) {
			$length = 12;
		}
		
		$string = substr($string, 0, $length - 3) . "***";
		return iconv('windows-1251','UTF-8' ,$string ); //Возвращаем кодировку в utf-8
	}

	private function fetchStat(){
		$query = $this->getQuery();

	 	$query .= " ORDER BY t1." . $this->order_by . " DESC";

	 	if ($this->page_length != -1) {
	 		$query .= " LIMIT " . ($this->page) * $this->page_length . ", " . $this->page_length;
	 	}

		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":from_time", $this->range['from'], PDO::PARAM_INT);
		$stmt->bindParam(":to_time", $this->range['to'], PDO::PARAM_INT);

		if ($this->user_id > 0) {
	 		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
	 	}

		$stmt->execute();

		$c = Country::getAll();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$row['profit'] = (int) $row['profit'];
			$row['currency'] = $c[$row['country_code']]['currency_code'];
			$this->addOrder($row);
		}
	}

	public function fetch($page = 0, $page_length = -1){
		$this->page = $page;
		$this->page_length = $page_length;

		$this->fetchStat();

		return $this->orders;
	}
}

?>