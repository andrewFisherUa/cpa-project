<?php

class Balance {
	/**
	* @var int $user_id ID пользователя
	* @var string $country_code Код страны
	* @var double $hold Сумма в холде
	* @var double $processing Сумма в обработке (Когда цель еще не выполнена)
	* @var double $current Текущий баланс
	*/
	protected $account_id;
	protected $user_id;
	protected $country_code;
	protected $currency_code;
	protected $hold = 0;
	protected $processing = 0;
	protected $account_balance = 0;
	protected $current = 0;
	protected $canceled = 0;

	protected function __construct($user_id, $country_code = null){
		$this->user_id = $user_id;
		$this->country_code = $country_code;
		$this->calculateCurrent();
	}

	public static function get($user_id, $country_code = null) {
		if ($user_id == 0) {
			return new UMBalance($user_id, $country_code);
		}

		if (User::has_role($user_id, 2)) {
			// Вебмастер
			return new WebmasterBalance($user_id, $country_code);
		}

		if (User::has_role($user_id, 3)) {
			// Рекламодатель
			return new AdvertiserBalance($user_id, $country_code);
		}
	}

	protected function calculateCurrent(){
		//
	}

	protected function calculateCanceled(){
		//
	}

	protected function calculateHold(){
		//
	}

	protected function calculateProcessing(){
		//
	}


	public function getCurrencyCode() {
		return $this->currency_code;
	}

	public function getCountryCode() {
		return $this->country_code;
	}

	public function getCountryName(){
		return Country::getName($this->country_code);
	}

	public function getHold(){
		$this->calculateHold();
		return $this->hold;
	}

	public function getAccountId(){
		return $this->account_id;
	}

	public function getProcessing(){
		$this->calculateProcessing();
		return $this->processing;
	}

	public function getCurrent(){
		return $this->current;
	}

	public function getCanceled(){
		$this->calculateCanceled();
		return $this->canceled;
	}

	public function getCanceledRef(){
		$this->calculateCanceledRef();
		return $this->canceled_ref;
	}

	public function getReferal(){
		$this->calculateReferal();
		return $this->referal;
	}

	public function getReferalInHold(){
		$this->calculateReferalInHold();
		return $this->referal_in_hold;
	}

	public function getAccountBalance(){
		return $this->account_balance;
	}

	public static function getAll($user_id){
		$items = array();
		foreach (Country::getAll() as $country) {
			$items[$country["code"]] = self::get($user_id, $country["code"]);
		}
		return $items;
	}

	private static function setDefaultBalanceType($user_id, $type) {
		return self::createAccount($user_id, $type, true);
	}

	public static function requestChangeAccountCurrency($user_id, $currency){
		$default = self::getDefaultBalanceType($user_id);

		$currency = Country::getCurrencyCode($currency);
		$default_currency = Country::getCurrencyCode($default);

		$query = "INSERT INTO account_currency (user_id, currency, default_currency, status, created) VALUES (:user_id, :currency, :default_currency, 'processing', " . time() . ")";

		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":currency", $currency, PDO::PARAM_STR);
		$stmt->bindParam(":default_currency", $default_currency, PDO::PARAM_STR);
		return $stmt->execute();
	}

	public static function changeAccountCurrencyStatus($id, $status) {
		$stmt = $GLOBALS['DB']->prepare("SELECT currency, user_id FROM account_currency WHERE id = ?");
		$stmt->execute([$id]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		$user_id = $data['user_id'];
		$code = Country::getCode($data['currency']);

		if ($status == "approved") {
			self::setDefaultBalanceType($user_id, $code, true);
		}

		$stmt = $GLOBALS['DB']->prepare("UPDATE account_currency SET status = :s, changed = " . time() . " WHERE id = :id");
		$stmt->bindParam(":s", $status, PDO::PARAM_INT);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		return $stmt->execute();
	}

	public static function createAccount($user_id, $type, $isDefault = false){
		// Проверить существует аккаунт или нет
		$stmt = $GLOBALS['DB']->prepare("SELECT account_id FROM accounts WHERE user_id = :user_id AND type = :type");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":type", $type, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$account_id = $stmt->fetchColumn();
			if ($isDefault) {
				// Убрать default = 1 у других аккаунтов этого пользователя
				$stmt = $GLOBALS['DB']->prepare("UPDATE accounts SET `default` = 0 WHERE user_id = :user_id");
				$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
				$stmt->execute();
			}

			$query = "UPDATE accounts SET `default` = :default WHERE account_id = :account_id";
			$stmt2 = $GLOBALS['DB']->prepare($query);
			$stmt2->bindParam(":default", $isDefault, PDO::PARAM_INT);
			$stmt2->bindParam(":account_id", $account_id, PDO::PARAM_INT);
			$stmt2->execute();
			return $account_id;
		}

		if ($isDefault) {
			// Убрать default = 1 у других аккаунтов этого пользователя
			$stmt = $GLOBALS['DB']->prepare("UPDATE accounts SET `default` = 0 WHERE user_id = :user_id");
			$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
			$stmt->execute();
		}

		// Создать аккаунт
		$stmt = $GLOBALS['DB']->prepare("INSERT INTO accounts(type, user_id, balance, `default`) VALUES(:type, :user_id, 0, :default)");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->bindParam(":default", $isDefault, PDO::PARAM_INT);
		$stmt->bindParam(":type", $type, PDO::PARAM_STR);
		$stmt->execute();
		return $GLOBALS['DB']->lastInsertId();
	}

	protected function getDefaultAccountType(){
		// Нужен баланс по умолчанию
		$query = "SELECT type FROM accounts WHERE user_id = :user_id AND `default` = 1";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$this->country_code = $stmt->fetchColumn();
		} else {
			// Создать аккаунт по умолчанию
			$stmt2 = $GLOBALS['DB']->prepare("SELECT type FROM accounts WHERE user_id = ?");
			$stmt2->execute([$this->user_id]);
			if ($stmt2->rowCount()) {
				// Если у пользователя есть хотя бы один аккаунт, делаем его аккаунтом по умолчанию
				$this->country_code = $stmt2->fetchColumn();
				$stmt3 = $GLOBALS['DB']->prepare("UPDATE accounts SET `default` = 1 WHERE type = :type AND user_id = :user_id");
				$stmt3->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
				$stmt3->bindParam(":type", $this->country_code, PDO::PARAM_STR);
				$stmt3->execute();
			} else {
				// Если у пользователя нет аккаунтов - создать.
				$this->country_code = "ru";
				self::createAccount($this->user_id, $this->country_code, true);
			}
		}
	}

	protected function fetchAccountBalance(){
		if (is_null($this->country_code)) {
			$this->getDefaultAccountType();
		}

		$query = "SELECT account_id AS id, balance FROM accounts WHERE user_id = :user_id AND type = :type";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $this->user_id, PDO::PARAM_INT);
		$stmt->bindParam(":type", $this->country_code, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowCount()) {
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->account_balance = (int) $data['balance'];
			$this->current = (int) $data['balance'];
			$this->account_id = $data['id'];
		} else {
			// create account
			$this->account_id = self::createAccount($this->user_id, $this->country_code);
			$this->current = 0;
			$this->account_balance = 0;
		}

		$this->currency_code = Country::getCurrencyCode($this->country_code);
	}

	public static function getDefaultBalanceType($user_id){
		$query = "SELECT type FROM accounts WHERE user_id = :user_id AND `default` = 1";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount()) {
			return $stmt->fetchColumn();
		}

		$type = "ru";

		// Если аккаунт по умолчанию не найден создаем аккаунт `RU`
		self::createAccount($user_id, $type, true);
		return $type;
	}

	public static function getDefaultCurrency($user_id){
		$query = "SELECT type FROM accounts WHERE user_id = :user_id AND `default` = 1";
		$stmt = $GLOBALS['DB']->prepare($query);
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stmt->execute();
		if ($stmt->rowCount()) {
			return Country::getCurrencyCode($stmt->fetchColumn());
		}
		return null;
	}

}

?>