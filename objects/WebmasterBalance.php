<?php

class WebmasterBalance extends PartnerBalance {

	public function __construct($user_id, $country_code = null){
		parent::__construct($user_id, $country_code);
	}

	protected function calculateCurrent(){
		$this->fetchAccountBalance();
		// Текущий баланс
		$query = "SELECT SUM(webmaster_commission) FROM pass WHERE user_id = :user_id AND country_code = :country_code AND closed = 0";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->current += floor($stmt->fetchColumn());
		}
	}

	protected function calculateHold(){
		// Сумма в холде
		$query = "SELECT SUM(webmaster_commission) FROM orders
				 WHERE user_id = :user_id AND country_code = :country_code AND target_close = 1 AND hold = 1 AND
				 (status IN (" . Order::STATUS_CONFIRMED . ", " . Order::STATUS_DELIVERED . " ) OR
				  (status = " . Order::STATUS_RETURN . " AND target = 1 ))";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->hold = floor($stmt->fetchColumn());
		}
	}

	protected function calculateProcessing(){
		// Сумма в обработке (Когда цель еще не выполнена)
		$accepted = [Order::STATUS_PROCESSING, Order::STATUS_CONFIRMED, Order::STATUS_DELIVERED];
		$query = "SELECT SUM(webmaster_commission) FROM orders
				WHERE user_id = :user_id AND country_code = :country_code AND target_close = 0 AND status IN (".implode(",", $accepted).")";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->processing = floor($stmt->fetchColumn());
		}
	}

	protected function calculateCanceled(){
		$query = "SELECT SUM(webmaster_commission) FROM orders WHERE user_id = :user_id AND country_code = :country_code AND
				    (status = ".Order::STATUS_CANCELED." OR (status = " . Order::STATUS_RETURN." AND target != 1)) AND
				  	(target_close = 0 OR hold = 1 OR `rollback` = 1)";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->canceled = floor($stmt->fetchColumn());
		}
	}
}

?>