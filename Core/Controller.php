<?php

namespace Core;

use App\Auth;
use Exception;

/**
 * Base Controller
 */

abstract class Controller
{

    /**
     * Parameters from matched route
     */
    protected $route_params = [];

    public function __construct(array $route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * Execute before and after filter methods on action methods
     * @param string $name
     * @param array $args
     * @throws Exception
     */
    public function __call(string $name, array $args): void
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)){
            if ($this->before() !== false){
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new Exception("Method $method not found in ". get_class($this) . " controller", 404);
        }
    }

    protected function before()
    {

    }

    protected function after()
    {

    }

    public function redirectTo(string $url) : void
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit();
    }

    /**
     * Require the user to login before giving access to the requested page
     */
    public function requireLogin(): void
    {
        if (!Auth::getUser()) {
            Auth::rememberRequestedPage();
            $this->redirectTo('/login');
        }
    }

    public function requireAdminAuthorization(): void
    {
        if (Auth::getUser()->getRoleId() !== 1) {
            $this->redirectTo('/');
        }
    }
}