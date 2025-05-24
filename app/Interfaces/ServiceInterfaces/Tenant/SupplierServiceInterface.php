<?php

namespace App\Interfaces\ServiceInterfaces\Tenant;

use Illuminate\Support\Facades\Request;

interface SupplierServiceInterface
{
    public function getSuppliers();
    public function listing();
    public function storeSupplier($request);
    public function updateSupplier($request);
    public function deleteSupplier($request);
}
