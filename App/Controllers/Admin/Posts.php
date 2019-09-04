<?php


namespace App\Controllers\Admin;


use App\Models\Post;
use Core\View;

class Posts extends Admin
{
    public function indexAction(): void
    {
        $posts = Post::getAllPosts();

//        View::renderTemplate('Admin/Posts/index.html', [
//            'posts' => $posts
//        ]);
    }

    public function newAction(): void
    {
//        View::renderTemplate('Admin/Posts/new.html');
    }

    public function createAction(): void
    {

    }
}