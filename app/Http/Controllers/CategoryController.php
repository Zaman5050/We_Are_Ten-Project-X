<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\ServiceInterfaces\Tenant\CategoryServiceInterface;

class CategoryController extends Controller
{
    private $service, $folder;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->service = $categoryService;
        $this->folder = 'pages.categories.';
    }

    public function index(Request $request)
    {
        $productCategories = $this->service->getProductCategories();
        $materialCategories = $this->service->getMaterialCategories();
        return view($this->folder . 'index', compact('productCategories', 'materialCategories'));
    }
    public function store(Request $request)
    {
        return $this->service->storeCategory($request->all());
    }

    public function update(Request $request)
    {
        return $this->service->updateCategory($request->category, $request->all());
    }
    public function destroy(Request $request)
    {
        return $this->service->deleteCategory($request->category);
    }
}
