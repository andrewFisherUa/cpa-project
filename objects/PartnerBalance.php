<?php

class PartnerBalance extends Balance {

	/**
	* @var double $referral Реферальный баланс
	* @var double $referral_in_hold Реферальный баланс в холде
	* @var double $canceled Отмененный баланс (в случае аннулирования или возврата заказов)
	* @var double $canceled_ref Отмененный реферальный баланс (в случае аннулирования или возврата заказов)
	*/

	protected $referal = 0;
	protected $referal_in_hold = 0;
	protected $canceled = 0;
	protected $canceled_ref = 0;

	public function __construct($user_id, $country_code = null){
		parent::__construct($user_id, $country_code);
	}

	protected function calculateReferal(){
		// Реф. баланс
		$query = "SELECT SUM(t2.amount) AS amount
				  FROM orders AS t1 INNER JOIN order_refprofit AS t2 ON t1.id = t2.order_id
				  WHERE t2.user_id = :user_id AND t1.country_code = :country_code AND t1.status = " . Order::STATUS_DELIVERED . " AND t1.hold = 0 AND t2.closed = 0";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->referal = (int) $stmt->fetchColumn();
		}
	}

	// неправильно отображается когда холд закончился и статус подтвержден
	protected function calculateReferalInHold(){
		// Реф. баланс в холде
		$query = "SELECT SUM(t2.amount) AS amount
				  FROM orders AS t1 INNER JOIN order_refprofit AS t2 ON t1.id = t2.order_id
				  WHERE t2.user_id = :user_id AND t1.country_code = :country_code AND
				  (t1.status = " . Order::STATUS_CONFIRMED." OR (t1.status = " . Order::STATUS_DELIVERED." AND t1.hold = 1))";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->referal_in_hold = (int) $stmt->fetchColumn();
		}
	}

	protected function calculateCanceledRef(){
		$query = "SELECT SUM(t2.amount) AS amount
				  FROM orders AS t1 INNER JOIN order_refprofit AS t2 ON t1.id = t2.order_id
				  WHERE t2.user_id = :user_id AND t1.country_code = :country_code AND
				  		t1.status IN (".Order::STATUS_CANCELED.", ".Order::STATUS_RETURN.")";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->canceled_ref = (int) $stmt->fetchColumn();
		}
	}
}

?>