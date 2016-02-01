<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

Yaf_Loader::import(APPLICATION_PATH.'/application/init.php');

$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();
?>
