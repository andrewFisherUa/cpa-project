<?php

define('PATH', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
define('PATH_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('PATH_CACHE', PATH_ROOT . DS .'cache');
define('PATH_AJAX', PATH_ROOT . DS . 'components' . DS . 'ajax');
define('PATH_TEMPLATES', PATH_ROOT . DS .'templates');
define('PATH_TEMPLATES_C', PATH_CACHE.DS .'templates_c');

define('SITE_URL', 'http://univermag');
define('API_URL', 'http://api.univer-mag.com');
define('STREAMS_URL', 'http://univermag/streams');
define('ORDERS_HANDLER_URL', 'http://univermag/orders/handler.php');

// define('DB_USERNAME', 'umagdev');
// define('DB_HOST', '188.165.5.44');
// define('DB_PASSWORD', 'h4MXRpaNajPTq5nm');
// define('DB_NAME', 'umagdev');    

define('DB_USERNAME', 'root');
define('DB_HOST', 'localhost');
define('DB_PASSWORD', '');
define('DB_NAME', 'univermag');

$GLOBALS["DB"] = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
