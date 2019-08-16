<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Home Controller
 */

class Home extends Controller {

    protected function before(){

    }

    protected function after(){

    }

     public function indexAction(): void {
        echo "Hello form the index in Home controller!";
    }
}