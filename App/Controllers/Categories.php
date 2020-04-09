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
        } elseif (isset($route_params['slug'])) {
            $this->category = Category::findBySlug($route_params['slug']);
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

            $total_records = sizeof(Post::getAllPosts(false, 0, PHP_INT_MAX, $categories_ids));
            $paginator = new Paginator($this->page, 10, $total_records);
            $posts = Post::getAllPosts(false, $paginator->getOffset(), $paginator->getLimit(), $categories_ids);

            View::renderTemplate('Posts/index.html', [
                'posts' => $posts,
                'category_name' => $this->category->getName(),
                'page' => $this->page,
                'total_pages' => $paginator->getTotalPages(),
                'path' => '/categories/' . $this->category->getUrlSlug() . '/page/'
            ]);
        } else {
            throw new Exception("Category with given id or slug does not exist", 404);
        }
    }
}