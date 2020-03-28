<?php


namespace App\Controllers\Admin;


use App\Flash;
use Core\View;

class Settings extends Admin
{
    public function indexAction(): void
    {
        View::renderTemplate('Admin/Settings/index.html');
    }

    public function updateAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $settings = new \App\Models\Settings();

            if ($settings->update($_POST)) {
                Flash::addMessage('Settings have been successfully saved', Flash::SUCCESS);
                $this->redirectTo('/admin/settings');
            } else {
                View::renderTemplate('Admin/Settings/index.html', [
                    'errors' => $settings->getValidationErrors()
                ]);
            }
        } else {
            $this->redirectTo('/admin/settings');
        }
    }
}