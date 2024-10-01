<?php
namespace App\Middleware;

class CorsMiddleware {
    public static function handle() {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
            header("Access-Control-Allow-Headers: X-API-KEY, X-Requested-With, Content-Type, Authorization");
            header('Access-Control-Max-Age: 1728000');
            header('Content-Length: 0');
            header('content-type: text/plain');
            header("HTTP/1.1 200 OK");
            exit();
        }

        header('Access-Control-Allow-Origin: *');
        header('content-type: text/plain');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header("Access-Control-Allow-Headers: Content-Type, Origin, Authorization, Accept, Access-Control-Request-Headers, Access-Control-Request-Method");

    }
}


?>