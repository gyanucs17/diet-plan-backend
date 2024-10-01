<?php
namespace App\Repository;

use App\Lib\DB;
use App\Model\IddsiLvel;
use PDO;

class IddsiLevelRepository
{
    //---------------------code-----------------------//
    private $conn = NULL;
	
	public function __construct() {
		$this->conn = DB::getInstance(); 
    }
	/**
     * fetchIddsiLevels
     *
     * @return Array
     */
	public function fetchIddsiLevels()
    {
        $stmt = $this->conn->prepare("SELECT * FROM iddsi_levels");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>