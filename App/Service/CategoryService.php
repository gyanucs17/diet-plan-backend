<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use App\Lib\Logger;
use App\Lib\ExceptionHandler; 
use Exception;

class CategoryService
{
    //-----------------------code--------------------------//
    private CategoryRepository $categoryRepository;
    private Logger $logger;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
        $this->categoryRepository = new CategoryRepository();
        $this->logger = new Logger('CategoryLogger', __DIR__ . '/../../logs/category.log');
        $this->exceptionHandler = new ExceptionHandler($this->logger);
    }

    /**
     * saveCategory - Save a new category to the repository.
     *
     * @param  Category $category 
     * @return bool|array 
     */
    public function saveCategory($category): bool|array {
        try {
            return $this->categoryRepository->insertCategory($category);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'Category not saved');
        }
    }

    /**
     * updateCategory - Update an existing category in the repository.
     *
     * @param  Category $category 
     * @return bool|array 
     */
    public function updateCategory($category): bool|array {
        try {
            return $this->categoryRepository->updateCategory($category);
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'Category not updated');
        }
    }

    /**
     * getCategoryList - Retrieve a list of all categories.
     * 
     * @return Category[]|array 
     */
    public function getCategoryList(): array {
        try {
            return $this->categoryRepository->fetchCategorylist();
        } catch(Exception $e) {
            return $this->exceptionHandler->handle($e, 'No data found');
        }
    }
}