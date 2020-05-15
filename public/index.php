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


// Variable routes

// App\Controllers\User namespace
$router->addRoute('user/password/reset/{token:[\da-f]+}', [
    'controller' => 'Password',
    'action' => 'reset',
    'namespace' => 'User'
]);
$router->addRoute('user/{controller}/{action}', ['namespace' => 'User']);

// App\Controllers\Admin namespace
$router->addRoute('admin/{controller}', ['namespace' => 'Admin', 'action' => 'index']);
$router->addRoute('admin/{controller}/{action}', ['namespace' => 'Admin']);
$router->addRoute('admin/{controller}/{id:\d+}/{action}', ['namespace' => 'Admin']);
$router->addRoute('admin/{controller}/{page:\d+}', ['namespace' => 'Admin', 'action' => 'index']);

// App\Controllers namespace
$router->addRoute('categories/{slug:[\da-z-]+}', ['controller' => 'Categories', 'action' => 'show']);
$router->addRoute('categories/{slug:[\da-z-]+}/page/{page:\d+}', ['controller' => 'Categories', 'action' => 'show']);
$router->addRoute('posts/page/{page:\d+}', ['controller' => 'Posts', 'action' => 'index']);
$router->addRoute('posts/{slug:[\da-z-]+}', ['controller' => 'Posts', 'action' => 'show']);
$router->addRoute('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->addRoute('{controller}', ['action' => 'index']);
$router->addRoute('{controller}/{action}');
$router->addRoute('{controller}/{id:\d+}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);