<?php


namespace App\Models;


use Core\Model;
use PDO;

class Settings extends Model
{
    private $blog_name;
    private $blog_description;
    private $footer_text;
    private $facebook_profile;
    private $instagram_profile;
    private $twitter_profile;
    private $youtube_profile;
    private $validation_errors = [];

    public function __construct()
    {
        $db = static::getDatabase();
        $sql = 'SELECT * FROM settings';

        $stmt = $db->query($sql);
        $stmt->setFetchMode(PDO::FETCH_KEY_PAIR);
        $data = $stmt->fetchAll();

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function update(array $data): bool
    {
        $db = static::getDatabase();
        $sql = '';
        $attributes = get_object_vars($this);

        foreach ($attributes as $name => $value) {
            if ($name != 'validation_errors') {
                $this->$name = $data[$name] ?? null;
                $sql .= "UPDATE settings SET value = :$name WHERE name = '$name';\n";
            }
        }

        $this->validate();

        if (empty($this->validation_errors)) {
            $stmt = $db->prepare($sql);
            foreach ($attributes as $name => $value) {
                if ($name != 'validation_errors') {
                    $stmt->bindValue(":$name", trim($this->$name), PDO::PARAM_STR);
                }
            }

            return $stmt->execute();
        }

        return false;
    }

    public function validate()
    {
        if (strlen(trim($this->blog_name)) < 1 || strlen(trim($this->blog_name)) > 50) {
            $this->validation_errors[] = "Blog name must be between 1 and 50 characters";
        }
        if (strlen(trim($this->blog_description)) < 1 || strlen(trim($this->blog_description)) > 100) {
            $this->validation_errors[] = "Blog description must be between 1 and 50 characters";
        }
        if (strlen(trim($this->footer_text)) < 1 || strlen(trim($this->footer_text)) > 100) {
            $this->validation_errors[] = "Footer text must be between 1 and 50 characters";
        }
        if (!is_null($this->facebook_profile) && strlen(trim($this->facebook_profile)) < 1 ||
            strlen(trim($this->facebook_profile)) > 255) {
            $this->validation_errors[] = "Facebook profile name must be between 1 and 255 characters";
        }
        if (!is_null($this->instagram_profile) && strlen(trim($this->instagram_profile)) < 1 ||
            strlen(trim($this->instagram_profile)) > 255) {
            $this->validation_errors[] = "Instagram profile name must be between 1 and 255 characters";
        }
        if (!is_null($this->twitter_profile) && strlen(trim($this->twitter_profile)) < 1 ||
            strlen(trim($this->twitter_profile)) > 255) {
            $this->validation_errors[] = "Twitter profile name must be between 1 and 255 characters";
        }
        if (!is_null($this->youtube_profile) && strlen(trim($this->youtube_profile)) < 1 ||
            strlen(trim($this->youtube_profile)) > 255) {
            $this->validation_errors[] = "YouTube channel name must be between 1 and 255 characters";
        }
    }

    public function getBlogName(): ?string
    {
        return $this->blog_name;
    }

    public function getBlogDescription(): ?string
    {
        return $this->blog_description;
    }

    public function getFooterText(): ?string
    {
        return $this->footer_text;
    }

    public function getFacebookProfile(): ?string
    {
        return $this->facebook_profile;
    }

    public function getInstagramProfile(): ?string
    {
        return $this->instagram_profile;
    }

    public function getTwitterProfile(): ?string
    {
        return $this->twitter_profile;
    }

    public function getYoutubeProfile(): ?string
    {
        return $this->youtube_profile;
    }

    public function getValidationErrors(): array
    {
        return $this->validation_errors;
    }
}