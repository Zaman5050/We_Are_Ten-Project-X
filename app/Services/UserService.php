<?php

namespace App\Services;

use Illuminate\Validation\Rule;
use App\Interfaces\ServiceInterfaces\UserServiceInterface;
use App\Interfaces\RepositoryInterfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use App\Events\Teams\MemberRegistered;
use App\Interfaces\RepositoryInterfaces\CompanyRepositoryInterface;
use App\Interfaces\RepositoryInterfaces\TaskRepositoryInterface;
use App\Traits\AuthExtension;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Interfaces\RepositoryInterfaces\Tenant\ProjectRepositoryInterface;
use Exception;
use Carbon\Carbon;


class UserService implements UserServiceInterface
{
    use AuthExtension;
    protected $repository,
        $companyRepository,
        $taskRepository,
        $projectRepository;


    public function __construct(
        UserRepositoryInterface $userRepositoryInterface,
        CompanyRepositoryInterface $companyRepository,
        TaskRepositoryInterface $taskRepository,
        ProjectRepositoryInterface $projectRepository
    ) {
        $this->repository = $userRepositoryInterface;
        $this->companyRepository = $companyRepository;
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
    }

    public function getAllUsers()
    {
        return $this->repository->getAllUsers();
    }

    public function getTeamMembers($company)
    {
        return $this->repository->getTeamMembers($company);
    }

    public function findTeamMember($uuid)
    {
        return $this->repository->findTeamMember($uuid);
    }

    public function createTeamMember($request, $company)
    {
        $validatedData = Validator::make($request->all(), [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) use ($company) {
                    return $query->where('company_id', $company?->id);
                }),
            ],
            'phone_number' => ['required', 'string', 'regex:/^\+(\d\s?){10,13}$/'],
            'can_procure' => ['required', 'boolean'],
            'timezone' => ['required', 'string'],
            'location' => ['required', 'string'],
            'hourly_rate' => ['required'],
            'sick_leaves' => ['required'],
            "casual_leaves" => ['nullable'],
            "annual_leaves" => ['nullable'],
            "joining_date" => ['nullable'],
        ])->setAttributeNames([
            'can_procure' => 'designtation'
        ])->validate();
        $member =  $this->repository->storeTeamMember($validatedData, $company);
        $member->assignRole('designer');
        if ($member->can_procure) {
            $member->givePermissionTo(['can procure']);
        }

        event(new MemberRegistered($member, $company));
    }

    public function createPasswordView($request)
    {
        $token = $request->token;
        $tokenDecoded = token_decoded($token);
        $company = $this->companyRepository->findCompany(@$tokenDecoded['company_uuid']);

        $member = $this->findTeamMember(@$tokenDecoded['member_uuid']);
        $this->checkUserRole($member);

        return view('pages.auth.teams.create-password', compact('token', 'company'));
    }

    public function createPassword($request)
    {

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $tokenDecoded = token_decoded($request->token);
        $memberUuid = @$tokenDecoded['member_uuid'];

        return $this->repository->createPassword($request->password, $memberUuid);
    }

    public function getProfileMember($uuid)
    {
        $admin = currentAdmin();
        $member = $this->findTeamMember($uuid);

        if ($member->role_name == User::ROLE_DESIGNER) {
            $isAdmin = $admin->role_name == User::ROLE_ADMIN;
            $isTeamMember = $admin?->company?->teams?->contains($member);

            abort_if(($isAdmin && !$isTeamMember) || (!$isAdmin && $member->id != auth()->id()), 403);
        }

        return $member;
    }

    public function updateProfile($uuid, $data)
    {
        $member = $this->getProfileMember($uuid);
        return $this->repository->updateProfile($member, $data);
    }

    public function getMemberPojects($userUuid)
    {
        $user = $this->findTeamMember($userUuid);
        $projects =  $this->projectRepository->getPorjectsByUserId($user);
        return $projects;
    }

    public function getMemberTasks($userUuid)
    {
        $user = $this->findTeamMember($userUuid);
        $tasks =  $this->taskRepository->getTasksByUserId($user?->id);
        return $tasks;
    }

    public function deleteMember($uuid)
    {
        try {
            $this->repository->deleteMember($uuid);
        } catch (Exception $e) {

            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
        return response()->json([
            "message" => 'Profile is deleted successfully',
            'redirectedUrl' => tenant_route('teams.index'),
        ], 200);
    }
}
