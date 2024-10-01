<?php 

namespace App\Controller;

use App\Model\Resident;
use App\Service\ResidentService;
use App\Lib\Helper;
use Exception;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 

class ResidentController
{
    private ResidentService $residentService;
    private Helper $helper;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->residentService = new ResidentService();
        $this->helper = new Helper();
        $this->logger = new Logger('ResidentLogger', __DIR__ . '/../../logs/resident.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * addResident - Add a new resident.
     *
     * @param  array $req Post request input array
     * @return array Response 
     */
    public function addResident(array $req): array {
        $data = $this->helper->validateInsertUpdateResidentParams($req); // Validation for required params
        if (isset($data['status']) && $data['status'] === 'failed') {
            return $data;
        }

        try {
            // Creating Model
            $resident = new Resident((array)$req);
            $resp = $this->residentService->saveResident($resident);
            return $this->helper->respond($resp, 'Resident Saved', 'Resident not Saved');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Resident not saved');
        }
    }
    
    /**
     * updateResident - Update an existing resident.
     *
     * @param  array $req Post request input array
     * @return array Response 
     */
    public function updateResident(array $req): array {
        $resident = (array)$req;
        $resident['resident'] = (array)$resident['resident'];
        $data = $this->helper->validateInsertUpdateResidentParams($resident['resident']); // Validation for required params
        if (isset($data['status']) && $data['status'] === 'failed') {
            return $data;
        }

        $residentId = $resident['id'];
        $resident = $resident['resident'];
        $resident['created_by'] = $req['created_by'];

        try {
            // Creating Model
            $residentModel = new Resident($resident);
            $resp = $this->residentService->updateResident($residentModel);
            return $this->helper->respond($resp, 'Resident Updated', 'Resident not Updated');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Resident not updated');
        }
    }

    /**
     * getResidentByid - Retrieve a resident by ID.
     *
     * @param  array $req Post request input array
     * @return array Response 
     */
    public function getResidentByid(array $req): array {
        try {
            $residentId = $req['id'];
            if (!isset($residentId)) {
                return ['status' => 'failed', 'error' => 'ResidentId required'];
            }

            $residents = $this->residentService->getResidentByid($residentId);
            $resident = array_map(fn($resident) => $resident->jsonSerialize(), $residents);
            return $this->helper->respondList($resident, 'No data found');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data Found');
        }
    }

    /**
     * getResidentList - Retrieve a list of residents.
     *
     * @param  array $req Request input data
     * @return array Response 
     */
    public function getResidentList(array $req): array {
        try {
            $userId = $req['created_by'];
            $residents = $this->residentService->getResidentList($userId);
            if (!$residents) {
                return ['status' => 'failed', 'msg' => 'No data found'];
            }

            $residents = array_map(fn($resident) => $resident->jsonSerialize(), $residents);
            return $this->helper->respondList($residents, 'No data found');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data Found');
        }
    }

    /**
     * searchResident - Search for residents based on a query.
     *
     * @param  array $req Post request input array
     * @return array Response 
     */
    public function searchResident(array $req): array {
        try {
            $userId = $req['created_by'];
            $searchString = $req['search'] ?? "";
            $residents = $this->residentService->searchResident($searchString, $userId);
            if (!$residents) {
                return ['status' => 'failed', 'msg' => 'No data found'];
            }

            $residents = array_map(fn($resident) => $resident->jsonSerialize(), $residents);
            return $this->helper->respondList($residents, 'No data found');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data Found');
        }
    }

    /**
     * addResidentByCSV - Add residents from a CSV file.
     *
     * @param  array $req Request input data
     * @return array Response 
     */
    public function addResidentByCSV(array $req): array {
        try {
            $fileType = $_FILES['csv']['type'];
            if ($fileType !== "text/csv") {
                return ['status' => "failed", "msg" => "We accept CSV files only."];
            }

            $userId = $req['created_by'];
            $tmpName = $_FILES['csv']['tmp_name'];
            $residentArray = array_map('str_getcsv', file($tmpName));
            array_shift($residentArray); // Remove header
            $notSaved = [];

            foreach ($residentArray as $resident) {
                $isIddsiLevelNumeric = preg_match('/^-?\d+(\.\d+)?$/', $resident[1]);
                if (isset($resident[0]) && isset($resident[1]) && $isIddsiLevelNumeric && $resident[1] <= 7) {
                    $residentData = [
                        'id' => "",
                        'name' => $resident[0],
                        'iddsi_level' => $resident[1],
                        'created_by' => $userId
                    ];
                    $residentModel = new Resident($residentData);
                    
                    try {
                        $resp = $this->residentService->saveResident($residentModel);
                    } catch (Exception $e) {
                        $notSaved[] = $residentData; // Not saved data
                    }
                } else {
                    $notSaved[] = [
                        'name' => $resident[0],
                        'iddsi_level' => $resident[1]
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