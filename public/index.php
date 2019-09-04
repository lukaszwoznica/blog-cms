<?php
/*
 * Front controller
 */

/*
 * Autoload
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

/*
 * Error and exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/*
 * Sessions
 */
session_start();

/*
 * Routing
 */
$router = new Core\Router;

// Add the routes

// Fixed routes
$router->addRoute('', ['controller' => 'Home', 'action' => 'index']);
$router->addRoute('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->addRoute('admin/dashboard', [
    'namespace' => 'Admin',
    'controller' => 'Dashboard',
    'action' => 'index'
]);

// Variable routes

// App\Controllers namespace
$router->addRoute('{controller}', ['action' => 'index']);
$router->addRoute('{controller}/', ['action' => 'index']);
$router->addRoute('{controller}/{action}');
$router->addRoute('{controller}/{id:\d+}/{action}');
$router->addRoute('signup/activate/{token:[\da-f]+}', ['controller' => 'SignUp', 'action' => 'activate']);

// App\Controllers\User namespace
$router->addRoute('user/password/reset/{token:[\da-f]+}', [
    'controller' => 'Password',
    'action' => 'reset',
    'namespace' => 'User'
]);
$router->addRoute('user/{controller}/{action}', ['namespace' => 'User']);

// App\Controllers\Admin namespace
$router->addRoute('admin/{controller}/{action}', ['namespace' => 'Admin']);


$router->dispatch($_SERVER['QUERY_STRING']);