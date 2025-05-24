<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProjectGroupChatRequest;
use App\Interfaces\ServiceInterfaces\ChatServiceInterface;
use App\Interfaces\ServiceInterfaces\Tenant\ProjectServiceInterface;
use App\Http\Requests\ChatMessageRequest;
use App\Http\Resources\Tenant\TeamResource;
use App\Facades\Breadcrumbs;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Tenant\Chat\ChatResource;
use Illuminate\Support\Arr;
use App\Models\Project;


class ChatController extends Controller
{

    protected $service;
    protected $projectService;

    public function __construct(
        ChatServiceInterface $chatService,
        ProjectServiceInterface $ProjectService
    ) {
        $this->service = $chatService;
        $this->projectService = $ProjectService;
        $this->createBreadcrumbs(request());
    }

    private function createBreadcrumbs($request)
    {
        if ($request?->project && $request->isMethod('GET')) {
            $project = Project::findOrFailByUuid($request->project);
            Breadcrumbs::add('Projects', 'projects.index');
            Breadcrumbs::add($project?->name, 'projects.show', ['project' => $project?->uuid]);
        }
    }

    public function index(Request $request)
    {
        $userCompany = auth()?->user()?->company;

        // Retrieve active projects
        $projects = $this->projectService->getActiveProjects();

        // Load related chats for each project, even if they are empty
        $projects->load([
            'chats' => function ($query) {
                $query->whereHas('projectChatMembers', fn($uQuery) => $uQuery->where("user_id", auth()->id()));
                $query->with(['project', 'projectChatMembers', 'chatMessages' => fn($q) => $q->with("sender")->withCount('getChatMessagesStatus')])
                    ->withCount(['projectChatMembers']);
            }
        ]);

        // Retrieve direct chats
        $directChats = $this->service->directChats();
        $directChats = ChatResource::collection($directChats)->response()->getData(true);

        $directChatProjects = [];
        foreach ($directChats as $chat) {
            $directChatProjects[] = [
                'id' => 0,
                'uuid' => $chat['uuid'],
                'name' => 'Direct Chat',
                'chats' => [$chat],  // Ensure the chat is wrapped in an array
            ];
        }
        // Construct the "Direct Chat" project structure


        // Ensure all projects are included, even those without chats
        $projects = $projects->map(function ($project) {
            if ($project->chats->isEmpty()) {
                $project->chats = [];  // Assign an empty array if no chats exist for the project
            }
            return $project;
        });

        // Flatten the project chats into a single collection
        $chats = collect();
        if ($projects) {
            $chats = Arr::collapse($projects->pluck('chats'));
            $chats = ChatResource::collection($chats)->response()->getData(true);
            $projects = ProjectResource::collection($projects)->response()->getData(true);
        }

        // Retrieve team members excluding the current user
        $teamMembers = collect();
        if ($userCompany?->teams) {
            $teamMembers = TeamResource::collection($userCompany?->teams->where('id', '!=', auth()->id())->unique())->response()->getData(true) ?: collect();
            $teamMembers = collect($teamMembers);
        }
        // Add the direct chat project at the beginning of the projects array
        $chats = array_merge($chats, $directChats); // Merge directChatProject into projects
        $projects = array_merge($directChatProjects, $projects); // Merge directChatProject into projects
        return view('pages.chats.index', compact('projects', 'chats', 'teamMembers', 'directChats'));
    }

    public function getChats(Request $request, string $id)
    {
        Breadcrumbs::add('Chats', 'projects.chats', ['project' => $request->project]);

        $projects = collect();
        $project = $this->projectService->findProject($request->project);
        $chats = $this->service->getProjectChats([$project->id]);

        $teamMembers = collect();
        if ($project?->members) {
            $teamMembers = TeamResource::collection($project?->members->where('id', '!=', auth()->id())->unique())->response()->getData(true) ?: collect();
        }
        $teamMembers = collect($teamMembers);

        return view('pages.projects.chats', compact('project', 'projects', 'chats', 'teamMembers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ProjectGroupChatRequest $request)
    {
        $project = $this->projectService->findProject($request->project);
        return $this->service->createProjectGroupChat($request->validated(), $project);
    }

    public function startProjectChat(Request $request)
    {
        $project = $this->projectService->findProject($request->project);
        return $this->service->createProjectChat($project);
    }

    public function addParticipant(Request $request)
    {
        return $this->service->addParticipant($request);
    }

    public function sendMessage(ChatMessageRequest $request)
    {
        return $this->service->sendMessage($request);
    }

    public function getMessages(Request $request)
    {
        return $this->service->getMessages($request);
    }

    public function markSeenMessages(Request $request)
    {
        return $this->service->markSeenMessages($request);
    }
    public function createDirectChat(Request $request)
    {
        return $this->service->createDirectChat($request);
    }
}
