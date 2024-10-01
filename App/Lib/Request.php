<?php 

namespace App\Lib;

class Request
{
    public $params;
    public $reqMethod;
    public $contentType;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->reqMethod = trim($_SERVER['REQUEST_METHOD']);
        $this->contentType = !empty($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    }

    public static function getBody() {
        // Check if the content type is JSON
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            // Get JSON input
            $input = json_decode(file_get_contents("php://input"), true);
            return $input;
        }

        // For application/x-www-form-urlencoded
        return $_POST;
    }

    public function getJSON()
    {
        if ($this->reqMethod !== 'POST') {
            return [];
        }

        if (strcasecmp($this->contentType, 'application/json') !== 0) {
            return [];
        }
        // Receive the RAW post data.
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content);
        return $decoded;
    }
}
