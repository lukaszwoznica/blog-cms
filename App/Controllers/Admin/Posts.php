<?php


namespace App\Controllers\Admin;


use App\Auth;
use App\FileUploader;
use App\Flash;
use App\Models\Category;
use App\Models\Post;
use Core\View;

class Posts extends Admin
{
    private $post;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        if (isset($route_params['id'])) {
            $this->post = Post::findByID($route_params['id']);
        }
    }

    public function indexAction(): void
    {
        $posts = Post::getAllPosts();

        View::renderTemplate('Admin/Posts/index.html', [
            'posts' => $posts
        ]);
    }

    public function newAction(): void
    {
        $categories = Category::getAllCategories();

        View::renderTemplate('Admin/Posts/new.html', [
            'categories' => $categories
        ]);
    }

    public function createAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $author_id = Auth::getUser()->getId();
            $post_data = $_POST;
            $post_data['user_id'] = $author_id;
            if ($post_data['category_id'] == 0) {
                $post_data['category_id'] = null;
            }
            $img_upload_errors = [];

            if (is_uploaded_file($_FILES['image']['tmp_name'])) {
                $file = $_FILES['image'];
                $allowed_types = ['image/gif', 'image/png', 'image/jpeg'];
                $upload_path = '/public/uploads/posts-images/';
                $file_uploader = new FileUploader($upload_path, 8388608, $allowed_types);
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = $post_data['url_slug'] . '.' . $file_extension;

                if ($file_uploader->upload($file, $filename)) {
                    $post_data['image'] = '/uploads/posts-images/' . $filename;
                } else {
                    $img_upload_errors = $file_uploader->getValidationErrors();
                }
            } else {
                $post_data['image'] = null;
            }

            $post = new Post($post_data);

            if (empty($img_upload_errors) && $post->saveToDatabase()) {
                Flash::addMessage('Post has been successfully added', Flash::SUCCESS);
                $this->redirectTo('/admin/posts');
            } else {
                if (!empty($img_upload_errors)) {
                    $post->setValidationErrors(array_merge($post->getValidationErrors(), $img_upload_errors));
                }
                $categories = Category::getAllCategories();
                View::renderTemplate('Admin/Posts/new.html', [
                    'post' => $post,
                    'categories' => $categories
                ]);
            }
        } else {
            $this->redirectTo('/admin/posts');
        }
    }

    public function editAction(): void
    {
        $categories = Category::getAllCategories();

        if ($this->post) {
            View::renderTemplate('Admin/Posts/edit.html', [
                'post' => $this->post,
                'categories' => $categories
            ]);
        } else {
            Flash::addMessage('Post with given id does not exist', Flash::WARNING);
            $this->redirectTo('/admin/posts');
        }
    }

    public function updateAction(): void
    {
        if ($this->post && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_data = $_POST;
            if ($post_data['category_id'] == 0) {
                $post_data['category_id'] = null;
            }

            if ($this->post->update($post_data)){
                Flash::addMessage('Post has been successfully updated', Flash::SUCCESS);
                $this->redirectTo('/admin/posts');
            } else {
                $categories = Category::getAllCategories();

                View::renderTemplate('Admin/Posts/edit.html', [
                    'post' => $this->post,
                    'categories' => $categories
                ]);
            }
        } else {
            $this->redirectTo('/admin/posts');
        }
    }

    public function destroyAction(): void
    {
        if ($this->post) {
            if ($this->post->delete()) {
                Flash::addMessage('Post has been successfully deleted', Flash::SUCCESS);
            } else {
                Flash::addMessage('An error occurred while deleting the post', Flash::ERROR);
            }
        } else {
            Flash::addMessage('Post with given id does not exist', Flash::WARNING);
        }

        $this->redirectTo('/admin/posts');
    }

    /**
     * Check if slug is available (AJAX)
     */
    public function validateSlugAction(): void
    {
        if (isset($_GET['url_slug']) && !empty($_GET['url_slug'])) {
            $slug_valid = !Post::slugExist($_GET['url_slug'], $_GET['ignore_id'] ?? null);
            header('Content-Type: application/json');
            echo json_encode($slug_valid);
        }
    }

}