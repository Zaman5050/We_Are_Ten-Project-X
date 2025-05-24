<?php

namespace App\Services\Tenant;

use App\Interfaces\ServiceInterfaces\Tenant\SupplierServiceInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\SupplierRepositoryInterface;
use App\Traits\Timezone;
use App\Interfaces\ServiceInterfaces\Tenant\CategoryServiceInterface;

class SupplierService implements SupplierServiceInterface
{
    private $repository, $categoryService;
    use Timezone;

    public function __construct(SupplierRepositoryInterface $supplierRepository, CategoryServiceInterface $categoryService)
    {
        $this->repository = $supplierRepository;
        $this->categoryService = $categoryService;
    }

    public function getSuppliers()
    {
        return $this->repository->getSuppliers();
    }

    public function listing()
    {
        $suppliers =  $this->repository->getSuppliers();
        $timezones = $this->timezone_list();
        $currentCompany = auth()?->user()?->company;
        return [
            'currentCompany' => $currentCompany,
            'timezones' => $timezones,
            'categories' => $this->categoryService->getCategories(),
            'suppliers' => $suppliers,
        ];
    }

    public function deleteSupplier($request){

        $status = $this->repository->deleteSupplierEntry($request);

        if($status){
            session()->flash('message',  'Supplier entry deleted successfully');
            return response()->json(['error' => false], 200);
        }
        return response()->json(['error' => true], 200);
    }

    public function storeSupplier($request)
    {
        return $this->repository->storeSupplier($request);
    }
    public function updateSupplier($request)
    {
        $supplierUuid = $request->supplierUuid;
        return $this->repository->updateSupplier($supplierUuid, $request->validated());
    }




}
