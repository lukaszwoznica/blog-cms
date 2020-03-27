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
        $total_posts = Post::getTotal(false);
        $total_categories = Category::getTotal();
        $total_users = User::getTotal();
        $total_comments = Comment::getTotal();

        View::renderTemplate('Admin/Dashboard/index.html', [
            'total_posts' => $total_posts,
            'total_categories' => $total_categories,
            'total_users' => $total_users,
            'total_comments' => $total_comments
        ]);
    }
}