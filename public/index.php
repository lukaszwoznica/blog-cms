<?php
/**
 * Front controller
 */

/**
 * Autoload
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Error and exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->addRoute('{controller}/{action}');
$router->addRoute('{controller}/{id:\d+}/{action}');
$router->addRoute('', ['controller' => 'Home', 'action' => 'index']);

$router->dispatch($_SERVER['QUERY_STRING']);