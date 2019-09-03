<?php


namespace App\Controllers\User;

use App\Controllers\Authenticated;
use Core\View;

class Profile extends Authenticated
{
    public function showAction(): void
    {
        $this->requireLogin();
        View::renderTemplate('User/Profile/show');
    }

    public function editAction(): void
    {
        View::renderTemplate('User/Profile/edit');
    }
}