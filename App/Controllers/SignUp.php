<?php


namespace App\Controllers;

use Core\Controller;
use Core\View;

class SignUp extends Controller
{
    public function indexAction(): void
    {
        View::renderTemplate("Signup/new");
    }

    public function createAction(): void
    {
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
    }


}