<?php
ini_set('display_errors','Off');
error_reporting('E_ERROR');

session_start();

require_once 'config.php';
require_once PATH_ROOT . DS . 'smarty' . DS . 'Smarty.class.php';
require_once PATH_ROOT . DS . 'misc'. DS . 'php' . DS . 'functions.php';

spl_autoload_register('autoloader');

require_once PATH_ROOT . DS . 'misc'. DS . 'php' . DS . 'users_online.php';

$smarty = new Smarty;
$app = new AppController();

$app->getContent();
