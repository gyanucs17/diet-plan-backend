<?php

namespace App\Repository;

use App\Lib\DB;
use App\Model\ResidentDietPlan;
use PDO;
use Exception;
use App\Lib\Logger;
use App\Lib\ExceptionHandler;

class ResidentDietPlanRepository
{
    //-------------------------code---------------------------//
    private $conn = null;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->conn = DB::getInstance(); 
        $this->logger = new Logger('ResidentDietPlanLogger', __DIR__ . '/../../logs/resident_diet_plan.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * insertDietPlan - Insert a new diet plan for a resident.
     *
     * @param  ResidentDietPlan $residentDietPlan
     * @return bool|array
     */
    public function insertDietPlan($residentDietPlan): bool|array {
        try {
            $query = "INSERT INTO residentdietplans (resident_id, food_id, created_by) 
                      VALUES (:residentId, :foodId, :created_by)";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindValue(':residentId', $residentDietPlan->getResidentId());
            $stmt->bindValue(':foodId', $residentDietPlan->getFoodId());
            $stmt->bindValue(':created_by', $residentDietPlan->getCreatedBy());
    
            return $stmt->execute();
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not assigned');
        }
    }

    /**
     * fetchDietPlanByResidentId - Fetch diet plan by resident ID.
     *
     * @param  int $residentId 
     * @return ResidentDietPlan[]
     */
    public function fetchDietPlanByResidentId($residentId): array {
        try {
            $query = 'SELECT 
                        id,
                        resident_id,
                        food_id,
                        created_by
                      FROM 
                         residentdietplans
                      WHERE 
                         resident_id = :id'; 
        
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $residentId); // Ensure the type is specified
            $stmt->execute();
        
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = new ResidentDietPlan($row);
            }
            return $data;
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * removeFoodFromDietPlan - Remove a food item from the resident's diet plan.
     *
     * @param  ResidentDietPlan $residentDietPlan 
     * @return bool|array
     */
    public function removeFoodFromDietPlan($residentDietPlan): bool|array {
        try {
            $query = "DELETE FROM residentdietplans WHERE food_id = :foodId AND resident_id = :residentId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':residentId', $residentDietPlan->getResidentId());
            $stmt->bindValue(':foodId', $residentDietPlan->getFoodId());
            return $stmt->execute();
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not unassigned');
        }
    }
}

?>