<?php


namespace App\Controllers\Admin;


use App\Auth;
use App\FileUploader;
use App\Flash;
use App\Models\Post;
use App\Paginator;
use Core\View;

class Posts extends Admin
{
    private $post;
    private $file_uploader;
    private $page;

    public function __construct(array $route_params)
    {
        parent::__construct($route_params);

        if (isset($route_params['id'])) {
            $this->post = Post::findByID($route_params['id']);
        }
        if (isset($route_params['page'])) {
            $this->page = (int)$route_params['page'];
        } else {
            $this->page = 1;
        }
        $allowed_types = ['image/gif', 'image/png', 'image/jpeg'];
        $upload_path = '/public/uploads/posts-images/';
        $this->file_uploader = new FileUploader($upload_path, 8388608, $allowed_types);
    }

    public function indexAction(): void
    {
        $context_data = [];

        if (isset($_GET['search_query'])) {
            $posts = Post::getAllPostsContainsFilter($_GET['search_query']);
            $context_data['search_query'] = $_GET['search_query'];
        } else {
            $paginator = new Paginator($this->page, 10, Post::getTotal(false));
            $posts = Post::getAllPosts(true, $paginator->getOffset(), $paginator->getLimit());
            $context_data['page'] = $this->page;
            $context_data['total_pages'] = $paginator->getTotalPages();
        }
        $context_data['posts'] = $posts;

        View::renderTemplate('Admin/Posts/index.html', $context_data);
    }

    public function newAction(): void
    {
        View::renderTemplate('Admin/Posts/new.html');
    }

    public function createAction(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $author_id = Auth::getUser()->getId();
            $post_data = $_POST;
            $post_data['user_id'] = $author_id;
            if ($post_data['category_id'] == 0) {
                $post_data['category_id'] = null;
            }
            $img_upload_errors = [];

            if (is_uploaded_file($_FILES['image']['tmp_name'])) {
                $file = $_FILES['image'];
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = $post_data['url_slug'] . '.' . $file_extension;
                if ($this->file_uploader->upload($file, $filename)) {
                    $post_data['image'] = '/uploads/posts-images/' . $filename;
                } else {
                    $img_upload_errors = $this->file_uploader->getValidationErrors();
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

                View::renderTemplate('Admin/Posts/new.html', [
                    'post' => $post
                ]);
            }
        } else {
            $this->redirectTo('/admin/posts');
        }
    }

    public function editAction(): void
    {
        if ($this->post) {
            View::renderTemplate('Admin/Posts/edit.html', [
                'post' => $this->post
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
            $img_upload_errors = [];

            if (is_uploaded_file($_FILES['image']['tmp_name'])) {
                $file = $_FILES['image'];
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = $post_data['url_slug'] . '.' . $file_extension;
                if ($this->file_uploader->upload($file, $filename)) {
                    $post_data['image'] = '/uploads/posts-images/' . $filename;
                } else {
                    $img_upload_errors = $this->file_uploader->getValidationErrors();
                }
            } else {
                if ($post_data['url_slug'] != $this->post->getUrlSlug() && $this->post->getImage() != null) {
                    $img_path = dirname(__DIR__, 3) . '/public' . $this->post->getImage();
                    if (file_exists($img_path)) {
                        $extension = pathinfo($img_path, PATHINFO_EXTENSION);
                        $new_filename = $post_data['url_slug'] . '.' . $extension;
                        $new_path = dirname(__DIR__, 3) . '/public/uploads/posts-images/' . $new_filename;
                        rename($img_path, $new_path);
                        $post_data['image'] = '/uploads/posts-images/' . $new_filename;
                    }
                } else {
                    $post_data['image'] = $this->post->getImage();
                }
            }

            if (empty($img_upload_errors) && $this->post->update($post_data)) {
                Flash::addMessage('Post has been successfully updated', Flash::SUCCESS);
                $this->redirectTo('/admin/posts');
            } else {
                if (!empty($img_upload_errors)) {
                    $this->post->setValidationErrors(array_merge($this->post->getValidationErrors(), $img_upload_errors));
                }

                View::renderTemplate('Admin/Posts/edit.html', [
                    'post' => $this->post
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
                $img_path = dirname(__DIR__, 3) . '/public' . $this->post->getImage();
                if (file_exists($img_path)) {
                    unlink($img_path);
                }
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