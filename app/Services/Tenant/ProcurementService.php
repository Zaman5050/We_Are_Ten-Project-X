<?php

namespace App\Services\Tenant;

use App\Interfaces\ServiceInterfaces\Tenant\ProcurementServiceInterface;
use App\Interfaces\RepositoryInterfaces\Tenant\ProcurementRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProcurementExport;
use App\Traits\FileHandler;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProcurementService implements ProcurementServiceInterface
{
    private $repository;
    use FileHandler;

    public function __construct(ProcurementRepositoryInterface $procurementRepositoryInterface)
    {
        $this->repository = $procurementRepositoryInterface;
    }

    public function getProcurements(string $projectUuid)
    {
        return $this->repository->getProcurements($projectUuid);
    }

    public function storeProcurement(string $subdomain, string $projectUuid, array $data)
    {
        return $this->repository->storeProcurement($subdomain, $projectUuid, $data);
    }

    public function getProducts(string $projectUuid)
    {
        return $this->repository->getProducts($projectUuid);
    }

    public function addProcurementFromLibrary($projectUuid, $data)
    {
        return $this->repository->addProcurementFromLibrary($projectUuid, $data);
    }

    public function exportToExcel(string $projectUuid)
    {
        $procurements = $this->repository->getProcurements($projectUuid);
        return Excel::download(new ProcurementExport($procurements), 'procurements.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function updateProcurement(string $procurementUuid, array $data)
    {
        return $this->repository->updateProcurement($procurementUuid, $data);
    }

    public function getAllProducts()
    {
        return $this->repository->getAllProducts();
    }

    public function updateProduct(string $productUuid, array $data)
    {
        return $this->repository->updateProduct($productUuid, $data);
    }

    public function deleteProduct(string $productUuid)
    {
        try {
            $this->repository->deleteProduct($productUuid);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        return response()->json(['message' => 'Product has been deleted successfully.'], 200);
    }
    public function deleteProcurement(string $procurementUuid, string $projecttUuid)
    {
        try {
            $this->repository->deleteProcurement($procurementUuid);
            return redirect(tenant_route('projects.procurements.index', ['project' => $projecttUuid]))
                ->with('message', 'Procurement has been deleted successfully.');
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }

        return response()->json(['message' => 'Procurement has been deleted successfully.'], 200);
    }
    public function duplicateProcurement(string $procurementUuid, string $projecttUuid)
    {
        try {
            $duplicatedProcurement = $this->repository->duplicateProcurement($procurementUuid);
            if (!$duplicatedProcurement) {
                return redirect(tenant_route('projects.procurements.index', ['project' => $projecttUuid]))
                    ->with([
                        'message' => 'Duplication failed! Insufficient quantity available.',
                        'alert-type' => 'error',
                    ]);
            }
            return redirect(tenant_route('projects.procurements.index', ['project' => $projecttUuid]))
                ->with([
                    'message' => 'Procurement has been duplicated successfully.',
                    'alert-type' => 'success',
                ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
    }
    public function updateStatusProcurement(string $procurementUuid, string $projecttUuid, string $status)
    {
        try {
            $this->repository->updateStatusProcurement($procurementUuid, $status);
            return redirect(tenant_route('projects.procurements.index', ['project' => $projecttUuid]))
                ->with([
                    'message' => 'Procurement status has been Updated successfully.',
                    'alert-type' => 'success',
                ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
    }

    public function updateProcurementMarkup(string $procurementUuid, array $data)
    {
        try {
            $procurement =  $this->repository->updateProcurementMarkup($procurementUuid, $data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        return response()->json(['message' => 'Procurement has been duplicated successfully.', 'procurement' => $procurement]);
    }

    public function storeProduct(array $data)
    {
        try {
            $product =  $this->repository->storeProduct($data);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        return response()->json([
            'message' => 'Procurement has been created successfully.',
            'product' => $product
        ]);
    }
}
