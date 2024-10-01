<?php 

namespace App\Controller;

use App\Model\ResidentDietPlan;
use App\Service\ResidentDietPlanService;
use App\Lib\Helper;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 
use Exception;

class ResidentDietPlanController
{
    private Helper $helper;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;
    private ResidentDietPlanService $residentDietPlanService;

    public function __construct() {
        $this->logger = new Logger('ResidentDietPlanLogger', __DIR__ . '/../../logs/resident_diet_plan.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
        $this->residentDietPlanService = new ResidentDietPlanService();
        $this->helper = new Helper();
    }

    /**
     * assignFood - Assign food to a resident's diet plan.
     *
     * @param  array $req 
     * @return array Response 
     */
    public function assignFood(array $req): array {
        $data = $this->helper->validateFoodAssignment($req); // Validation for required params
        if (isset($data['status']) && $data['status'] === 'failed') {
            return $data;
        }

        try {
            $residentDietPlan = new ResidentDietPlan($data);
            return $this->helper->respond($this->residentDietPlanService->assignFoodDiet($residentDietPlan), 'Food assigned', 'Food not assigned');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not assigned');
        }
    }

    /**
     * getAssignedFoodList - Retrieve the list of assigned food for a resident.
     *
     * @param  int $residentId Resident ID
     * @return array Response 
     */
    public function getAssignedFoodList(int $residentId): array {
        if (empty($residentId)) {
            return $this->helper->formatErrorResponse('ResidentId required');
        }

        try {
            $resp = $this->residentDietPlanService->getDietPlan($residentId);
            return $resp ? $resp : $this->helper->formatErrorResponse('No data found');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * unAssignFood - Unassign food from a resident's diet plan.
     *
     * @param  array $req 
     * @return array Response 
     */
    public function unAssignFood(array $req): array {
        $data = $this->helper->validateFoodAssignment($req);
        if (isset($data['status']) && $data['status'] === 'failed') {
            return $data;
        }

        try {
            $residentDietPlan = new ResidentDietPlan($data);
            return $this->helper->respond($this->residentDietPlanService->unAssignFoodDiet($residentDietPlan), 'Food unassigned', 'Food not unassigned');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Food not unassigned');
        }
    }

    /**
     * filterFoodByIddsiLevelSearchQuery - Filter food by IDDSI level and search query.
     *
     * @param  array $req 
     * @return array Response 
     */
    public function filterFoodByIddsiLevelSearchQuery(array $req): array {
        try {
            $residentId = $req['id'];
            $searchString = $req['search'] ?? '';
            $resp = $this->residentDietPlanService->searchDietFoodBySearchString($residentId, $searchString);

            if (isset($resp['foodData']) && count($resp['foodData']) > 0) {
                $resp['foodData'] = array_map(fn($food) => $food->jsonSerialize(), $resp['foodData']);
            }

            return ['status' => 'success', 'data' => $this->helper->parseData($resp)];
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }
}
