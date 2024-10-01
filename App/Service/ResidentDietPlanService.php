<?php

namespace App\Service;

use App\Repository\FoodRepository;
use App\Repository\ResidentDietPlanRepository;
use App\Repository\ResidentRepository;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 
use Exception;

class ResidentDietPlanService
{
    //----------------------code--------------------------//
    private FoodRepository $foodRepository;
    private ResidentDietPlanRepository $residentDietPlanRepository;
    private ResidentRepository $residentRepository;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->foodRepository = new FoodRepository();
        $this->residentDietPlanRepository = new ResidentDietPlanRepository();
        $this->residentRepository = new ResidentRepository();
        $this->logger = new Logger('FoodLogger', __DIR__ . '/../../logs/food.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * assignFoodDiet - Assign food to a resident's diet plan.
     *
     * @param  ResidentDietPlan $residentDietPlan 
     * @return bool|array 
     */
    public function assignFoodDiet($residentDietPlan): bool|array {
        try {
            return $this->residentDietPlanRepository->insertDietPlan($residentDietPlan);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not assigned');
        }
    }

    /**
     * getDietPlan - Retrieve the diet plan for a specific resident.
     *
     * @param  int $residentId 
     * @return ResidentDietPlan[]|array 
     */
    public function getDietPlan($residentId): array {
        try {
            return $this->residentDietPlanRepository->fetchDietPlanByResidentId($residentId);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * unAssignFoodDiet - Unassign food from a resident's diet plan.
     *
     * @param  ResidentDietPlan $residentDietPlan 
     * @return bool|array 
     */
    public function unAssignFoodDiet($residentDietPlan): bool|array {
        try {
            return $this->residentDietPlanRepository->removeFoodFromDietPlan($residentDietPlan);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not unassigned');
        }
    }

    /**
     * searchDietFoodBySearchString - Search for food by IDDSI level and search string.
     *
     * @param  int $residentId 
     * @param  string $searchString 
     * @return array 
     */
    public function searchDietFoodBySearchString($residentId, $searchString): array {
        try {
            $residentData = $this->residentRepository->fetchResidentByid($residentId);
            $foodData = $this->foodRepository->fetchFoodByIddsiLevelSearchString($residentData->getIddsiLevel(), $searchString);
            $dietPlan = $this->residentDietPlanRepository->fetchDietPlanByResidentId($residentId);
            $data = ["status" => "success"];
            $data['foodData'] = $foodData;
            $data['assignData'] = $dietPlan;
            return $data;

        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }
}