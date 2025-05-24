<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\ServiceInterfaces\UserServiceInterface;
use App\Traits\Timezone;
use App\Http\Requests\Teams\ProfileUpdateRequest;
use App\Http\Resources\Tenant\TeamResource;
use App\Interfaces\ServiceInterfaces\Tenant\FileHandlingServiceInterface;

class UserController extends Controller
{

    use Timezone;
    protected $service, $fileHandlingService;

    public function __construct(
        UserServiceInterface $userService,
        FileHandlingServiceInterface $fileHandlingService
    ) {
        $this->service = $userService;
        $this->fileHandlingService = $fileHandlingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentAdmin = currentAdmin();
        $company = $currentAdmin?->company;
        $teams = $company?->teams ?: collect();

        if ($teams->isNotEmpty()) {
            $teams->load(['upcomingLeave', 'timesheets']);
            $teams->loadCount(['memberActiveProjects']);
        }

        return view('pages.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentAdmin = currentAdmin();
        $company = $currentAdmin?->company;

        $timezones = $this->timezone_list();
        return view('pages.teams.add-member', compact('company', 'timezones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $currentAdmin = currentAdmin();
        $company = $currentAdmin?->company;
        $this->service->createTeamMember($request, $company);
        return tenant_to_route('teams.index');
    }

    /**
     * Display the specified resource.
     */



    public function show(Request $request)
    {
        $member = $this->service->findTeamMember($request->uuid);
        $projects = $this->service->getMemberPojects($request->uuid);

        return view('pages.teams.show', [
            "member" => $member,
            "projects" => $projects,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $member = $this->service->getProfileMember($request->uuid);
        $member = TeamResource::make($member);
        $timezones = $this->timezone_list();
        return view('pages.auth.teams.profile', compact('member', 'timezones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileUpdateRequest $request)
    {
        return $this->service->updateProfile($request->uuid, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }



    public function createPasswordView(Request $request)
    {
        return $this->service->createPasswordView($request);
    }

    public function createPasswordStore(Request $request)
    {
        return $this->service->createPassword($request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editMemberProfile(Request $request)
    {
        $member = $this->service->getProfileMember($request->uuid);
        $member = TeamResource::make($member);
        $timezones = $this->timezone_list();
        return view('pages.auth.teams.member-profile', compact('member', 'timezones'));
    }

    public function uploadProfilePicture(Request $request)
    {
        return $this->fileHandlingService->uploadAttachments($request->all());
    }

    public function deleteProfilePicture(Request $request)
    {
        return $this->fileHandlingService->deleteAttachment($request->all());
    }


    public function getTasks(Request $request)
    {
        $member = $this->service->findTeamMember($request->uuid);
        $tasks = $this->service->getMemberTasks($request->uuid);
        $projectCount = $this->service->getMemberPojects($request->uuid)->count();
        return view('pages.teams.tasks', [
            "member" => $member,
            "tasks" => $tasks,
            'projectCount' => $projectCount,
        ]);
    }


    public function getLeaves(Request $request)
    {
        $member = $this->service->findTeamMember($request->uuid);
        $leaves = $member->leaves;
        $pendingLeaves = $leaves->whereIn('status', ['pending', 'negotiated', 're-negotiated']);
        $historyLeaves = $leaves->whereIn('status', ['approved', 'declined']);
        $projectCount = $this->service->getMemberPojects($request->uuid)->count();

        return view('pages.teams.leaves', [
            "member" => $member,
            "leaves" => $leaves,
            "pendingLeaves" => $pendingLeaves,
            "historyLeaves" => $historyLeaves,
            'projectCount' => $projectCount,
        ]);
    }

    public function deleteMember(Request $request)
    {
        return $this->service->deleteMember($request->uuid);
    }
}
