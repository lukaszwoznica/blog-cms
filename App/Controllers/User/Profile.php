<?php


namespace App\Controllers\User;

use App\Controllers\Authenticated;
use Core\View;

class Profile extends Authenticated
{
    public function indexAction(): void
    {
        $this->requireLogin();
        View::renderTemplate('User/Profile/index');
    }
}