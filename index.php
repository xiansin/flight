<?php
require_once 'flight/Flight.php';
require_once 'app/libs/Folder.php';
//include constant
require_once 'app/config/constant.php';
//Open the output control buffer
ob_start();
//close display errors
error_reporting(0);
//no cache
header("Cache-Control: no-cache, must-revalidate");
//set the session cookie expire time, before in session_start
session_start();
//set start time
define("START_TIME", microtime());
//set const DIR directory
define("DIR", dirname(__FILE__) . DIRECTORY_SEPARATOR);
//set const APP_PATH directory
define("APP_PATH", DIR . "app" . DIRECTORY_SEPARATOR);
//autoLoading config list
Flight::set(require APP_PATH . "config/config.php");
//autoLoading PHPExcel
Flight::set(require APP_PATH."libs/phpExcel/PHPExcel.php");
//Adds a path for autoLoading controllers classes.
Flight::path(Flight::get("flight.controllers.path"));
//Adds a path for autoLoading models classes.
Flight::path(Flight::get("flight.models.path"));
//Adds a path for autoLoading libs classes.
Flight::path(Flight::get("flight.libs.path"));
//autoLoading route list
$routes = Folder::getFolderFiles("./routes");
foreach ($routes as $route){
    //loop loading route files
    include_once $route;
}

//notFound redirect to 404 page
Flight::map('notFound', function () {
    include 'app/views/error/404.php';
});

Flight::before("start", array("Controller", "init"));

Flight::start();

