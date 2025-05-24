<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\ServiceInterfaces\Tenant\ProcurementServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\SupplierServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectServiceInterface;
use App\Facades\Breadcrumbs;
use App\Http\Requests\ProcurementRequest;
use App\Models\Project;
use App\Http\Requests\ProductRequest;

class ProductLibraryController extends Controller
{
    private $folder = 'pages.product-library.';
    private $service, $supplierService, $projectServiceInterface;

    public function __construct(ProcurementServiceInterface $procurementService, SupplierServiceInterface $supplierService, ProjectServiceInterface $projectServiceInterface)
    {
        $this->service = $procurementService;
        $this->supplierService = $supplierService;
        $this->projectServiceInterface = $projectServiceInterface;
        $this->createBreadcrumbs(request());
    }

    private function createBreadcrumbs($request)
    {
        if ($request?->project && $request->isMethod('GET')) {
            $project = Project::findByUuid($request->project)->first();
            Breadcrumbs::add('Projects', 'projects.index');
            Breadcrumbs::add($project?->name, 'projects.procurements.index', ['project' => $project?->uuid]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Breadcrumbs::add('Product Library', 'product-library.index',);
        $suppliers = $this->supplierService->getSuppliers();
        $allCompanyProducts = $this->service->getAllProducts();
        $projects = $this->projectServiceInterface->getProjectsWithAreasAndMeasurementUnit();
        return view($this->folder . 'index', compact('allCompanyProducts', 'suppliers', 'projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProcurementRequest $request)
    {
        return $this->service->storeProduct($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request)
    {
        return $this->service->updateProduct($request->product_library, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        return $this->service->deleteProduct($request->product_library);
    }
}
