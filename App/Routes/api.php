<?php

use App\Controller\AuthController;
use App\Controller\FoodController;
use App\Controller\ResidentController;
use App\Controller\ResidentDietPlanController;
use App\Controller\CategoryController;
use App\Lib\Router;

$authController = new AuthController();
$foodController = new FoodController();
$residentController = new ResidentController();
$residentDietPlanController = new ResidentDietPlanController();
$categoryController = new CategoryController();

$routes = new Router();

// User API routes
$routes->group('/user', function($router) use ($authController) {
    $router->addRoute('POST', '/login', [$authController, 'processLogin']);
    $router->addRoute('POST', '/register', [$authController, 'register']);
});

// Food API routes
$routes->group('/food', function($router) use ($foodController) {
    $router->addRoute('POST', '/add-food', [$foodController, 'addFood']);
    $router->addRoute('PUT', '/update-food', [$foodController, 'updateFood']);
    $router->addRoute('POST', '/upload-food-csv', [$foodController, 'addFoodByCSV']);
    $router->addRoute('GET', '/get-food/{id}', [$foodController, 'getFoodByid']);
    $router->addRoute('GET', '/get-food-list', [$foodController, 'getFoodList']);
    $router->addRoute('GET', '/search-food/{search}', [$foodController, 'searchFood']);
    $router->addRoute('GET', '/get-resident-food-list/{id}', [$foodController, 'filterFoodByIddsiLevel']);
});

// Resident API routes
$routes->group('/resident', function($router) use ($residentController) {
    $router->addRoute('POST', '/add-resident', [$residentController, 'addResident']);
    $router->addRoute('PUT', '/update-resident', [$residentController, 'updateResident']);
    $router->addRoute('POST', '/upload-resident-csv', [$residentController, 'addResidentByCSV']);
    $router->addRoute('GET', '/get-resident/{id}', [$residentController, 'getResidentByid']);
    $router->addRoute('GET', '/get-resident-list', [$residentController, 'getResidentList']);
    $router->addRoute('GET', '/search-resident/{search}', [$residentController, 'searchResident']);
});

// Resident Diet Plan API routes
$routes->group('/diet', function($router) use ($residentDietPlanController) {
    $router->addRoute('POST', '/assign-food', [$residentDietPlanController, 'assignFood']);
    $router->addRoute('POST', '/unassign-food', [$residentDietPlanController, 'unAssignFood']);
    $router->addRoute('GET', '/search-diet-food/{search}/{id}', [$residentDietPlanController, 'filterFoodByIddsiLevelSearchQuery']);
});

// Category API routes
$routes->group('/category', function($router) use ($categoryController) {
    $router->addRoute('POST', '/add-category', [$categoryController, 'addCategory']);
    $router->addRoute('PUT', '/update-category', [$categoryController, 'updateCategory']);
    $router->addRoute('GET', '/get-all-category', [$categoryController, 'getCategoryList']);
});

return $routes;