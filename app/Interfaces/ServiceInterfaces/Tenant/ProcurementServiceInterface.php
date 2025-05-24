<?php

namespace App\Interfaces\ServiceInterfaces\Tenant;

use Illuminate\Support\Facades\Request;

interface ProcurementServiceInterface
{
    public function getProcurements(string $projectUuid);
    public function storeProcurement(string $subdomain, string $projectUuid, array $data);
    public function getProducts(string $projectUuid);
    public function addProcurementFromLibrary(string $projectUuid, array $data);
    public function exportToExcel(string $projectUuid);
    public function updateProcurement(string $procurementUuid, array $data);
    public function getAllProducts();
    public function updateProduct(string $productUuid, array $data);
    public function deleteProduct(string $productUuid);
    public function deleteProcurement(string $procurementUuid, string $projectUuid);
    public function duplicateProcurement(string $procurementUuid, string $projectUuid);
    public function updateStatusProcurement(string $procurementUuid, string $projectUuid, string $status);
    public function updateProcurementMarkup(string $procurementUuid, array $data);
    public function storeProduct(array $data);
}
