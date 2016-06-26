<?php

class Converter {

	public static $currencies = ["UAH", "RUB", "USD", "BYR", "KZT"];
	const STATUS_MODERATION = 1;
	const STATUS_APPROVED = 2;
	const STATUS_CANCELED = 3;

	protected function __construct(){

	}

	private static function getStatus($status, $start, $end){
		$s = [
			"is_active" => FALSE
		];

		$time = time();

		if ($status == self::STATUS_MODERATION) {
			$s['status_alias'] = "moderation";
		}

		if ($status == self::STATUS_APPROVED ) {
			if ($time < $start) {
				$s['status_alias'] = "waiting";
			} else if ($time > $start && $time < $end) {
				$s['status_alias'] = "active";
				$s['is_active'] = true;
			} else if ($time > $end) {
				$s['status_alias'] = "archive";
			}
		}

		if ($status == self::STATUS_CANCELED) {
			$s['status_alias'] = "canceled";
		}
		return $s;
	}

	public static function sortByStatus($items){
		$r = [];
		$order = ['active', 'moderation', 'waiting', 'canceled', 'archive'];

		foreach ($order as $o) {
			foreach ($items as $item) {
				if ($item['status_alias'] == $o) {
					$r[] = $item;
				}
			}
		}

		return $r;
	}

	public static function getSpecial($filters = array()){
		$values = [];
		$where = [];
		$time = time();

		$query = "SELECT * FROM special_exchange_rates";

		if (isset($filters['from'])) {
			$where[] = "`from` = :from";
		}

		if (isset($filters['to'])) {
			$where[] = "`to` = :to";
		}

		if (isset($filters['status'])) {
			switch ($filters['status']) {
				case 'moderation' : $where[] = "status = " . self::STATUS_MODERATION;
				                    break;
				case 'active' : $where[] = "status = " . self::STATUS_APPROVED;
								$where[] = "start < " . time();
								$where[] =  time() . " < end";
								break;
				case 'waiting' : $where[] = "status = " . self::STATUS_APPROVED;
								 $where[] = time() . " < start";
								 break;
				case 'archive' : $where[] = "status = " . self::STATUS_APPROVED;
								 $where[] = time() . " > end";
								 break;
				case 'canceled' : $where[] = "status = " . self::STATUS_CANCELED;
				                  break;
			}
		}

		if (!empty($where)) {
			$query .= " WHERE " . implode(" AND ", $where);
		}

		$query .= " ORDER BY start DESC, created DESC";
		$stmt = $GLOBALS['DB']->prepare($query);
		if (isset($filters['from'])) {
			$stmt->bindParam(":from", $filters['from'], PDO::PARAM_STR);
		}

		if (isset($filters['to'])) {
			$stmt->bindParam(":to", $filters['to'], PDO::PARAM_STR);
		}

		$stmt->execute();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$s = self::getStatus($row['status'], $row['start'], $row['end']);
			$row['status_alias'] = $s['status_alias'];
			$row['is_active'] = $s['is_active'];
			$values[] = $row;
		}

		return self::sortByStatus($values);
	}

	public static function getCurrencies($except = array()) {
		$temp = self::$currencies;
		if (!empty($except)) {
			foreach ($except as $e){
				foreach ($temp as $k=>$t) {
					if ($t == $e) {
						unset($temp[$k]);
					}
				}
			}
		}
		return $temp;
	}

	/**
	 * Проверят правильность параметров при добавлении нового значения валюты
	 *
	 * @param array $data Параметры значения валюты
	 * @return boolean | array Возвращает true если параметры корректы, список ошибок - в другом случае
	 */
	private function check($data){
		$errors = [];

		if (!User::exists($data['user_id'])) {
			$errors[] = "Пользователь с ID `".$data['user_id']."` не найден";
		}

		if (is_null($data['user_id'])) {
			$errors[] = "user_id должен быть определен";
		}

		if (empty($data['from'])) {
			$errors[] = "Валюта `from` не может быть пустой";
		} else {
			if (!in_array($data['from'], self::$currencies)) {
				$errors[] = "Валюта '" . $data['from'] . "' не найдена";
			}
		}

		if (empty($data['to'])) {
			$errors[] = "Валюта `to` не может быть пустой";
		} else {
			if (!in_array($data['to'], self::$currencies)) {
				$errors[] = "Валюта '" . $data['to'] . "' не найдена";
			}
		}

		if ($data['from'] === $data['to']) {
			$errors[] = "Валюты должны быть разными";
		}

		if (empty($data['bid']) || $data['bid'] < 0) {
			$errors[] = "Значение `Bid` должно быть больше 0";
		}

		if (empty($data['ask']) || $data['ask'] < 0) {
			$errors[] = "Значение `Ask` должно быть больше 0";
		}

		if (empty($errors)) {
			return true;
		}

		return $errors;
	}

	/**
	 * Если параметры являются корректными, добавляет новое значение валюты
	 *
	 * @param array $data Параметры значения валюты
	 * @return boolean | array Возвращает true если значение было добавлено, список ошибок - в другом случае
	 */
	public function addSpecialValue($data) {
		$valid = self::check($data);
		if ($valid === true) {
			$query = "INSERT INTO special_exchange_rates(`from`, `to`, bid, ask, start, end, user_id, created, status)
					VALUES (:from, :to, :bid, :ask, :start, :end, :user_id, ".time().", " . self::STATUS_MODERATION . ")";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":from", $data['from'], PDO::PARAM_INT);
			$stmt->bindParam(":to", $data['to'], PDO::PARAM_INT);
			$stmt->bindParam(":bid", $data['bid'], PDO::PARAM_INT);
			$stmt->bindParam(":ask", $data['ask'], PDO::PARAM_INT);
			$stmt->bindParam(":start", $data['start'], PDO::PARAM_INT);
			$stmt->bindParam(":end", $data['end'], PDO::PARAM_INT);
			$stmt->bindParam(":user_id", $data['user_id'], PDO::PARAM_INT);
			return $stmt->execute();
		}
		return $valid;
	}

	public static function getRate($from, $to) {
	    $stmt = $GLOBALS['DB']->prepare("SELECT ask, bid FROM exchange_rate WHERE `from` = :from AND `to` = :to");
	    $stmt->bindParam(":from", $from, PDO::PARAM_STR);
	    $stmt->bindParam(":to", $to, PDO::PARAM_STR);
	    $stmt->execute();
	    if ($stmt->rowCount()){
	        return $stmt->fetch(PDO::FETCH_ASSOC);
	    }
	    return false;
	}

	public static function getConvert($from, $to, $amount, $default = "RUB"){
		if ($from == $to) {
			return [
				"rate" => 1,
				"pair" => "RUBRUB",
				"amount" => $amount
			];
		}

		$k = self::getActualRate($from, $to, $default);

		if ($k['ask']) {
			$a = $amount / $k['rate'];
		} else {
			$a = $amount * $k['rate'];
		}

		return [
			"rate" => $k['rate'],
			"pair" => $k['pair'],
			"amount" => $a
		];
	}


	public static function getPair($from, $to){
		$pair =  $from . $to;

		switch ($pair) {
			case "BYRKZT": return ["from" => "KZT", "to" => "BYR"];
			case "BYRRUB": return ["from" => "RUB", "to" => "BYR"];
			case "BYRUAH": return ["from" => "UAH", "to" => "BYR"];
			case "RUBUAH": return ["from" => "UAH", "to" => "RUB"];
			case "KZTUAH": return ["from" => "UAH", "to" => "KZT"];
			case "KZTRUB": return ["from" => "RUB", "to" => "KZT"];
		}

		return ["from" => $from, "to" => $to];
	}

	public static function getActualRate($from, $to, $default = "UAH") {
		$f = $from;
		if ($to != $default) {
			if ($from == $default) {
				$from = $to;
				$to = $default;
			} else {
				// Выбор валютной пары по приоритетам
				$pair = self::getPair($from, $to);
				if ($pair['from'] != $from) {
					$from = $pair['from'];
					$to = $pair['to'];
				}
			}
		}

		$ask = $f != $from;

		$stmt = $GLOBALS['DB']->query("SELECT `from`, `to`, bid, ask FROM exchange_rate WHERE `from` = 'USD'");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rates[$row['from'] . $row['to']] = $row;
		}

		$stmt = $GLOBALS['DB']->query("SELECT `from`, `to`, bid, ask FROM special_exchange_rates WHERE `from` = 'USD' AND status = " . self::STATUS_APPROVED . " AND start < " . time() . " AND " . time() . " < end");

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rates[$row['from'] . $row['to']] = $row;
		}

		if ($ask) {
			$k = $rates["USD".$to]['ask']/$rates["USD".$from]['bid'];
		} else {
			$k = $rates["USD".$to]['bid']/$rates["USD".$from]['ask'];
		}

		return [
			"ask" => $ask,
			"rate" => $k,
			"pair" => $from . "/" . $to
		];
	}

	public static function testCollision($ids = array()){
		$values = []; $r = [];

		$query = "SELECT id, `from`, `to`, start, end FROM special_exchange_rates WHERE id IN (" . implode(",", $ids) . ")";
		$stmt = $GLOBALS['DB']->query($query);
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$key = $row['from'] . $row['to'];
			if (!empty($values[$key])) {
				// Сравнить даты, если пересекаются, добавить флаг collission = true
				foreach ($values[$key] as $item) {
					//Даты не пересекаются если BeginDate2 > EndDate1 или EndDate2 < BeginDate1
					$collision = !($item['start'] > $row['end'] && $item['end'] < $row['start']);
					if ($collision) {
						$r[] = $item["id"];
						$r[] = $row["id"];
					}
				}
			}

			$values[$key][] = [
				"id" => $row['id'],
				"start" => $row['start'],
				"end" => $row['end']
			];
		}

		if (count($r)) {
			return array_unique($r);
		}

		return true;
	}

	public static function updateStatus($id, $status) {
		if ($status == self::STATUS_APPROVED) {
			$stmt = $GLOBALS['DB']->prepare("SELECT `from`, `to` FROM special_exchange_rates WHERE id = ?");
			$stmt->execute([$id]);
			$pair = $stmt->fetch(PDO::FETCH_ASSOC);

			$stmt = $GLOBALS['DB']->prepare("SELECT id FROM special_exchange_rates WHERE `from` = :from AND `to` = :to AND status = " . self::STATUS_APPROVED);
			$stmt->bindParam(":from", $pair['from'], PDO::PARAM_STR);
			$stmt->bindParam(":to", $pair['to'], PDO::PARAM_STR);
			$stmt->execute();
			while ($t = $stmt->fetchColumn()) {
				$temp[] = $t;
			}
			if (!empty($temp)) {
				$temp[] = $id;
				$ids = self::testCollision($temp);
				if ($ids !== true) {
					foreach ($ids as $i) {
						if ($i != $id) {
							self::updateStatus($i, self::STATUS_CANCELED);
						}
					}
				}
			}
		}

		if ($status == self::STATUS_APPROVED || $status == self::STATUS_CANCELED) {
			$stmt = $GLOBALS['DB']->prepare("UPDATE special_exchange_rates SET status = ? WHERE id = ?");
			$stmt->execute([
				$status,
				$id
			]);

			// Сохранение изменения статуса курса
			$time = time();
			$user_id = User::get_current_user_id();
			$query = "INSERT INTO exchange_rates_logs(sid, status, user_id, changed) VALUES (?, ?, ?, " . $time . ")";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->execute([
				$id,
				$status,
				$user_id
			]);
		}

		return false;
	}

	public static function getWidget($to = "USD", $default = false, $additional = false){
	    $rates = [];

	    $precision = ($to == "USD") ? 2 : 4;

	    if ($default) {
	    	$stmt = $GLOBALS['DB']->query("SELECT `from`, `to`, bid, ask FROM exchange_rate WHERE `from` = 'USD'");
	    	$rates = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    } else {
	    	$rates = self::getRates($to);
	    	if ($additional) {
	    		$rates = array_merge($rates, self::getAdditionalRates());
	    	}
	    }

	    if (empty($rates)) {
	    	return false;
	    }

	    $table = '<table class="table table-hover table-exchange"><tbody>';
	    foreach ($rates as $v) {
	    	$s = ($v['special'] == true) ? "special" : "";
	        $table .= '<tr>
	        			<td class="name">'.$v['from'].'/'.$v['to'].':</td>
	        			<td class="bid '.$s.'">'.round($v['bid'], $precision).'</td>
	        			<td class="ask '.$s.'">'.round($v['ask'], $precision).'</td>
	        		   </tr>';
	    }
	    return $table .= '</tbody></table>';
	}

	public static function getAdditionalRates(){
		$rates = []; $r = [];

		$d = [
			["from" => "KZT", "to" => "BYR"],
			["from" => "RUB", "to" => "BYR"],
			["from" => "UAH", "to" => "BYR"],
			["from" => "UAH", "to" => "KZT"],
			["from" => "RUB", "to" => "KZT"]
		];

		// Значения по умолчанию (курс Yahoo)
		$stmt = $GLOBALS['DB']->query("SELECT `from`, `to`, bid, ask FROM exchange_rate WHERE `from` = 'USD'");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rates[$row['from'] . $row['to']] = $row;
		}

		$stmt = $GLOBALS['DB']->query("SELECT `from`, `to`, bid, ask FROM special_exchange_rates WHERE `from` = 'USD' AND status = " . self::STATUS_APPROVED . " AND start < " . time() . " AND " . time() . " < end");

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rates[$row['from'] . $row['to']] = $row;
		}

		foreach ($d as $i) {
			$r[$i['from'].$i['to']] = [
				'bid' => $rates["USD".$i['to']]['bid']/$rates["USD".$i['from']]['ask'],
				'ask' => $rates["USD".$i['to']]['ask']/$rates["USD".$i['from']]['bid'],
				'from' => $i['from'],
				'to' => $i['to'],
			];
		}

		return $r;
	}

	public static function getRates($to = "USD"){
		$rates = []; $c = [];

		// Значения по умолчанию (курс Yahoo)
		$stmt = $GLOBALS['DB']->query("SELECT `from`, `to`, bid, ask FROM exchange_rate WHERE `from` = 'USD'");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rates[$row['from'] . $row['to']] = $row;
		}

		$stmt = $GLOBALS['DB']->query("SELECT `from`, `to`, bid, ask FROM special_exchange_rates WHERE `from` = 'USD' AND status = " . self::STATUS_APPROVED . " AND start < " . time() . " AND " . time() . " < end");

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rates[$row['from'] . $row['to']] = $row;
			if ($to == "USD") {
				$rates[$row['from'] . $row['to']]['special'] = true;
			}
		}

		if ($to == "USD") {
			return $rates;
		}

		$r = [];

		foreach (self::$currencies as $a) {
			if ($a != $to) {
				if ($a == "USD") {
					$r[$a.$to]['bid'] = $rates["USD".$to]['bid'];
					$r[$a.$to]['ask'] = $rates["USD".$to]['ask'];
				} else {
					$r[$a.$to]['bid'] = $rates["USD".$to]['bid']/$rates["USD".$a]['ask'];
					$r[$a.$to]['ask'] = $rates["USD".$to]['ask']/$rates["USD".$a]['bid'];
				}

				$r[$a.$to]['from'] = $a;
				$r[$a.$to]['to'] = $to;
			}
		}

		return $r;
	}
}

?>