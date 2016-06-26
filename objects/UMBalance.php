<?php

class UMBalance extends Balance {

	/**
	* @var double $profit Прибыль
	* @var double $expense Расход
	* @var double $expense_in_hold Расход в холде
	*/

	private $profit;
	private $expense = 0;
	private $expense_in_hold = 0;

	public function __construct($user_id, $country_code = null){
		parent::__construct($user_id, $country_code);
	}

	protected function calculateCurrent(){
		$this->fetchAccountBalance();
		$this->current = $this->current + $this->getProfit() - $this->getExpense();
	}

	public function getExpense(){
		$this->calculateExpense();
		return $this->expense;
	}

	public function getExpenseInHold(){
		$this->calculateExpenseInHold();
		return $this->expense_in_hold;
	}

	public function getProfit(){
		$this->calculateProfit();
		return $this->profit;
	}

	private function calculateExpense(){
		$query = "SELECT SUM(t2.amount) AS amount
				  FROM orders AS t1 INNER JOIN order_refprofit AS t2 ON t1.id = t2.order_id
				  WHERE t1.country_code = :country_code AND t1.status = " . Order::STATUS_DELIVERED . " AND t1.pass > 0";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		$this->expense = floor($stmt->fetchColumn());
	}

	private function calculateExpenseInHold(){
		$query = "SELECT SUM(t2.amount) AS amount
				  FROM orders AS t1 INNER JOIN order_refprofit AS t2 ON t1.id = t2.order_id
				  WHERE t1.country_code = :country_code AND t1.target_close = 1 AND t1.hold = 1";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		$this->expense_in_hold = floor($stmt->fetchColumn());
	}

	private function calculateProfit(){
		$query = "SELECT SUM(commission) FROM pass WHERE country_code = :country_code";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->profit = floor($stmt->fetchColumn());
		}
	}

	protected function calculateHold(){
		// Сумма в холде
		$query = "SELECT SUM(commission) FROM orders WHERE country_code = :country_code AND target_close = 1 AND hold = 1 AND
				(status IN (" . Order::STATUS_CONFIRMED . ", " . Order::STATUS_DELIVERED .") OR
				(status = " . Order::STATUS_RETURN . " AND target = 1))";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->hold = floor($stmt->fetchColumn());
		}
	}

	protected function calculateProcessing(){
		// Сумма в обработке (Когда цель еще не выполнена)
		$accepted = [Order::STATUS_PROCESSING, Order::STATUS_CONFIRMED, Order::STATUS_DELIVERED];
		$query = "SELECT SUM(commission) FROM orders WHERE country_code = :country_code AND target_close = 0 AND status IN (".implode(",", $accepted).")";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->processing = floor($stmt->fetchColumn());
		}
	}

	protected function calculateCanceled(){
		$query = "SELECT SUM(commission) FROM orders WHERE country_code = :country_code AND
				   (status = ".Order::STATUS_CANCELED." OR (status = " . Order::STATUS_RETURN." AND target != 1)) AND
				   (target_close = 0 OR hold = 1 OR `rollback` = 1)";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":country_code", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->canceled = floor($stmt->fetchColumn());
		}
	}
}

?>