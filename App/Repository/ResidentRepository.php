<?php

namespace App\Repository;

use App\Lib\DB;
use App\Model\Resident;
use PDO;
use Exception;
use App\Lib\Logger;
use App\Lib\ExceptionHandler;

class ResidentRepository
{
    //----------------------------code-------------------------------//
    private $conn = null;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->conn = DB::getInstance(); 
        $this->logger = new Logger('ResidentLogger', __DIR__ . '/../../logs/resident.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * insertResident - Insert a new resident into the database.
     *
     * @param  Resident $resident 
     * @return bool|array
     */
    public function insertResident($resident): bool|array {
        try {
            $query = "
                INSERT INTO residents (name, iddsi_level, created_by) 
                VALUES (:residentName, :iddsi_level, :created_by)
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':residentName', $resident->getResidentname());
            $stmt->bindValue(':iddsi_level', $resident->getIddsiLevel());
            $stmt->bindValue(':created_by', $resident->getCreatedBy());
    
            return $stmt->execute();
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Resident not saved');
        }
    }

    /**
     * updateResident - Update an existing resident's details.
     *
     * @param  Resident $resident 
     * @return bool|array
     */
    public function updateResident($resident): bool|array {
        try {
            $query = "
                UPDATE residents 
                SET name = :residentName, iddsi_level = :iddsi_level
                WHERE id = :id
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':residentName', $resident->getResidentname());
            $stmt->bindValue(':iddsi_level', $resident->getIddsiLevel());
            $stmt->bindValue(':id', $resident->getId());
    
            return $stmt->execute();
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Resident not updated');
        }
    }

    /**
     * fetchResidentById - Fetch a resident by their ID.
     *
     * @param  int $residentId 
     * @return Resident
     */
    public function fetchResidentById($residentId): Resident {
        try {
            $query = "
                SELECT 
                    residents.id,
                    residents.name AS name,
                    residents.iddsi_level AS iddsi_level,
                    iddsi_levels.id AS iddsiLevel,
                    iddsi_levels.name AS iddsiLevelName,
                    residents.created_by AS created_by
                FROM 
                    residents
                JOIN 
                    iddsi_levels ON residents.iddsi_level = iddsi_levels.id
                WHERE 
                    residents.id = :id
            ";
        
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $residentId); 
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? new Resident($data) : null;
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * fetchResidentList - Fetch a list of residents created by a user.
     *
     * @param  int $userId 
     * @return Resident[]
     */
    public function fetchResidentList($userId): array {   
        try {
            $query = "
                SELECT 
                    residents.id,
                    residents.name AS name,
                    residents.iddsi_level AS iddsi_level,
                    iddsi_levels.id AS iddsiLevel,
                    iddsi_levels.name AS iddsiLevelName,
                    residents.created_by AS created_by
                FROM 
                    residents
                JOIN 
                    iddsi_levels ON residents.iddsi_level = iddsi_levels.id
                WHERE 
                    residents.created_by = :createdBy
            "; 

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':createdBy', $userId);
            $stmt->execute();
    
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = new Resident($row);
            }
            return $data;
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    /**
     * fetchResidentByQuery - Search for residents by name.
     *
     * @param  string $searchString
     * @param  int $userId 
     * @return Resident[]
     */
    public function fetchResidentByQuery($searchString, $userId): array {
        try {
            $query = "
                SELECT 
                    residents.id,
                    residents.name AS name,
                    residents.iddsi_level AS iddsi_level,
                    iddsi_levels.id AS iddsiLevel,
                    iddsi_levels.name AS iddsiLevelName,
                    residents.created_by AS created_by
                FROM 
                    residents
                JOIN 
                    iddsi_levels ON residents.iddsi_level = iddsi_levels.id
                WHERE 
                    residents.created_by = :createdBy 
                    AND residents.name LIKE :searchString
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':searchString', '%' . $searchString . '%');
            $stmt->bindValue(':createdBy', $userId);
            $stmt->execute();
    
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = new Resident($row);
            }
            return $data;
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }

    //-------------------------End code------------------------//
}

?>