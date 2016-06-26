<?php

class Transaction {

	private $id;
	private $type;
	private $from_account;
	private $to_account;
	private $amount;
	private $created;
	private $desc;
	private $user_id;
	private $status;
	private $rate;
	private $from_currency;
	private $to_currency;
	private $from_amount;
	private $residue;

	const TYPE_IN = "in"; // пополнение баланса
	const TYPE_OUT = "out"; // снятие
	const TYPE_TRANSFER = "transfer"; // перевод

	const STATUS_PROCESSING = "processing";
	const STATUS_APPROVED = "approved";
	const STATUS_CANCELED = "canceled";

	private function __construct($data = array()){
		$this->id = (isset($data['id'])) ? $data['id'] : 0;
		$this->type = (isset($data['type'])) ? $data['type'] : "";
		$this->from_account = (isset($data['from_account'])) ? $data['from_account'] : 0;
		$this->to_account = (isset($data['to_account'])) ? $data['to_account'] : 0;
		$this->amount = (isset($data['amount'])) ? $data['amount'] : 0;
		$this->created = (isset($data['created'])) ? $data['created'] : time();
		$this->desc = (isset($data['desc'])) ? $data['desc'] : "";
		$this->user_id = (isset($data['user_id'])) ? $data['user_id'] : 0;
		$this->status = (isset($data['status'])) ? $data['status'] : self::STATUS_PROCESSING;
		$this->rate = (isset($data['rate'])) ? $data['rate'] : 0;
		$this->from_currency = (isset($data['from_currency'])) ? $data['from_currency'] : "";
		$this->to_currency = (isset($data['to_currency'])) ? $data['to_currency'] : "";
		$this->from_amount = (isset($data['from_amount'])) ? $data['from_amount'] : 0;
		$this->residue = (isset($data['residue'])) ? $data['residue'] : 0;
	}

	public function getType(){
		return $this->type;
	}

	public function getStatus(){
		return $this->status;
	}

	public function getFromAccountId(){
		return $this->from_account;
	}

	public function getToAccountId(){
		return $this->to_account;
	}

	public function getAmount(){
		return $this->amount;
	}

	public function getFromAmount(){
		return $this->from_amount;
	}

	/**
	 * Проверят правильность параметров при добавлении нового значения валюты
	 *
	 * @param array $data Параметры значения валюты
	 * @return boolean | array Возвращает true если параметры корректы, список ошибок - в другом случае
	 */
	private function check(){

		$stmt = $GLOBALS['DB']->query("SELECT currency_code FROM country");
		$c = $stmt->fetchAll(PDO::FETCH_COLUMN);

		if ($this->user_id == 0) {
			$errors[] = "user_id - обязательное поле";
		}

		if (empty($this->type)) {
			$errors[] = "type - обязательное поле";
		}

		if ($this->type == self::TYPE_TRANSFER) {
			if (empty($this->from_currency)) {
				$errors[] = "Необходимо выбрать баланс, с которого осуществляется перевод";
			} else {
				if (!in_array($this->from_currency, $c)) {
					$errors[] = "Валюта `{$this->from_currency}` не найдена";
				}
			}

			if (empty($this->to_currency)) {
				$errors[] = "Необходимо выбрать баланс, на который осуществляется перевод";
			} else {
				if (!in_array($this->to_currency, $c)) {
					$errors[] = "Валюта `{$this->to_currency}` не найдена";
				}
			}

			if ($this->from_amount <= 0) {
				$errors[] = "Сумма перевода должна быть больше 0";
			}
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

	public static function add($data){

		switch ($data['type']) {
			case self::TYPE_TRANSFER : $data['status'] = self::STATUS_PROCESSING; break;
			case self::TYPE_IN : $data['status'] = self::STATUS_APPROVED; break;
		}

		$instance = new self($data);
		$valid = $instance->check();

		if ($valid === true) {
			if ($instance->type == self::TYPE_TRANSFER) {
				// Дополняем недостающие поля
				$from = Balance::get($instance->user_id, Country::getCode($instance->from_currency));
				if ($from->getCurrent() < $instance->amount_from){
					return ["Недостаточно средств"];
				}

				$to = Balance::get($instance->user_id, Country::getCode($instance->to_currency));
				$instance->from_account = $from->getAccountId();
				$instance->to_account = $to->getAccountId();

				$c = Converter::getConvert($instance->from_currency, $instance->to_currency, $instance->from_amount, Balance::getDefaultCurrency($instance->user_id));
				$instance->amount = $c['amount'];
				$instance->rate = $c['rate'];
			}

 			return $instance->save();
		}

		return $valid;
	}

	public function save() {
		// save
		$query = "INSERT INTO transactions(type, from_account, to_account, amount, created, `desc`, user_id, status, rate, from_currency, to_currency, from_amount)
				VALUES (:type, :from_account, :to_account, :amount, :created, :desc, :user_id, :status, :rate, :from_currency, :to_currency, :from_amount)";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":type", $this->type, PDO::PARAM_STR);
		$stmt->bindParam(":from_account", $this->from_account, PDO::PARAM_INT);
		$stmt->bindParam(":to_account", $this->to_account, PDO::PARAM_INT);
		$stmt->bindParam(":amount", $this->amount, PDO::PARAM_INT);
		$stmt->bindParam(":created", $this->created, PDO::PARAM_INT);
		$stmt->bindParam(":desc", $this->desc, PDO::PARAM_STR);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":status", $this->status, PDO::PARAM_STR);
		$stmt->bindParam(":rate", $this->rate, PDO::PARAM_INT);
		$stmt->bindParam(":from_currency", $this->from_currency, PDO::PARAM_STR);
		$stmt->bindParam(":to_currency", $this->to_currency, PDO::PARAM_STR);
		$stmt->bindParam(":from_amount", $this->from_amount, PDO::PARAM_INT);
		if ($stmt->execute()) {
			$this->id = $GLOBALS['DB']->lastInsertId();
			if ($this->type == self::TYPE_IN) {
				$stmt2 = $GLOBALS['DB']->prepare("UPDATE accounts SET balance = balance + :amount WHERE account_id = :account_id");
				$stmt2->bindParam(":amount", $this->amount, PDO::PARAM_INT);
				$stmt2->bindParam(":account_id", $this->to_account, PDO::PARAM_INT);
				$stmt2->execute();
			}

			if ($this->type == self::TYPE_OUT) {
				$stmt2 = $GLOBALS['DB']->prepare("UPDATE accounts SET balance = balance - :amount WHERE account_id = :account_id");
				$stmt2->bindParam(":amount", $this->amount, PDO::PARAM_INT);
				$stmt2->bindParam(":account_id", $this->from_account, PDO::PARAM_INT);
				$stmt2->execute();
			}

			// temp
			//if ($this->type == self::TYPE_TRANSFER) {
				//self::changeStatus($this->id, self::STATUS_APPROVED);
			//}
			// end temp

			return true;
		}

		return false;
	}

	public function get($id){
		$stmt = $GLOBALS['DB']->prepare("SELECT *, transaction_id as id FROM transactions WHERE transaction_id = ?");
		$stmt->execute([$id]);
		if ($stmt->rowCount()) {
			return new self($stmt->fetch(PDO::FETCH_ASSOC));
		}
	}

	public static function changeStatus($id, $status){
		$instance = self::get($id);
		if ($status == self::STATUS_APPROVED && $instance->getStatus() != self::STATUS_APPROVED) {
			// статус перевода - одобрен (Перевести деньги)
			$from_amount = $instance->getFromAmount();
			$from_account = $instance->getFromAccountId();
			$to_amount = $instance->getAmount();
			$to_account = $instance->getToAccountId();

			$stmt = $GLOBALS['DB']->prepare("UPDATE accounts SET balance = balance - ? WHERE account_id = ?");
			$stmt->execute([$from_amount, $from_account]);

			$stmt = $GLOBALS['DB']->prepare("UPDATE accounts SET balance = balance + ? WHERE account_id = ?");
			$stmt->execute([$to_amount, $to_account]);

			$stmt = $GLOBALS['DB']->prepare("SELECT balance FROM accounts WHERE account_id = ?");
			$stmt->execute([$from_account]);
			$residue = $stmt->fetchColumn();

			$stmt = $GLOBALS['DB']->prepare("UPDATE transactions SET status = ?, residue = ? WHERE transaction_id = ?");
			$stmt->execute([$status, $residue, $id]);
		}

	}
}

?>