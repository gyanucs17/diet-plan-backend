<?php 

namespace App\Controller;

use App\Service\FoodService;
use App\Model\Food;
use App\Lib\Helper;
use Exception;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 

class FoodController
{
    private FoodService $foodService;
    private Helper $helper;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->foodService = new FoodService();
        $this->helper = new Helper();
        $this->logger = new Logger('FoodLogger', __DIR__ . '/../../logs/food.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * addFood - Add a new food item.
     *
     * @param  array $req Post request input data
     * @return array Response 
     */
    public function addFood(array $req): array {
        $data = $this->helper->validateInsertUpdateFoodParams($req); // Validation for required params
        if (isset($data['status']) && $data['status'] === 'failed') {
            return $data;
        }

        try {
            // Create Food model
            $food = new Food((array)$req);
            $resp = $this->foodService->saveFood($food);
            return $this->helper->respond($resp, 'Food Saved', 'Food not Saved');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not saved');
        }
    }
    
    /**
     * updateFood - Update an existing food item.
     *
     * @param  array $req 
     * @return array Response 
     */
    public function updateFood(array $req): array {
        $data = $this->helper->validateInsertUpdateFoodParams($req['food']);
        if (isset($data['status']) && $data['status'] === 'failed') {
            return $data;
        }

        $food = (array)$req['food'];
        $food['created_by'] = $req['created_by'];
        
        try {
            // Create Food model
            $foodModel = new Food($food);
            $resp = $this->foodService->updateFood($foodModel);
            return $this->helper->respond($resp, 'Food Updated', 'Food not Updated');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not updated');
        }
    }

    /**
     * getFoodByid - Retrieve a food item by its ID.
     *
     * @param  array $req 
     * @return array Response 
     */
    public function getFoodByid(array $req): array {
        try {
            $foodId = $req['id'];
            if (!isset($foodId)) {
                return ['status' => 'failed', 'error' => 'FoodId required'];
            }

            // Getting food
            $food = $this->foodService->getFoodByid($foodId);
            return $this->helper->respondList($food->jsonSerialize(), 'No data found');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data Found');
        }
    }
    
    /**
     * getFoodList - Retrieve a list of food items.
     *
     * @param  array $req 
     * @return array Response 
     */
    public function getFoodList(array $req): array {
        try {
            // Getting food data
            $foods = $this->foodService->getFoodList($req['created_by']);
            if (!$foods) {
                return ['status' => 'failed', 'msg' => 'No data found'];
            }
            $foods = array_map(fn($food) => $food->jsonSerialize(), $foods); // Serialize the food data to array
            return $this->helper->respondList($foods, 'No data found');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data Found');
        }
    }

    /**
     * searchFood - Search for food items based on a query.
     *
     * @param  array $req 
     * @return array Response 
     */
    public function searchFood(array $req): array {
        try {
            $userId = $req['created_by'];
            $searchString = $req['search'] ?? '';
            $foods = $this->foodService->searchFood($searchString, $userId);
            $foods = array_map(fn($food) => $food->jsonSerialize(), $foods); 
            return $this->helper->respondList($foods, 'No data found');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data Found');
        }
    }

    /**
     * filterFoodByIddsiLevel - Filter food items by IDDSI level.
     *
     * @param  array $req 
     * @return array Response 
     */
    public function filterFoodByIddsiLevel(array $req): array {
        try {
            $residentId = $req['id'];
            $userId = $req['created_by'];
            $resp = $this->foodService->getFoodByIddsiLevel($residentId, $userId);
            if (isset($resp['foodData']) && count($resp['foodData']) > 0) {
                $resp['foodData'] = array_map(fn($food) => $food->jsonSerialize(), $resp['foodData']);
            }
            $data = $this->helper->parseData($resp);
            return $this->helper->respondList($data, 'No data found');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data Found');
        }
    }

    /**
     * addFoodByCSV - Add food items from a CSV file.
     *
     * @param  array $req 
     * @return array Response 
     */
    public function addFoodByCSV(array $req): array {
        try {
            $userId = $req['created_by'];
            $fileType = $_FILES['csv']['type'];
            if ($fileType !== "text/csv") {
                return ['status' => "failed", "msg" => "We accept CSV files only."];
            }

            // Parse CSV to array
            $tmpName = $_FILES['csv']['tmp_name'];
            $foodArray = array_map('str_getcsv', file($tmpName));
            array_shift($foodArray); // Remove header

            $notSaved = [];
            foreach ($foodArray as $food) {
                $isIddsiLevelNumeric = preg_match('/^-?\d+(\.\d+)?$/', $food[1]); 
                $isCategoryNumeric = preg_match('/^-?\d+(\.\d+)?$/', $food[2]); 
                
                if (isset($food[0], $food[1], $food[2]) && $isCategoryNumeric && $isIddsiLevelNumeric && $food[1] < 7) {
                    $foodData = [
                        'id' => "",
                        'name' => $food[0],
                        'category' => $food[2],
                        'iddsi_level' => $food[1],
                        'created_by' => $userId
                    ];
                    $foodModel = new Food($foodData);
                    
                    try {
                        $this->foodService->saveFood($foodModel);
                    } catch (Exception $e) {
                        $notSaved[] = $foodData; // Not saved data
                    }
                } else {
                    $notSaved[] = [
                        'name' => $food[0],
                        'iddsi_level' => $food[1],
                        'category' => $food[2]
                    ]; // Not saved data
                }
            }

            return [
                'status' => "success", 
                "msg" => "CSV data inserted successfully", 
                "NotSavedData" => $notSaved
            ];

        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'CSV upload failed');
        }
    }
}
