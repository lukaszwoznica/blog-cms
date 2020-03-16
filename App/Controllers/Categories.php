<?php


namespace App\Controllers;


use App\Models\Category;
use App\Models\Post;
use App\Paginator;
use Core\Controller;
use Core\View;
use Exception;

class Categories extends Controller
{
    private $category;
    private $page;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        if (isset($route_params['id'])) {
            $this->category = Category::findByID($route_params['id']);
        }

        if (isset($route_params['page'])) {
            $this->page = (int)$route_params['page'];
        } else {
            $this->page = 1;
        }
    }

    public function showAction(): void
    {
        if ($this->category) {
            $categories_list = [];
            $categories_ids = [];
            $categories_tree = Category::getTree();

            foreach ($categories_tree as $category_node) {
                $categories_list[] = $category_node;
                $categories_list = [...$categories_list, ...$category_node->getAllDescendantSubcategories()];
            }

            foreach ($categories_list as $category) {
                if ($this->category->getId() == $category->getId()) {
                    $categories_ids[] = $category->getId();
                    foreach ($category->getAllDescendantSubcategories() as $subcategory) {
                        $categories_ids[] = $subcategory->getId();
                    }
                    break;
                }
            }

            $paginator = new Paginator($this->page, 2, sizeof($categories_ids));
            $posts = Post::getAllPosts(true, $paginator->getOffset(), $paginator->getLimit(), $categories_ids);

            View::renderTemplate('Posts/index.html', [
                'posts' => $posts,
                'category_name' => $this->category->getName(),
                'page' => $this->page,
                'total_pages' => $paginator->getTotalPages(),
                'path' => '/categories/' . $this->category->getId() . '/'
            ]);
        } else {
            throw new Exception("Category with given id or slug does not exist", 404);
        }
    }
}