<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

/**
 * Home Controller
 */

class Home extends Controller {

    protected function before(){

    }

    protected function after(){

    }

     public function indexAction(): void {
        View::renderTemplate('Home/index', [
            'name' => "Łukasz"
        ]);
    }
}