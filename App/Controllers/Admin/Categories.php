<?php


namespace App\Controllers\Admin;



use App\Flash;
use App\Models\Category;
use Core\View;

class Categories extends Admin
{
    private $category;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        if (isset($route_params['id'])) {
            $this->category = Category::findByID($route_params['id']);
        }
    }

    public function indexAction(): void
    {
        View::renderTemplate('Admin/Categories/index.html');
    }

    public function newAction(): void
    {
        View::renderTemplate('Admin/Categories/new.html');
    }

    public function createAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_data = $_POST;
            if ($post_data['parent_id'] == 0) {
                $post_data['parent_id'] = null;
            }

            $category = new Category($post_data);

            if ($category->saveToDatabase()) {
                Flash::addMessage('Category has been successfully added', Flash::SUCCESS);
                $this->redirectTo('/admin/categories');
            } else {
                View::renderTemplate('Admin/Categories/new.html', [
                    'category' => $category
                ]);
            }
        } else {
            $this->redirectTo('/admin/categories');
        }
    }

    public function destroyAction(): void
    {
        if($this->category){
            if ($this->category->delete()) {
                Flash::addMessage('Category has been successfully deleted', Flash::SUCCESS);
            } else {
                Flash::addMessage('An error occurred while deleting the category', Flash::ERROR);
            }
        } else {
            Flash::addMessage('Category with given id does not exist', Flash::WARNING);
        }

        $this->redirectTo('/admin/categories');
    }

    public function editAction(): void
    {
        if ($this->category){
            View::renderTemplate('Admin/Categories/edit.html', [
                'category' => $this->category
            ]);
        } else {
            Flash::addMessage('Category with given id does not exist', Flash::WARNING);
            $this->redirectTo('/admin/categories');
        }

    }

    public function updateAction(): void
    {
        if ($this->category && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_data = $_POST;
            if($post_data['parent_id'] == 0) {
                $post_data['parent_id'] = null;
            }

            if ($this->category->update($post_data)) {
                Flash::addMessage('Category has been successfully updated', Flash::SUCCESS);
                $this->redirectTo('/admin/categories');
            } else {
                View::renderTemplate('Admin/Categories/edit.html', [
                    'category' => $this->category
                ]);
            }
        } else {
            $this->redirectTo('/admin/categories');
        }
    }

    /**
     * Check if slug is available (AJAX)
     */
    public function validateSlugAction(): void
    {
        if (isset($_GET['url_slug']) && !empty($_GET['url_slug'])) {
            $slug_valid = !Category::slugExist($_GET['url_slug'], $_GET['ignore_id'] ?? null);
            header('Content-Type: application/json');
            echo json_encode($slug_valid);
        }
    }
}