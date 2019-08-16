<?php

namespace App\Controllers;

/**
 * Home Controller
 */

class Home extends \Core\Controller {

    public function index(): void {
        echo "Hello form the index in Home controller!";
    }
}