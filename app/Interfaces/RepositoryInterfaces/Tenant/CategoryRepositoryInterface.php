<?php

namespace App\Interfaces\RepositoryInterfaces\Tenant;

use Illuminate\Support\Facades\Request;

interface CategoryRepositoryInterface
{
    public function getCategories();
    public function storeCategory(array $data);
    public function updateCategory(string $categoryUuid, array $data);
    public function getProductCategories();
    public function getMaterialCategories();
    public function deleteCategory(string $categoryUuid);
}
