<?php

class Hold {
	private $values = array();
	private $user_id;

	public function __construct($user_id = null){
		$this->user_id = $user_id;
		$this->fetch($this->user_id);
	}

	public function getValue($target_id, $country_code, $timestamp = false) {
		if (!isset($this->values[$target_id][$country_code])) {
			return false;
		}
		if ($timestamp) {
			return 86400 * $this->values[$target_id][$country_code];
		}
		return $this->values[$target_id][$country_code];
	}

	public function getValues(){
		return $this->values;
	}

	private function fetch(){
		$stmt = $GLOBALS['DB']->query("SELECT * FROM hold ORDER BY country_code");
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->values[$row["target_id"]][$row["country_code"]] = $row["value"];
		}

		if (!is_null($this->user_id)) {
			$query = "SELECT * FROM webmaster_hold WHERE user_id = ?";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->execute(array($this->user_id));
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$this->values[$row["target_id"]][$row["country_code"]] = $row["value"];
			}
		}
	}

	public function saveValue($data) {
		$this->values[$data['target_id']][$data['country_code']] = (int) $data['value'];
		if (!isset($data['user_id'])) {
			//defaults
			$value = (int) $data['value'];
			$query = "UPDATE hold SET value = :value WHERE target_id = :target_id AND country_code = :country_code";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":value", $value, PDO::PARAM_INT);
			$stmt->bindParam(":target_id", $data['target_id'], PDO::PARAM_INT);
			$stmt->bindParam(":country_code", $data['country_code'], PDO::PARAM_STR);
			$stmt->execute();
		} else {
			//webmaster
			$value = (int) $data['value'];
			$query = "DELETE FROM webmaster_hold WHERE user_id = :user_id, target_id = :target_id, country_code = :country_code";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":user_id", $data['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(":target_id", $data['target_id'], PDO::PARAM_INT);
			$stmt->bindParam(":country_code", $data['country_code'], PDO::PARAM_STR);
			$stmt->execute();

			$query = "INSERT INTO webmaster_hold(user_id, country_code, target_id, value) VALUES (:user_id, :country_code, :target_id, :value);";
			$stmt = $GLOBALS['DB']->prepare($query);
			$stmt->bindParam(":value", $value, PDO::PARAM_INT);
			$stmt->bindParam(":user_id", $data['user_id'], PDO::PARAM_INT);
			$stmt->bindParam(":target_id", $data['target_id'], PDO::PARAM_INT);
			$stmt->bindParam(":country_code", $data['country_code'], PDO::PARAM_STR);
			$stmt->execute();
		}
	}

	public function getTable(){
		$targets = Target::getAll();
	    $countries = Country::getAll();

	    $table = '<table class="table table-hover table-striped"><thead><tr><th class="text-center">Цель</th>';
	    foreach ($countries as $code=>$item) {
	        $table .= '<th class="text-center"><i class="flag flag-'.$code.'"></i></th>';
	    }
	    $table .= '</tr></thead><tbody>';
	    foreach ($this->values as $target_id=>$item) {
	        $table .= "<tr><td>{$targets[$target_id]['name']}</td>";
	        foreach ($countries as $c) {
	            $table .= '<td class="text-center">'.$item[$c['code']].'</td>';
	        }
	        $table .= '</tr>';
	    }
	    return $table . '</tbody></table>';
	}

}

?>