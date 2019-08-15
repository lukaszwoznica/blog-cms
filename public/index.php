<?php
/**
 * Front controller
 */

/**
 * Loads
 */
require_once __DIR__ . '/../Core/Router.php';

/**
 * Routing
 */
$router = new Router();

// Add the routes
$router->addRoute('', ['controller' => 'Home', 'action' => 'index']);
$router->addRoute('{controller}/{action}');
$router->addRoute('{controller}/{id:\d+}/{action}');

