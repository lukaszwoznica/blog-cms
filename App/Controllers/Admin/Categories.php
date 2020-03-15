<?php


namespace App\Controllers\Admin;



use App\Flash;
use App\Models\Category;
use App\TreePreorderTraversal;
use Core\View;

class Categories extends Admin
{
    private $category;
    private $categories_tree;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        $this->categories_tree = Category::getTree();

        if (isset($route_params['id'])) {
            $this->category = Category::findByID($route_params['id']);
        }
    }

    public function indexAction(): void
    {
        View::renderTemplate('Admin/Categories/index.html', [
            'categories' => $this->categories_tree
        ]);
    }

    public function newAction(): void
    {
        View::renderTemplate('Admin/Categories/new.html', [
            'categories' => $this->categories_tree
        ]);
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
                    'category' => $category,
                    'categories' => $this->categories_tree
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
                'category' => $this->category,
                'categories' => $this->categories_tree
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
                    'category' => $this->category,
                    'categories' => $this->categories_tree
                ]);
            }
        } else {
            $this->redirectTo('/admin/categories');
        }
    }

}