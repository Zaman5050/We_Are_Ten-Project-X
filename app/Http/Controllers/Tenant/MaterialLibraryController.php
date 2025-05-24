<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\ServiceInterfaces\Tenant\ScheduleServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\SupplierServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectServiceInterface;
use App\Facades\Breadcrumbs;
use App\Http\Requests\ScheduleRequest;
use App\Http\Requests\MaterialRequest;
use App\Models\Project;

class MaterialLibraryController extends Controller
{
    private $folder = 'pages.material-library.';
    private $service, $supplierService, $projectServiceInterface;

    public function __construct(ScheduleServiceInterface $scheduleService, SupplierServiceInterface $supplierService, ProjectServiceInterface $projectServiceInterface)
    {
        $this->service = $scheduleService;
        $this->supplierService = $supplierService;
        $this->projectServiceInterface = $projectServiceInterface;
        $this->createBreadcrumbs(request());
    }

    private function createBreadcrumbs($request)
    {
        if ($request?->project && $request->isMethod('GET')) {
            $project = Project::findByUuid($request->project)->first();
            Breadcrumbs::add('Projects', 'projects.index');
            Breadcrumbs::add($project?->name, 'projects.schedules.index', ['project' => $project?->uuid]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Breadcrumbs::add('Material Library', 'material-library.index');
        $suppliers = $this->supplierService->getSuppliers();
        $allCompanyMaterials = $this->service->getAllMaterials();
        $projects = $this->projectServiceInterface->getProjectsWithAreasAndMeasurementUnit();
        return view($this->folder . 'index', compact('suppliers', 'allCompanyMaterials', 'projects'));
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
    public function store(ScheduleRequest $request)
    {
        return $this->service->storeMaterial($request->validated());
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
    public function update(MaterialRequest $request, string $id)
    {
        return $this->service->updateMaterial($request->material_library, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        return $this->service->deleteMaterial($request->material_library);
    }

}
