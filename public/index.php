<?php
/**
 * Front controller
 */


require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Autoloader
 */
spl_autoload_register(function ($class) {
   $root = dirname(__DIR__);
   $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
   if (is_readable($file)){
       require_once $root . '/' . str_replace('\\', '/', $class) . '.php';
   }
});

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->addRoute('{controller}/{action}');
$router->addRoute('{controller}/{id:\d+}/{action}');
$router->addRoute('', ['controller' => 'Home', 'action' => 'index']);

$router->dispatch($_SERVER['QUERY_STRING']);