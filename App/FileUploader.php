<?php


namespace App;


use finfo;

class FileUploader
{
    private $upload_dir;
    private $max_file_size;
    private $allowed_mime_types;
    private $validation_errors = [];

    public function __construct(string $upload_dir, int $max_file_size, array $allowed_mime_types)
    {
        $this->upload_dir = dirname(__DIR__) . $upload_dir;
        $this->max_file_size = $max_file_size;
        $this->allowed_mime_types = $allowed_mime_types;
    }

    public function upload(array $file, string $filename): bool
    {
        $this->validate($file);

        if (empty($this->validation_errors)) {
            $target = $this->upload_dir . '/' . $filename;
            if (!file_exists($this->upload_dir)) {
                mkdir($this->upload_dir, 0777, true);
            }
            if (move_uploaded_file($file['tmp_name'], $target)) {
                return true;
            }
        }

        return false;
    }

    private function validate(array $file): void
    {
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                $finfo = new finfo(FILEINFO_MIME_TYPE);

                if (!in_array($finfo->file($file['tmp_name']), $this->allowed_mime_types)) {
                    $this->validation_errors[] = 'The type of uploaded file is not allowed';
                } else if ($file['size'] > $this->max_file_size) {
                    $this->validation_errors[] = 'The uploaded file is too large.';
                }
                break;

            case UPLOAD_ERR_FORM_SIZE:
            case UPLOAD_ERR_INI_SIZE:
                $this->validation_errors[] = 'The uploaded file is too large.';
                break;

            case UPLOAD_ERR_NO_FILE:
                $this->validation_errors[] = 'No file was uploaded';
                break;

            default:
                $this->validation_errors[] = 'An error occurred while uploading the file';
                break;
        }
    }

    public function getValidationErrors(): array
    {
        return $this->validation_errors;
    }

    public function getUploadDir(): string
    {
        return $this->upload_dir;
    }

}