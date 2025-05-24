<?php

namespace App\Interfaces\RepositoryInterfaces\Tenant;

use Illuminate\Support\Facades\Request;

interface ProcurementRepositoryInterface
{
    public function getProcurements(string $projectUuid);
    public function storeProcurement(string $subdomain, string $projectUuid, array $data);
    public function getProducts(string $projectUuid);
    public function addProcurementFromLibrary(string $projectUuid, array $data);
    public function updateProcurement(string $procurementUuid, array $data);
    public function getAllProducts();
    public function updateProduct(string $productUuid, array $data);
    public function deleteProduct(string $productUuid);
    public function deleteProcurement(string $procurementUuid);
    public function duplicateProcurement(string $procurementUuid);
    public function updateStatusProcurement(string $procurementUuid, string $status);
    public function updateProcurementMarkup(string $procurementUuid, array $data);
    public function storeProduct(array $data);
}
