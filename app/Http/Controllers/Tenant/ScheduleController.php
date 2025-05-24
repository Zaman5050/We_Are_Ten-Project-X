<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\ServiceInterfaces\Tenant\ScheduleServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\SupplierServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectAreaServiceInterface;
use App\Facades\Breadcrumbs;
use App\Http\Requests\ScheduleRequest;
use App\Models\Project;

class ScheduleController extends Controller
{
    private $folder = 'pages.schedules.';
    private $service, $supplierService, $projectAreaServiceInterface;

    public function __construct(
        ScheduleServiceInterface $scheduleService,
        SupplierServiceInterface $supplierService,
        ProjectAreaServiceInterface $projectAreaServiceInterface
    ) {
        $this->service = $scheduleService;
        $this->supplierService = $supplierService;
        $this->projectAreaServiceInterface = $projectAreaServiceInterface;
        $this->createBreadcrumbs(request());
    }
    private function createBreadcrumbs($request)
    {
        if ($request?->project && $request->isMethod('GET')) {
            $project = Project::findByUuid($request->project);
            if (!$project) abort(404);
            Breadcrumbs::add('Projects', 'projects.index');
            Breadcrumbs::add($project?->name, 'projects.schedules.index', ['project' => $project?->uuid]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Breadcrumbs::add('Schedules', 'projects.schedules.index', ['project' => $request->project]);
        $categoriesWithSchedules = $this->service->getSchedules($request->project);
        $suppliers = $this->supplierService->getSuppliers();
        $categoriesWithMaterials = $this->service->getMaterials($request->project);
        $project_areas = $this->projectAreaServiceInterface->getProjectAreasAndMeasurementUnit($request->project)['projectAreas'] ?? [];
        $project = Project::findByUuid($request->project)->first()->toArray();
        return view($this->folder . 'index', compact('categoriesWithSchedules', 'suppliers', 'categoriesWithMaterials', 'project_areas','project'));
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
        return $this->service->storeSchedule(request()->subdomain, request()->project, $request->validated());
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
    public function update(ScheduleRequest $request, string $id)
    {
        return $this->service->updateSchedule($request->schedule, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function addScheduleFromLibrary(Request $request)
    {
        return $this->service->addScheduleFromLibrary($request->project, $request->all());
    }

    public function exportToExcel(Request $request)
    {
        return $this->service->exportToExcel($request->project);
    }
}
