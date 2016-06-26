<?php

/**
 * Phalcon\Filter
 *
 * The Phalcon\Filter component provides a set of commonly needed data filters. It provides
 * object oriented wrappers to the php filter extension. Also allows the developer to
 * define his/her own filters
 *
 *<code>
 *	$filter = new \Phalcon\Filter();
 *	$filter->sanitize("some(one)@exa\\mple.com", "email"); // returns "someone@example.com"
 *	$filter->sanitize("hello<<", "string"); // returns "hello"
 *	$filter->sanitize("!100a019", "int"); // returns "100019"
 *	$filter->sanitize("!100a019.01a", "float"); // returns "100019.01"
 *</code>
 */
class Filter {

	const FILTER_EMAIL      = "email";
	const FILTER_ABSINT     = "absint";
	const FILTER_INT        = "int";
	const FILTER_INT_CAST   = "int!";
	const FILTER_STRING     = "string";
	const FILTER_FLOAT      = "float";
	const FILTER_FLOAT_CAST = "float!";
	const FILTER_ALPHANUM   = "alphanum";
	const FILTER_TRIM       = "trim";
	const FILTER_STRIPTAGS  = "striptags";
	const FILTER_LOWER      = "lower";
	const FILTER_UPPER      = "upper";

	/**
	 * Sanitizes a value with a specified single or set of filters
	 */
	public function sanitize($value, $filters, $noRecursive = false) {
		if (is_array($filters)) {
			if (!is_null($value)){
				foreach ($filters as $filter) {
					if (is_array($value) && !$noRecursive) {
						$arrayValue = [];
						foreach ($value as $itemKey=>$itemValue) {
							$arrayValue[$itemKey] = $this->_sanitize($itemValue, $filter);
						}
						$value = $arrayValue;
					} else {
						$value = $this->_sanitize($value, $filter);
					}
				}
			}

			return $value;
		}

		if (is_array($value) && !$noRecursive) {
			$sanitizedValue = [];
			foreach ($value as $itemKey=>$itemValue) {
				$sanitizedValue[$itemKey] = $this->_sanitize($itemValue, $filters);
			}
			return $sanitizedValue;
		}

		return $this->_sanitize($value, $filters);
	}

	/**
	 * Internal sanitize wrapper to filter_var
	 */
	protected function _sanitize($value, $filter){

		switch ($filter) {

			case Filter::FILTER_EMAIL:
				/**
				 * The 'email' filter uses the filter extension
				 */
				return filter_var($value, constant("FILTER_SANITIZE_EMAIL"));

			case Filter::FILTER_INT:
				/**
				 * 'int' filter sanitizes a numeric input
				 */
				return filter_var($value, FILTER_SANITIZE_NUMBER_INT);

			case Filter::FILTER_INT_CAST:

				return intval($value);

			case Filter::FILTER_ABSINT:

				return abs(intval($value));

			case Filter::FILTER_STRING:

				return filter_var($value, FILTER_SANITIZE_STRING);

			case Filter::FILTER_FLOAT:
				/**
				 * The 'float' filter uses the filter extension
				 */
				return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, ["flags"=> FILTER_FLAG_ALLOW_FRACTION]);

			case Filter::FILTER_ALPHANUM:

				return preg_replace("/[^A-Za-z0-9]/", "", $value);

			case Filter::FILTER_TRIM:

				return trim($value);

			case Filter::FILTER_STRIPTAGS:

				return strip_tags($value);

			case Filter::FILTER_LOWER:

				if (function_exists("mb_strtolower")) {
					/**
					 * 'lower' checks for the mbstring extension to make a correct lowercase transformation
					 */
					return mb_strtolower($value);
				}
				return strtolower($value);

			case Filter::FILTER_UPPER:

				if (function_exists("mb_strtoupper")) {
					/**
					 * 'upper' checks for the mbstring extension to make a correct lowercase transformation
					 */
					return mb_strtoupper($value);
				}
				return strtoupper($value);

			default:
				throw new Exception("Sanitize filter '" . filter . "' is not supported");
		}
	}
}

?>