<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectAreaServiceInterface;
use App\Http\Requests\ProjectAreaRequest;
use App\Facades\Breadcrumbs;
use App\Models\Project;

class ProjectAreaController extends Controller
{
    private $folder = 'pages.projects.project-areas';
    private $service;

    public function __construct(ProjectAreaServiceInterface $projectAreaService)
    {
        $this->service = $projectAreaService;
        $this->createBreadcrumbs(request());
    }

    private function createBreadcrumbs($request)
    {
        if($request?->project && $request->isMethod('GET')){
            $project = Project::findOrFailByUuid($request->project);
            Breadcrumbs::add('Projects', 'projects.index');
            Breadcrumbs::add($project?->name, 'projects.show',[ 'project' => $project?->uuid]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $this->service->getProjectAreasAndMeasurementUnit($request->project);
        Breadcrumbs::add('Areas', 'project-areas.index',['project' => $request->project]);
        return view($this->folder, compact('data'));
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectAreaRequest $request)
    {
        return $this->service->createOrUpdateProjectAreas( $request->project, $request->validated() );
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        return $this->service->deleteProjectAreas($request->project_area);
    }

}
