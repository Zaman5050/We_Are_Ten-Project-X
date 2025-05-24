<?php

namespace App\Repositories\Tenant;

use App\Interfaces\RepositoryInterfaces\Tenant\CategoryRepositoryInterface;
use Exception;
use App\Models\Categories;

class CategoryRepository implements CategoryRepositoryInterface

{
    protected $model;

    public function __construct(Categories $category)
    {
        $this->model = $category;
    }

    public function getCategories()
    {
        return $this->model::where('company_id', auth()?->user()?->company_id)->get();
    }

    public function storeCategory(array $data)
    {
        try {
            return $this->model::create([
                ...$data,
                'company_id' => auth()?->user()?->company_id
            ]);
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    public function updateCategory(string $categoryUuid, array $data)
    {
        try {
            $category = $this->model::where('uuid', $categoryUuid)->first();
            $category->update($data);
            return $category;
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }
    public function getProductCategories()
    {
        return $this->model::where('company_id', auth()?->user()?->company_id)->where('type', $this->model::TYPE_PRODUCT)->get();
    }
    public function getMaterialCategories()
    {
        return $this->model::where('company_id', auth()?->user()?->company_id)->where('type', $this->model::TYPE_MATERIAL)->get();
    }
    public function deleteCategory(string $categoryUuid)
    {
        try {
            $category = $this->model::where('uuid', $categoryUuid)->first();

            if (!$category) {
                throw new Exception("Category not found.");
            }

            $hasProducts = $category->products()->exists(); 
            $hasMaterials = $category->materials()->exists(); 
            $hasSuppliers = $category->suppliers()->exists(); 
            $hasProcurmentBudgetCategory = $category->procurmentBudgetCategory()->exists(); 
            if ($hasProducts || $hasMaterials || $hasSuppliers || $hasProcurmentBudgetCategory) {
                throw new Exception("Cannot delete category. It has associated products, materials, or suppliers.");
            }

            $category->delete();
            return response()->json(['message' => 'Category deleted successfully.'], 200);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
