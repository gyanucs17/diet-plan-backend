<?php

namespace App\Service;

use App\Repository\FoodRepository;
use App\Repository\ResidentDietPlanRepository;
use App\Repository\ResidentRepository;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 
use Exception;

class FoodService
{
    //-----------------------code--------------------------//
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
     * saveFood - Save a new food item to the repository.
     *
     * @param  Food $food 
     * @return bool|array 
     */
    public function saveFood($food): bool|array {
        try {
            return $this->foodRepository->insertFood($food);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not saved');
        }
    }

    /**
     * updateFood - Update an existing food item in the repository.
     *
     * @param  Food $food 
     * @return bool|array 
     */
    public function updateFood($food): bool|array {
        try {
            return $this->foodRepository->updateFood($food);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not updated');
        }
    }

    /**
     * getFoodByid - Retrieve food item by ID.
     *
     * @param  int $foodId 
     * @return Food|array 
     */
    public function getFoodByid($foodId): bool|array {
        try {
            return $this->foodRepository->fetchFoodByid($foodId);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * getFoodList - Retrieve a list of food items for a specific user.
     *
     * @param  int $userId 
     * @return Food[]|array 
     */
    public function getFoodList($userId): array {
        try {
            return $this->foodRepository->fetchFoodlist($userId);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * searchFood - Search for food items based on a query.
     *
     * @param  string $searchQuery 
     * @param  int $userId 
     * @return bool|array 
     */
    public function searchFood($searchQuery, $userId): array {
        try {
            return $this->foodRepository->fetchFoodByQuery($searchQuery, $userId);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * getFoodByIddsiLevel - Retrieve food items by the resident's IDDSI level.
     *
     * @param  int $residentId 
     * @param  int $userId 
     * @return array 
     */
    public function getFoodByIddsiLevel($residentId, $userId): array {
        try {
            // Getting IDDSI level of the resident
            $residentData = $this->residentRepository->fetchResidentByid($residentId);
            // Getting food data by IDDSI level
            $foodData = $this->foodRepository->fetchFoodByIddsiLevel($residentData->getIddsiLevel(), $userId);
            // Getting diet plan of the resident
            $dietPlan = $this->residentDietPlanRepository->fetchDietPlanByResidentId($residentId);
            $resp = ["status" => "success"];
            $resp['foodData'] = $foodData;
            $resp['assignData'] = $dietPlan;
            return $resp;

        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }
}