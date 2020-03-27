<?php


namespace App\Models;

use Core\Model;
use PDO;

class Category extends Model
{
    private $id;
    private $name;
    private $description;
    private $parent_id;
    private $url_slug;
    private $subcategories = [];
    private $validation_errors = [];
    private $descendant_subcategories = [];
    private $level;

    public function __construct(array $category_data = [])
    {
        foreach ($category_data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function saveToDatabase(): bool
    {
        $this->validate();

        if (empty($this->validation_errors)) {
            $db = self::getDatabase();
            $sql = 'INSERT INTO categories (name, description, parent_id, url_slug)
                VALUES (:name, :description, :parent_id, :url_slug)';

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':name', trim($this->name), PDO::PARAM_STR);
            $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
            $stmt->bindValue(':parent_id', $this->parent_id, PDO::PARAM_INT);
            $stmt->bindValue(':url_slug', $this->url_slug, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function validate(): void
    {
        if (strlen(trim($this->name)) < 3 || strlen(trim($this->name)) > 100) {
            $this->validation_errors[] = 'Category name must be between 3 and 100 characters';
        }

        if (strlen($this->url_slug) < 3 || strlen($this->url_slug) > 255) {
            $this->validation_errors[] = 'Slug must be between 3 and 25 characters';
        }
        if (preg_match("/^[a-z0-9-]+$/", $this->url_slug) === 0) {
            $this->validation_errors[] = 'Slug can only contain alphanumeric characters (lowercase letters a-z, numbers 0-9) and dash';
        }
        if (static::slugExist($this->url_slug, $this->id ?? null)) {
            $this->validation_errors[] = 'URL slug is already taken';
        }

        if (strlen($this->description) > 255) {
            $this->validation_errors[] = 'Category description cannot be longer than 255 characters';
        }
    }

    public static function getAllCategories(): array
    {
        $db = static::getDatabase();
        $sql = 'SELECT * FROM categories';

        $stmt = $db->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $result = $stmt->fetchAll();

        return $result;
    }

    public static function findByID(int $category_id): ?Category
    {
        $db = static::getDatabase();
        $sql = 'SELECT * FROM categories WHERE id = :category_id';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":category_id", $category_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $result = $stmt->fetch();
        if (!$result)
            return null;
        return $result;
    }

    public function delete(): bool
    {
        $db = static::getDatabase();
        $sql = 'DELETE FROM categories WHERE id = :id';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id);

        return $stmt->execute();
    }

    public function update(array $category_data): bool
    {
        $this->name = trim($category_data['name']);
        $this->description = $category_data['description'];
        $this->parent_id = $category_data['parent_id'];

        $this->validate();

        if (empty($this->validation_errors)) {
            $db = static::getDatabase();
            $sql = 'UPDATE categories
                    SET name = :name,
                    description = :description,
                    parent_id = :parent_id
                    WHERE id = :id';

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':parent_id', $this->parent_id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        return false;
    }

    public static function getTree(): array
    {
        $children = [];
        $categories = static::getAllCategories();

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $children[$category->getParentId()][] = $category;
            }

            foreach ($categories as $category) {
                if (isset($children[$category->getId()])) {
                    $category->setSubcategories($children[$category->getId()]);
                }
            }

            return $children[null];
        }

        return [];
    }

    public function getAllDescendantSubcategories()
    {
        if (empty($this->descendant_subcategories)) {
            $this->preorderTraversal($this, 0);
        }

        return $this->descendant_subcategories;
    }

    public static function findBySlug(string $slug): ?Category
    {
        $db = static::getDatabase();
        $sql = 'SELECT * FROM categories WHERE url_slug = :url_slug';

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":url_slug", $slug, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();

        $result = $stmt->fetch();
        if (!$result)
            return null;
        return $result;
    }

    public static function slugExist(string $slug, int $ignore_id = null): bool
    {
        $category = static::findBySlug($slug);
        if ($category && $category->id != $ignore_id) {
            return true;
        }
        return false;
    }

    public static function getTotal(): int
    {
        $db = static::getDatabase();
        $sql = "SELECT COUNT(*) FROM categories";
        $stmt = $db->query($sql);

        return $stmt->fetchColumn();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getValidationErrors(): array
    {
        return $this->validation_errors;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function getSubcategories(): array
    {
        return $this->subcategories;
    }

    public function setSubcategories(array $subcategories): void
    {
        $this->subcategories = $subcategories;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel($level): void
    {
        $this->level = $level;
    }

    public function getUrlSlug(): ?string
    {
        return $this->url_slug;
    }

    private function preorderTraversal(Category $node, int $level): void
    {
        foreach ($node->getSubcategories() as $subcategory) {
            $subcategory->setLevel($level);
            $this->descendant_subcategories[] = $subcategory;
            $this->preorderTraversal($subcategory, $level + 1);
        }
    }

}