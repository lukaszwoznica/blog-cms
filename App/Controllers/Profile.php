<?php


namespace App\Controllers;

use Core\View;

class Profile extends Authenticated
{
    public function indexAction(): void
    {
        $this->requireLogin();
        View::renderTemplate('Profile/index');
    }
}