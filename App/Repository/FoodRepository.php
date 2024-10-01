<?php

namespace App\Repository;

use App\Lib\DB;
use App\Model\Food;
use PDO;
use Exception;
use App\Lib\Logger;
use App\Lib\ExceptionHandler;

class FoodRepository
{
    //----------------------code------------------------//
    private $conn = null;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->conn = DB::getInstance(); 
        $this->logger = new Logger('FoodLogger', __DIR__ . '/../../logs/food.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * insertFood - Insert a new food item.
     *
     * @param  Food $food 
     * @return bool|array 
     */
    public function insertFood($food): bool|array {
        try {
            // Query for inserting food
            $query = "INSERT INTO foods (name, category, iddsi_level, created_by) VALUES (:fname, :category, :iddsi_level, :created_by)";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindValue(':fname', $food->getname());
            $stmt->bindValue(':category', $food->getCategory());
            $stmt->bindValue(':iddsi_level', $food->getIddsiLevel());
            $stmt->bindValue(':created_by', $food->getCreatedBy());
            return $stmt->execute();
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not saved');
        }
    }

    /**
     * updateFood - Update an existing food item.
     *
     * @param  Food $food 
     * @return bool|array 
     */
    public function updateFood($food): bool|array {
        try {
            // Query for updating food
            $query = "UPDATE foods SET name = :name, category = :category, iddsi_level = :iddsi_level, created_by = :created_by WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':name', $food->getname());
            $stmt->bindValue(':category', $food->getCategory());
            $stmt->bindValue(':iddsi_level', $food->getIddsiLevel());
            $stmt->bindValue(':created_by', $food->getCreatedBy());
            $stmt->bindValue(':id', $food->getId());
            return $stmt->execute();
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not updated');
        }
    }

    /**
     * fetchFoodByid - Fetch food by ID.
     *
     * @param  int $foodId 
     * @return Food[] 
     */
    public function fetchFoodByid($foodId): array {
        try {
            $query = 'SELECT 
                        foods.id,
                        foods.name AS name,
                        foods.iddsi_level AS iddsi_level,
                        category.id AS category,
                        category.name AS categoryName,
                        iddsi_levels.id AS iddsiLevel,
                        iddsi_levels.name AS iddsiLevelName,
                        foods.created_by as created_by
                      FROM 
                        foods
                      JOIN 
                        category ON foods.category = category.id
                      JOIN 
                        iddsi_levels ON foods.iddsi_level = iddsi_levels.id
                      WHERE 
                        foods.id = :id';

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $foodId);
            $stmt->execute();
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = new Food($row);
            }
            return $data;
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * fetchFoodlist - Fetch a list of foods created by a user.
     *
     * @param  int $userId 
     * @return Food[] 
     */
    public function fetchFoodlist($userId): array {   
        try {
            $query = 'SELECT 
                        foods.id,
                        foods.name AS name,
                        foods.iddsi_level AS iddsi_level,
                        category.id AS category,
                        category.name AS categoryName,
                        iddsi_levels.id AS iddsiLevel,
                        iddsi_levels.name AS iddsiLevelName,
                        foods.created_by as created_by
                    FROM 
                        foods
                    JOIN 
                        category ON foods.category = category.id
                    JOIN 
                        iddsi_levels ON foods.iddsi_level = iddsi_levels.id
                    WHERE 
                        foods.created_by = :createdBy';

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':createdBy', $userId);
            $stmt->execute();
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = new Food($row);
            }
            return $data;
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * fetchFoodByQuery - Fetch food items by search query.
     *
     * @param  string $searchQuery
     * @param  int $userId 
     * @return Food[] 
     */
    public function fetchFoodByQuery($searchQuery, $userId): array {
        try {
            $query = "SELECT 
                        foods.id,
                        foods.name AS name,
                        foods.iddsi_level AS iddsi_level,
                        category.id AS category,
                        category.name AS categoryName,
                        iddsi_levels.id AS iddsiLevel,
                        iddsi_levels.name AS iddsiLevelName,
                        foods.created_by as created_by
                    FROM 
                        foods
                    JOIN 
                        category ON foods.category = category.id
                    JOIN 
                        iddsi_levels ON foods.iddsi_level = iddsi_levels.id
                    WHERE 
                        foods.created_by = :createdBy AND foods.name LIKE :searchQuery";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':searchQuery', '%' . $searchQuery . '%');
            $stmt->bindValue(':createdBy', $userId);
            $stmt->execute();
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = new Food($row);
            }
            return $data;
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * fetchFoodByIddsiLevel - Fetch food items by iddsi level.
     *
     * @param  int $iddsiLevel 
     * @param  int $userId 
     * @return Food[] 
     */
    public function fetchFoodByIddsiLevel($iddsiLevel, $userId): array {
        try {
            $query = "SELECT 
                        foods.id,
                        foods.name AS name,
                        foods.iddsi_level AS iddsi_level,
                        category.id AS category,
                        category.name AS categoryName,
                        iddsi_levels.id AS iddsiLevel,
                        iddsi_levels.name AS iddsiLevelName,
                        foods.created_by as created_by
                    FROM 
                        foods
                    JOIN 
                        category ON foods.category = category.id
                    JOIN 
                        iddsi_levels ON foods.iddsi_level = iddsi_levels.id 
                    WHERE 
                        foods.iddsi_level <= :iddsiLevel AND foods.created_by = :userId";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':iddsiLevel', $iddsiLevel);
            $stmt->bindValue(':userId', $userId);
            $stmt->execute();
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = new Food($row);
            }
            return $data;
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * fetchFoodByIddsiLevelSearchString - Fetch food items by iddsi level and search string.
     *
     * @param  int $iddsiLevel 
     * @param  string $searchString
     * @return Food[] 
     */
    public function fetchFoodByIddsiLevelSearchString($iddsiLevel, $searchString): array {
        try {
            $query = "SELECT 
                        foods.id,
                        foods.name AS name,
                        foods.iddsi_level AS iddsi_level,
                        category.id AS category,
                        category.name AS categoryName,
                        iddsi_levels.id AS iddsiLevel,
                        iddsi_levels.name AS iddsiLevelName,
                        foods.created_by as created_by
                    FROM 
                        foods
                    JOIN 
                        category ON foods.category = category.id
                    JOIN 
                        iddsi_levels ON foods.iddsi_level = iddsi_levels.id 
                    WHERE 
                        iddsi_level <= :iddsiLevel AND foods.name LIKE :searchString";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':iddsiLevel', $iddsiLevel);
            $stmt->bindValue(':searchString', '%' . $searchString . '%');
            $stmt->execute();
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = new Food($row);
            }
            return $data;
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }
}

?>