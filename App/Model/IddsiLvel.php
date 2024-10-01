<?php
namespace App\Model;

use App\Lib\Config;
use App\Lib\DB;

class IddsiLvel
{
    private $db = NULL;
	
	public function __construct() {
		$this->db = new DB();
    }

}

?>