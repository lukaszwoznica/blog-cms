<?php


namespace App\Controllers\Admin;


use App\Flash;
use App\Models\Comment;
use App\Paginator;
use Core\View;

class Comments extends Admin
{
    private $page;
    private $comment;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        if (isset($route_params['id'])) {
            $this->comment = Comment::findById($route_params['id']);
        }

        if (isset($route_params['page'])) {
            $this->page = (int)$route_params['page'];
        } else {
            $this->page = 1;
        }
    }

    public function indexAction(): void
    {
        $context_data = [];

        if (isset($_GET['search_query'])) {
            $comments = Comment::getAllCommentsContainsFilter($_GET['search_query']);
            $context_data['search_query'] = $_GET['search_query'];
        } else {
            $paginator = new Paginator($this->page, 10, Comment::getTotal());
            $comments = Comment::getAllComments($paginator->getOffset(), $paginator->getLimit());
            $context_data['page'] = $this->page;
            $context_data['total_pages'] = $paginator->getTotalPages();
        }
        $context_data['comments'] = $comments;

        View::renderTemplate('Admin/Comments/index.html', $context_data);
    }

    public function editAction(): void
    {
        if ($this->comment) {
            View::renderTemplate('Admin/Comments/edit.html', [
                'comment' => $this->comment
            ]);
        } else {
            Flash::addMessage('Comment with given id does not exist', Flash::WARNING);
            $this->redirectTo('/admin/comments');
        }
    }

    public function updateAction(): void
    {
        if ($this->comment && $_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->comment->update($_POST)) {
                Flash::addMessage('Comment has been successfully updated', Flash::SUCCESS);
                $this->redirectTo('/admin/comments');
            } else {
                View::renderTemplate('Admin/Comments/edit.html', [
                    'category' => $this->comment
                ]);
            }
        } else {
            $this->redirectTo('/admin/comments');
        }
    }

    public function destroyAction(): void
    {
        if($this->comment){
            if ($this->comment->delete()) {
                Flash::addMessage('Comment has been successfully deleted', Flash::SUCCESS);
            } else {
                Flash::addMessage('An error occurred while deleting the comment', Flash::ERROR);
            }
        } else {
            Flash::addMessage('Comment with given id does not exist', Flash::WARNING);
        }

        $this->redirectTo('/admin/comments');
    }
}