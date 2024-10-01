<?php

namespace App\Service;

use App\Repository\ResidentRepository;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 
use Exception;

class ResidentService
{
    //-------------------------------code-----------------------------------//
    private ResidentRepository $residentRepository;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->residentRepository = new ResidentRepository();
        $this->logger = new Logger('ResidentLogger', __DIR__ . '/../../logs/resident.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * saveResident - Save a new resident.
     *
     * @param  Resident $resident 
     * @return bool|array 
     */
    public function saveResident($resident): bool|array {
        try {
            return $this->residentRepository->insertResident($resident);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'Resident not saved');
        }
    }

    /**
     * updateResident - Update an existing resident's information.
     *
     * @param  Resident $resident 
     * @return bool|array 
     */
    public function updateResident($resident): bool|array {
        try {
            return $this->residentRepository->updateResident($resident);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'Resident not updated');
        }
    }

    /**
     * searchResident - Search for residents by a query string.
     *
     * @param  string $searchString 
     * @param  int $userId 
     * @return Resident[]|array 
     */
    public function searchResident($searchString, $userId): array {
        try {
            return $this->residentRepository->fetchResidentByQuery($searchString, $userId);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * getResidentList - Retrieve a list of residents.
     *
     * @param  int $userId 
     * @return Resident[]|array 
     */
    public function getResidentList($userId): array {
        try {
            return $this->residentRepository->fetchResidentlist($userId);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * getResidentById - Retrieve a resident by their ID.
     *
     * @param  int $residentId 
     * @return Resident|array 
     */
    public function getResidentById($residentId): array {
        try {
            return $this->residentRepository->fetchResidentByid($residentId);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }
}
