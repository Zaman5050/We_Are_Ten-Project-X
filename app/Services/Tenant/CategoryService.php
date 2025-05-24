<?php

namespace App\Services\Tenant;

use App\Interfaces\ServiceInterfaces\Tenant\CategoryServiceInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\CategoryRepositoryInterface;
use Exception;

class CategoryService implements CategoryServiceInterface
{
    private $repository;
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {

        $this->repository = $categoryRepository;
    }

    public function getCategories()
    {
        return $this->repository->getCategories();
    }

    public function storeCategory(array $data)
    {
        try {
            $category = $this->repository->storeCategory($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        return response()->json([
            'message' => 'Category has been created successfully.',
            'category' => $category
        ]);
    }
    public function updateCategory(string $categoryUuid, array $data)
    {
        try {
            $category = $this->repository->updateCategory($categoryUuid, $data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        return response()->json([
            'message' => 'Category has been updated successfully.',
            'category' => $category
        ]);
    }

    public function getProductCategories()
    {
        return $this->repository->getProductCategories();
    }

    public function getMaterialCategories()
    {
        return $this->repository->getMaterialCategories();
    }
    public function deleteCategory(string $categoryUuid)
    {
        try {
            $category = $this->repository->deleteCategory($categoryUuid);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        return response()->json([
            'message' => 'Category has been deleted successfully.',
            'category' => $category
        ]);
    }
}
