<?php

class Country {

	public static $_instance = null;

	public static function init(){
		if (is_null(self::$_instance)) {
			$stmt = $GLOBALS['DB']->query("SELECT *, c_id as id FROM country");
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				self::$_instance[$row['id']] = $row;
				self::$_instance[$row['code']] = $row;
				self::$_instance[$row['currency_code']] = $row;
			}
		}
	}

	public static function getCode($param){
		self::init();
		return self::$_instance[$param]['code'];
	}

	public static function getCurrencyCode($param){
		self::init();
		return self::$_instance[$param]['currency_code'];
	}

	public static function getCurrency($param){
		self::init();
		return self::$_instance[$param]['currency'];
	}

	public static function getName($param){
		self::init();
		return self::$_instance[$param]['name'];
	}

	public static function getId($param){
		self::init();
		return self::$_instance[$param]['c_id'];
	}

    public static function getAll() {
    	self::init();
        $stmt = $GLOBALS['DB']->query("SELECT * FROM country ORDER BY c_id");
        $items = array();
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        	$items[$data['code']] = $data;
        }
        return $items;
    }

}