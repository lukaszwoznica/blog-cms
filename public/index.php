<?php
/**
 * Front controller
 */

/**
 * Autoload
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->addRoute('{controller}/{action}');
$router->addRoute('{controller}/{id:\d+}/{action}');
$router->addRoute('', ['controller' => 'Home', 'action' => 'index']);

$router->dispatch($_SERVER['QUERY_STRING']);