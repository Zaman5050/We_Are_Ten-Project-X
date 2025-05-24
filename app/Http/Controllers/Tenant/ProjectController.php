<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\DropboxServiceInterface;
use App\Http\Requests\ProjectRequest;
use App\Facades\Breadcrumbs;
use App\Models\Project;


class ProjectController extends Controller
{

    private $folder = 'pages.projects.';
    private $service, $dropboxService;

    public function __construct(ProjectServiceInterface $projectService, DropboxServiceInterface $dropboxService)
    {
        $this->service = $projectService;
        $this->dropboxService = $dropboxService;
        $this->createBreadcrumbs(request());
    }

    private function createBreadcrumbs($request)
    {
        if (!$request->project || !$request->isMethod('GET')) {
            return;
        }

        $project = Project::findOrFailByUuid($request->project);
        Breadcrumbs::add('Projects', 'projects.index');
        Breadcrumbs::add($project?->name, 'projects.show', ['project' => $project?->uuid]);

        if ($request->routeIs('projects.team')) {
            Breadcrumbs::add('Teams', 'projects.team', ['project' => $project?->uuid]);
        }
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = $this->service->getActiveProjects([], 'active');
        $draftProjects = $this->service->getActiveProjects([], 'draft');
        return view($this->folder . 'index', compact('projects', 'draftProjects'));
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
    public function store(ProjectRequest $request)
    {
        return $this->service->storeProject($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = $this->service->getProjectDashboardData(request()->project);
        return view($this->folder . 'dashboard', compact('data'));
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
    public function update(ProjectRequest $request)
    {
        return $this->service->updateProject($request->all(), $request->project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        return $this->service->deleteProject($request->project);
    }

    public function getDetails(Request $request)
    {
        $project = $this->service->findProject($request->project);
        $project->load(['members', 'tags', 'stages', 'attachment']);
        Breadcrumbs::add('Details', 'projects.details', ['project' => $request->project]);
        return view($this->folder . 'details', compact('project'));
    }

    public function getFiles(Request $request)
    {
        Breadcrumbs::add('Files', 'projects.files', ['project' => $request->project]);
        $isDropboxConnected = auth()->user()->dropbox_access_token != '' ? 1 : 0;
        $contentList = $this->dropboxService->getAllFilesFromDropbox($request?->data ?? '');
        $project = $this->service->findProject($request->project);
        $project->load(['members']);
        if ($request->ajax()) {
            return response()->json([
                'contentList' => $contentList,
            ], 200);
        } else {
            return view($this->folder . 'files', compact('contentList', 'isDropboxConnected', 'project'));
        }
    }

    public function getTeam(Request $request)
    {
        Breadcrumbs::add('Teams', 'projects.team', ['project' => $request->project]);
        $project = $this->service->getProjectTeam($request->project);
        return view($this->folder . 'team', compact('project'));
    }

    public function getPrecurements(Request $request, string $id)
    {
        Breadcrumbs::add('Procurements', 'projects.procurements', ['project' => $request->project]);
        return view($this->folder . 'procurements');
    }

    public function upload(Request $request)
    {
        return $this->service->uploadAttachments($request);
    }

    public function deleteAttachment(Request $request)
    {
        return $this->service->deleteAttachment($request);
    }

    public function clone(Request $request)
    {
        $this->service->cloneProject($request->project);
        return redirect()->route('tenant.projects.index', ['subdomain' => request()->subdomain])
            ->with('message', 'Project cloned successfully.');
    }

    public function updateProjectFinancials(Request $request)
    {
        return $this->service->updateProjectFinancials($request->input(), $request->project);
    }

    public function getProjectBudgetFinancial(Request $request)
    {
        $project = $this->service->getProjectBudgetFinancial($request->project);
        Breadcrumbs::add('Financial', 'project.financials.budget', ['project' => $request->project]);
        return view('pages.financials.budget', compact('project'));
    }

    public function getProjectCostingFinancial(Request $request)
    {
        $costingFinancialData = $this->service->projectWithCostingFinancial($request->project);
        Breadcrumbs::add('Financial', 'project.financials.costing', ['project' => $request->project]);
        return view('pages.financials.costing', compact('costingFinancialData'));
    }

    public function getProjectProcurementFinancial(Request $request)
    {
        $procurementFinancialData = $this->service->projectWithProcurementFinancial($request->project);
        Breadcrumbs::add('Financial', 'project.financials.procurement', ['project' => $request->project]);
        return view('pages.financials.procurement', compact('procurementFinancialData'));
    }

    public function getProjectServiceFinancial(Request $request)
    {
        $serivceFinancialData = $this->service->getProjectServiceFinancial($request->project);
        Breadcrumbs::add('Financial', 'project.financials.services', ['project' => $request->project]);
        return view('pages.financials.services', compact('serivceFinancialData'));
    }
}
