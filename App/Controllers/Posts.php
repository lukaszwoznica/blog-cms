<?php


namespace App\Controllers;


use App\Models\Post;
use App\Paginator;
use Core\Controller;
use Core\View;
use Exception;

class Posts extends Controller
{
    private $post;
    private $page;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        if (isset($route_params['id'])) {
            $this->post = Post::findByID($route_params['id']);
        } elseif (isset($route_params['slug'])) {
            $this->post = Post::findBySlug($route_params['slug']);
        }

        if (isset($route_params['page'])) {
            $this->page = (int)$route_params['page'];
        } else {
            $this->page = 1;
        }

    }

    public function indexAction(): void
    {
        $paginator = new Paginator($this->page, 12, Post::getTotal());

        $posts = Post::getAllPosts($paginator->getOffset(), $paginator->getLimit());

        View::renderTemplate('Posts/index.html', [
            'posts' => $posts,
            'page' => $this->page,
            'total_pages' => $paginator->getTotalPages()
        ]);
    }

    public function showAction(): void
    {
        if ($this->post) {
            View::renderTemplate('Posts/show.html', [
                'post' => $this->post
            ]);
        } else {
            throw new Exception("Post with given id or slug does not exist", 404);
        }
    }
}