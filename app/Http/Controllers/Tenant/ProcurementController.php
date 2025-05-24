<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\ServiceInterfaces\Tenant\ProcurementServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\SupplierServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectAreaServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectServiceInterface;
use App\Facades\Breadcrumbs;
use App\Http\Requests\ProcurementRequest;
use App\Models\Project;

class ProcurementController extends Controller
{
    private $folder = 'pages.procurements.';
    private $service, $supplierService, $projectAreaServiceInterface, $projectService;

    public function __construct(
        ProcurementServiceInterface $procurementService,
        SupplierServiceInterface $supplierService,
        ProjectAreaServiceInterface $projectAreaServiceInterface,
        ProjectServiceInterface $projectService
    ) {
        $this->service = $procurementService;
        $this->supplierService = $supplierService;
        $this->projectAreaServiceInterface = $projectAreaServiceInterface;
        $this->projectService = $projectService;
        $this->createBreadcrumbs(request());
    }

    private function createBreadcrumbs($request)
    {
        if ($request?->project && $request->isMethod('GET')) {
            $project = Project::findByUuid($request->project);
            if (!$project) abort(404);
            Breadcrumbs::add('Projects', 'projects.index');
            Breadcrumbs::add($project?->name, 'projects.procurements.index', ['project' => $project?->uuid]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Breadcrumbs::add('Procurements', 'projects.procurements.index', ['project' => $request->project]);
        $areasWithProcurements = $this->service->getProcurements($request->project);
        $suppliers = $this->supplierService->getSuppliers();
        $areasWithProducts = $this->service->getProducts($request->project);
        $project_areas = $this->projectAreaServiceInterface->getProjectAreasAndMeasurementUnit($request->project)['projectAreas'] ?? [];
        $project = $this->projectService->getProjectBudgetFinancial($request->project);
        return view($this->folder . 'index', compact('areasWithProcurements', 'suppliers', 'areasWithProducts', 'project_areas', 'project'));
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
        return $this->service->storeProcurement(request()->subdomain, request()->project, $request->validated());
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
    public function update(ProcurementRequest $request, string $id)
    {
        return $this->service->updateProcurement($request->procurement, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        return $this->service->deleteProcurement($request->procurement, $request->project);
    }

    public function addProcurementFromLibrary(Request $request)
    {
        return $this->service->addProcurementFromLibrary($request->project, $request->all());
    }

    public function exportToExcel(Request $request)
    {
        return $this->service->exportToExcel($request->project);
    }
    public function duplicate(Request $request)
    {
        return $this->service->duplicateProcurement($request->procurement, $request->project);
    }
    public function status(Request $request)
    {
        return $this->service->updateStatusProcurement($request->procurement, $request->project, $request->status);
    }

    public function updateProcurementMarkup(Request $request)
    {
        return $this->service->updateProcurementMarkup($request->procurementUuid, $request->all());
    }
}
