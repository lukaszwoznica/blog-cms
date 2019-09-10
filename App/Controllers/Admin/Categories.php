<?php


namespace App\Controllers\Admin;


use App\Flash;
use App\Models\Category;
use Core\View;

class Categories extends Admin
{
    private $id;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);
        $this->id = $route_params['id'] ?? null;
    }

    public function indexAction(): void
    {
        $categories = Category::getAllCategories();

        View::renderTemplate('Admin/Categories/index.html', [
            'categories' => $categories
        ]);
    }

    public function newAction(): void
    {
        View::renderTemplate('Admin/Categories/new.html');
    }

    public function createAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $category = new Category($_POST);

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
        $category = $this->getCategoryById($this->id);

        if($category){
            if ($category->delete()) {
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
        $category = $this->getCategoryById($this->id);

        if ($category){
            View::renderTemplate('Admin/Categories/edit.html', [
                'category' => $category
            ]);
        } else {
            Flash::addMessage('Category with given id does not exist', Flash::WARNING);
            $this->redirectTo('/admin/categories');
        }

    }

    public function updateAction(): void
    {
        $category = $this->getCategoryById($this->id);

        if ($category && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($category->update($_POST)) {
                Flash::addMessage('Category has been successfully updated', Flash::SUCCESS);
                $this->redirectTo('/admin/categories');
            } else {
                View::renderTemplate('Admin/Categories/edit.html', [
                    'category' => $category
                ]);
            }
        } else {
            $this->redirectTo('/admin/categories');
        }
    }

    private function getCategoryById(?int $id): ?Category
    {
        if ($id === null){
            return null;
        }
        return Category::findByID($id);
    }
}