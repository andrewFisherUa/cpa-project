<?php

class AdvertiserBalance extends PartnerBalance {

	public function __construct($user_id, $country_code = null){
		parent::__construct($user_id, $country_code);
	}

	protected function calculateCurrent(){
		$this->fetchAccountBalance();
	}

	protected function calculateHold(){
		// Сумма в холде
		$query = "SELECT SUM(t2.commission+t2.webmaster_commission)
				  FROM orders AS t1 INNER JOIN order_goods AS t2 ON t1.id = t2.order_id
				  WHERE t2.product_owner = :user_id AND t1.country_code = :country_code AND t1.hold != 0 AND
				  (t1.status IN (" . Order::STATUS_CONFIRMED . ", " . Order::STATUS_DELIVERED . ", " . Order::STATUS_PROCESSING . " ) OR
				  (t1.status = ".Order::STATUS_RETURN."  AND t1.target = 1))";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->hold = floor($stmt->fetchColumn());
		}
	}

	protected function calculateProcessing(){
		// Сумма в обработке (Когда цель еще не выполнена)+`
		return 0;
	}

	protected function calculateCanceled(){
		$query = "SELECT SUM(t2.commission+t2.webmaster_commission)
				  FROM orders AS t1 INNER JOIN order_goods AS t2 ON t1.id = t2.order_id
				  WHERE t2.product_owner = :user_id AND t1.country_code = :country_code AND
				  (t1.status = ".Order::STATUS_CANCELED." OR (t1.status = " . Order::STATUS_RETURN." AND t1.target != 1)) AND
				  (t1.target_close = 0 OR t1.hold = 1 OR t1.`rollback` = 1)";
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