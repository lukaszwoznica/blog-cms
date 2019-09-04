<?php


namespace App\Controllers\Admin;


use Core\View;

class Dashboard extends Admin
{
    public function indexAction(): void
    {
        View::renderTemplate('Admin/Dashboard/index.html');
    }
}