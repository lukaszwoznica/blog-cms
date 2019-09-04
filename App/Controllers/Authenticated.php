<?php

namespace App\Controllers;

use Core\Controller;

/**
 * Authenticated base controller
 */
abstract class Authenticated extends Controller
{
    /**
     * Require the user to be authenticated before giving access to all methods in the controller
     */
    protected function before(): void
    {
        $this->requireLogin();
    }
}
