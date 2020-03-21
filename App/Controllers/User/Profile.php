<?php


namespace App\Controllers\User;

use App\Auth;
use App\Controllers\Authenticated;
use App\FileUploader;
use App\Flash;
use Core\View;
use Gumlet\ImageResize;
use Gumlet\ImageResizeException;

class Profile extends Authenticated
{
    public function showAction(): void
    {
        $this->requireLogin();
        View::renderTemplate('User/Profile/show.html');
    }

    public function editAction(): void
    {
        View::renderTemplate('User/Profile/edit.html');
    }

    public function updateAction(): void
    {
        $user = Auth::getUser();

        if ($user && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $post_data = $_POST;
            if (is_uploaded_file($_FILES['avatar']['tmp_name'])) {
                $file = $_FILES['avatar'];
                $allowed_types = ['image/gif', 'image/png', 'image/jpeg'];
                $upload_path = '/public/uploads/users-avatars/';
                $file_uploader = new FileUploader($upload_path, 204800, $allowed_types);
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = $post_data['username'] . '.' . $file_extension;

                if ($file_uploader->upload($file, $filename)) {
                    try {
                        $full_patch = $file_uploader->getUploadDir() . $filename;
                        $image = new ImageResize($full_patch);
                        $image->resize(300, 300)->save($full_patch);
                        $post_data['avatar'] = '/uploads/users-avatars/' . $filename;
                    } catch (ImageResizeException $e) {
                        echo $e->getMessage();
                    }
                } else {
                    $img_upload_errors = $file_uploader->getValidationErrors();
                }
            } else {
                if ($post_data['username'] != $user->getUsername() && $user->getAvatar() != null) {
                    $img_path = dirname(__DIR__, 3) . '/public' . $user->getAvatar();
                    if (file_exists($img_path)) {
                        $extension = pathinfo($img_path, PATHINFO_EXTENSION);
                        $new_filename = $post_data['username'] . '.' . $extension;
                        $new_path = dirname(__DIR__, 3) . '/public/uploads/users-avatars/' . $new_filename;
                        rename($img_path, $new_path);
                        $post_data['avatar'] = '/uploads/users-avatars/' . $new_filename;
                    }
                } else {
                    $post_data['avatar'] = $user->getAvatar();
                }
            }

            if (empty($img_upload_errors) && $user->updateProfile($post_data)) {
                Flash::addMessage('Changes saved successfully.', Flash::SUCCESS);
                $this->redirectTo('/user/profile/show');
            } else {
                if (!empty($img_upload_errors)) {
                    $user->setValidationErrors([...$user->getValidationErrors(), ...$img_upload_errors]);
                }

                View::renderTemplate('User/Profile/edit.html', [
                    'errors' => $user->getValidationErrors()
                ]);
            }
        } else {
            $this->redirectTo('/user/profile/show');
        }
    }
}