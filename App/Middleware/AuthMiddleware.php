<?php
namespace App\Middleware;

use App\Controller\AuthController;
use App\Lib\Response;

class AuthMiddleware
{
    public static function getUserIdFromToken()
    {
        $authController = new AuthController();
        $headers = getallheaders();
        
        if (isset($headers['Authorization'])) {
            $token = $headers['Authorization'];
            $userId = $authController->verifyToken($token);

            if ($userId) {
                return $userId;
            } else {
                Response::unauthorized('Invalid Token');
            }
        } else {
            Response::unauthorized('Token not found');
        }
    }
}
