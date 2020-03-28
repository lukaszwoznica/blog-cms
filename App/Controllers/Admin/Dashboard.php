<?php


namespace App\Controllers\Admin;


use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Core\View;

class Dashboard extends Admin
{
    public function indexAction(): void
    {
        View::renderTemplate('Admin/Dashboard/index.html', [
            'total_posts' => Post::getTotal(false),
            'total_categories' => Category::getTotal(),
            'total_users' => User::getTotal(),
            'total_comments' => Comment::getTotal(),
            'posts_by_category' => Post::countPostsByCategory(),
            'comments_by_post' => Comment::countCommentsByPost(5)
        ]);
    }
}