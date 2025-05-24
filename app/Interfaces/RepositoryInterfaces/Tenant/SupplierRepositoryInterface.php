<?php

namespace App\Interfaces\RepositoryInterfaces\Tenant;

use Illuminate\Support\Facades\Request;

interface SupplierRepositoryInterface
{
    public function getSuppliers();
    public function storeSupplier($request);
    public function updateSupplier($supplierUuid ,$data);
    public function deleteSupplierEntry($request);
}
