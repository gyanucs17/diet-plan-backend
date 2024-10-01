<?php
require __DIR__ . '/vendor/autoload.php';

use App\Lib\Router;
use App\Lib\Request;
use App\Lib\Response;
use App\Middleware\CorsMiddleware;
use App\Middleware\AuthMiddleware;
use App\Controller\ResidentController;
use App\Controller\AuthController;
use App\Controller\FoodController;
use App\Controller\ResidentDietPlanController;
use App\Controller\CategoryController;
use App\Model\User;

// Handle CORS preflight requests
CorsMiddleware::handle();

//-------------------------------------code---------------------------------//
/**
 * getAccessToken
 *
 * @return int $userId 
 */
$userId = "";
$response = new Response();
$foodController = new FoodController();
$authController = new AuthController();
$redidentController = new ResidentController();
$residentDietPlanController = new ResidentDietPlanController();
$categoryController = new CategoryController();
$routes = new Router();

// Load routes from a separate file
require __DIR__ . '/App/Routes/api.php';

try {
    // Get the HTTP method and URI
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // Match the route and extract parameters
    $route = $routes->match($httpMethod, $uri);
    if ($route) {
        if($uri != '/user/login' && $uri != '/user/register'){
            $userId = AuthMiddleware::getUserIdFromToken();
        }
        
        $handler = $route['handler'];
        $params = $route['params'];
        $params['created_by'] = $userId; 
        $body = Request::getBody();
        if ($httpMethod === 'POST' || $httpMethod === 'PUT') {
            $params = array_merge($params, $body);
        }
        $response->toJSON(call_user_func_array($handler, [$params]));
    } else {
        $response->toJSON(['status' => 'failed', 'msg' => 'Url Not Found']); 
    }

} catch(Exception $e) {
    $response->toJSON(['status' => 'failed', 'msg' => 'Url Not Found']); 
}




