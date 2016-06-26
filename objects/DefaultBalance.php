<?php

class DefaultBalance {

	private $currency;
	private $code;
	private $accounts = [];

	public function __construct($user_id = null) {
		if (is_null($user_id)) {
			$this->code = "ru";
		} else {
			$this->code = Balance::getDefaultBalanceType($user_id, true);
		}

		foreach ($this->fetchCountries() as $c) {
			$this->accounts[$c] = Balance::get($user_id, $c);
		}
		$this->currency = Country::getCurrencyCode($this->code);
	}

	public function getCountryCode(){
		return $this->code;
	}

	public function getCurrencyCode(){
		return $this->currency;
	}

	private function fetchCountries(){
		$stmt = $GLOBALS['DB']->query("SELECT code FROM country");
		return $stmt->fetchAll(PDO::FETCH_COLUMN);
	}

	public function getCurrent(){
		$current = $this->accounts[$this->code]->getCurrent();

		foreach ($this->accounts as $k=>$a) {
			if ($k != $this->code) {
				$t = Converter::getConvert($a->getCurrencyCode(), $this->currency, $a->getCurrent(), $this->currency);
				$current += $t['amount'];
			}
		}

		return floor($current);
	}

	public function getCanceled(){
		$canceled = $this->accounts[$this->code]->getCanceled();

		foreach ($this->accounts as $k=>$a) {
			if ($k != $this->code) {
				$t = Converter::getConvert($a->getCurrencyCode(), $this->currency, $a->getCanceled(), $this->currency);
				$canceled += $t['amount'];
			}
		}

		return floor($canceled);
	}

	public function getHold(){
		foreach ($this->accounts as $k=>$a) {
			if ($k == $this->code) {
				$hold += $a->getHold();
			} else {
				$t = Converter::getConvert($a->getCurrencyCode(), $this->currency, $a->getHold(), $this->currency);
				$hold += $t['amount'];
			}
		}

		return floor($hold);
	}

	public function getReferal(){
		if ($this->accounts[$this->code] instanceof UMBalance) {
			return false;
		}

		$referal = $this->accounts[$this->code]->getReferal();

		foreach ($this->accounts as $k=>$a) {
			if ($k != $this->code) {
				$t = Converter::getConvert($a->getCurrencyCode(), $this->currency, $a->getReferal(), $this->currency);
				$referal += $t['amount'];
			}
		}

		return floor($referal);
	}

}