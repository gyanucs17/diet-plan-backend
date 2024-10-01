<?php

namespace App\Lib;

class Response
{
    private $status = 200;
    
    private function get_status_message($code){
		$status = array(
			'success' => 200,
            'failed' => 400,  
			'No Content' =>204,  
			'Not Found' => 404,  
			'Not Acceptable' => 406,
			'Unauthorized'  => 401,
		);
		return ($status[$code])?$status[$code]:$status[500];
	}

    public function status(int $code)
    {
        $this->status = $code;
        return $this;
    }
    
    public function toJSON($data = [])
    {
        $code = 'success';
        if(isset($data['status']))
            $code = $data['status'];
        http_response_code($this->get_status_message($code));
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
