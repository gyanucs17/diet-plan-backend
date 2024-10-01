<?php

namespace App\Repository;

use App\Lib\DB;
use PDO;
use Exception;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 

class CategoryRepository
{
    //-------------------code----------------------//
    private $conn = null;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->conn = DB::getInstance(); 
        $this->logger = new Logger('CategoryLogger', __DIR__ . '/../../logs/category.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * insertCategory - Insert a new category.
     *
     * @param  Category $category 
     * @return bool|array 
     */
    public function insertCategory($category): bool|array {
        try {
            // Query for inserting a category
            $query = "INSERT INTO category (name) VALUES (:name)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':name', $category->getname());
            return $stmt->execute();
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Category not saved');
        }
    }

    /**
     * updateCategory - Update an existing category.
     *
     * @param  Category $category 
     * @return bool|array 
     */
    public function updateCategory($category): bool|array {
        try {
            // Query for updating a category
            $query = "UPDATE category SET name = :name WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':name', $category->getname());
            $stmt->bindValue(':id', $category->getId());
            return $stmt->execute();
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Category not updated');
        }
    }

    /**
     * fetchCategorylist - Fetch a list of all categories.
     *
     * @return array 
     */
    public function fetchCategorylist(): array {
        $stmt = $this->conn->prepare("SELECT id, name FROM category");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
