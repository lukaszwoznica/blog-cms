<?php

namespace Core;

/**
 * Base Controller
 */

abstract class Controller {

    /**
     * Parameters from matched route
     */
    protected $route_params = [];

    public function __construct(array $route_params){
        $this->route_params = $route_params;
    }
}