<?php


namespace App\Controllers\Admin;

use App\Controllers\Authenticated;

abstract class Admin extends Authenticated
{
    protected function before(): void
    {
        parent::before();
        $this->requireAdminAuthorization();
    }
}