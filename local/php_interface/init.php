<?
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/local/php_interface/logs/addMessage2Log.log");

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/Helper/Autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/Helper/Dump.php';
spl_autoload_register('autoLoad');
