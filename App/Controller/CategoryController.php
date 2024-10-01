<?php 

namespace App\Controller;

use App\Service\CategoryService;
use App\Model\Category;
use App\Lib\Helper;
use Exception;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 

class CategoryController
{
    private CategoryService $categoryService;
    private Helper $helper;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->categoryService = new CategoryService();
        $this->helper = new Helper();
        $this->logger = new Logger('FoodLogger', __DIR__ . '/../../logs/food.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * addCategory - Add a new category.
     * 
     * @param  array $req 
     * @return array Response 
     */
    public function addCategory(array $req): array {
        $data = $this->helper->validateCategoryParams($req); // Validation for required params
        
        if (isset($data['status']) && $data['status'] === 'failed') {
            return $data;
        }

        try {
            // Create category model
            $category = new Category($req);
            $resp = $this->categoryService->saveCategory($category);
            return $this->helper->respond($resp, 'Category Saved', 'Category not Saved');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Category not saved');
        }
    }
    
    /**
     * updateCategory - Update an existing category.
     *
     * @param  array $req 
     * @return array Response 
     */
    public function updateCategory(array $req): array {
        $data = $this->helper->validateCategoryParams($req['category']); 
        
        if (isset($data['status']) && $data['status'] === 'failed') {
            return $data;
        }

        try {
            // Create category model
            $category = new Category($req['category']);
            $resp = $this->categoryService->updateCategory($category);
            return $this->helper->respond($resp, 'Category Updated', 'Category not Updated');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'Category not updated');
        }
    }
    
    /**
     * getCategoryList - Get the list of categories.
     *
     * @param  array $req 
     * @return array Response 
     */
    public function getCategoryList(array $req): array {
        try {
            // Getting category data
            $categories = $this->categoryService->getCategoryList();
            return $this->helper->respondList($categories, 'No data found');
        } catch (Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data Found');
        }
    }

    // End of code
}
