<?php


namespace App\Models;


use Core\Model;
use PDO;

class Comment extends Model
{
    private $id;
    private $user_id;
    private $post_id;
    private $content;
    private $submit_time;
    private $author_name;
    private $post_title;
    private $validation_errors = [];


    public function __construct(array $user_data = [])
    {
        foreach ($user_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __set($name, $value): void
    {
        switch ($name) {
            case 'username':
                if (empty($this->author_name)) {
                    $this->author_name = $value;
                }
                break;
            case 'title':
                if (empty($this->post_title)) {
                    $this->post_title = $value;
                }
        }
    }

    public static function getAllComments(int $limit_offset = 0, int $limit_row_num = PHP_INT_MAX, int $post_id = null): array
    {
        $db = static::getDatabase();
        $sql = "SELECT comments.*, username, title FROM comments 
                INNER JOIN users ON user_id = users.id
                INNER JOIN posts ON post_id = posts.id";
        if ($post_id) {
            $sql .= "\nWHERE post_id = :post_id";
        }
        $sql .= "\nORDER BY submit_time DESC
                LIMIT $limit_offset, $limit_row_num";

        $stmt = $db->prepare($sql);
        if ($post_id) {
            $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        }

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }

    public function save(): bool
    {
        $this->validate();

        if (empty($this->validation_errors)) {
            $db = self::getDatabase();
            $sql = 'INSERT INTO comments (user_id, post_id, content)
                    VALUES (:user_id, :post_id, :content)';

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':post_id', $this->post_id, PDO::PARAM_INT);
            $stmt->bindValue(':content', trim($this->content), PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function validate(): void
    {
        if (strlen(trim($this->content)) < 1 || strlen(trim($this->content)) > 2000) {
            $this->validation_errors[] = 'Category name must be between 1 and 2000 characters';
        }
        if (!User::findByID($this->user_id)) {
            $this->validation_errors[] = 'Invalid user identifier';
        }
        if (!Post::findByID($this->post_id)) {
            $this->validation_errors[] = 'Invalid post identifier';
        }
    }

    public static function findById(int $id): ?Comment
    {
        $db = static::getDatabase();
        $sql = 'SELECT * FROM comments WHERE id = :id';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $result = $stmt->fetch();
        if (!$result)
            return null;
        return $result;
    }

    public static function getTotal(): int
    {
        $db = static::getDatabase();
        $sql = "SELECT COUNT(*) FROM comments";
        $stmt = $db->query($sql);

        return $stmt->fetchColumn();
    }

    public function update(array $comment_data): bool
    {
        $this->content = trim($comment_data['content']);

        $this->validate();

        if (empty($this->validation_errors)) {
            $db = static::getDatabase();
            $sql = 'UPDATE comments
                    SET content = :content
                    WHERE id = :id';

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;
    }

    public function delete(): bool
    {
        $db = static::getDatabase();
        $sql = 'DELETE FROM comments WHERE id = :id';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id);

        return $stmt->execute();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getPostId(): ?int
    {
        return $this->post_id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getSubmitTime(): ?string
    {
        return $this->submit_time;
    }

    public function getAuthorName(): ?string
    {
        return $this->author_name;
    }

    public function getPostTitle(): ?string
    {
        return $this->post_title;
    }
}