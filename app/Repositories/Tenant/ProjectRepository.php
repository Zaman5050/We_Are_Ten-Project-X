<?php

namespace App\Repositories\Tenant;

use App\Http\Resources\ProjectAttachmentResource;
use App\Interfaces\RepositoryInterfaces\Tenant\ProjectRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\Project;
use App\Models\ProjectAttachment;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Traits\FileHandler;
use App\Models\ProjectArea;
use App\Models\Procurement;


class ProjectRepository implements ProjectRepositoryInterface
{
    use FileHandler;

    protected $model;

    public function __construct(Project $project)
    {
        $this->model = $project;
    }

    public function findProject($uuid = '')
    {
        if (!$uuid) $uuid = '';
        $authUser = auth()->user();
        $project = $this->model::findOrFailByUuid($uuid);
        $companyAdmin = User::where('company_id', $authUser->company->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', User::ROLE_ADMIN);
            })->first();


        if ($companyAdmin && !$project->members->contains($companyAdmin->id)) {
            $project->members->push($companyAdmin);
        }

        return $project;
    }

    public function getAllProjects($params)
    {
        $authUser = auth()->user();
        if ($authUser->hasRole($authUser::ROLE_ADMIN)) {
            $q = $this->model::where('company_id', $authUser->company->id);
            if (isset($params['status'])) $q->where('status', $params['status']);
            return $q->with('members', 'tags', 'stages')->get();
        } else if ($authUser->hasRole($authUser::ROLE_DESIGNER)) {
            $q = $this->model::where('owner_id', $authUser->id);
            if (isset($params['status'])) $q->where('status', $params['status']);
            return $q->orWhere(function ($query) use ($params) {
                $query->whereIn('id', auth()->user()->projectMemberOf()->pluck('projects.id'));
                if (isset($params['status'])) $query->where('status', $params['status']);
                return $query;
            })
                ->with('members', 'tags', 'stages')->get();
        }

        return $this->model::all();
    }


    public function storeProject(array $data)
    {
        DB::beginTransaction();
        try {
            $data['code'] = $this->generateCodeFromName($data['name']);
            $data['owner_id'] = auth()->id();
            $data['company_id'] = auth()->user()->company->id;
            $data['status'] = isset($data['status']) && $data['status'] != '' ? $data['status'] : 'active';
            $project = $this->model::create($data);

            $project->stages()->createMany($data['stages']);
            $project->tags()->createMany($data['tags']);
            if (isset($data['member_ids']) && !empty($data['member_ids'])) {
                $project->members()->attach(array_column($data['member_ids'], 'id'));
            }


            if (isset($data['attachments'])) {
                $attachments = $data['attachments'];
                $this->assignAttachments($project, $attachments);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        DB::commit();
        return response()->json([
            'message' => 'Project is created successfully.',
            'alert-type' => 'success',
        ], 200);
    }

    private function assignAttachments($project, $attachments)
    {
        $project->attachments()->whereNotIn("uuid", $attachments)->delete();
        ProjectAttachment::whereIn('uuid', $attachments)->update([
            'project_id' => $project->id
        ]);
    }

    public function getActiveProjects($query = [], $status = 'active')
    {
        $authUser = auth()->user();
        if ($authUser->hasRole($authUser::ROLE_ADMIN)) {
            return $this->model::where('company_id', $authUser->company->id)->where('status', $status)->with('members', 'tags', 'stages', 'attachment')->get();
        } else if ($authUser->hasRole($authUser::ROLE_DESIGNER)) {
            $projects =  $this->model::where('owner_id', $authUser->id)->where('status', $status)
                ->orWhere(function ($query) use ($status) {
                    $query->whereIn('id', auth()->user()->projectMemberOf()->pluck('projects.id'))
                        ->where('status', $status);
                })
                ->with('members', 'tags', 'stages', 'attachment')
                ->get();

            // Append the company admin
            $companyAdmin = User::where('company_id', $authUser->company->id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', User::ROLE_ADMIN);
                })->first();

            foreach ($projects as $project) {

                if ($companyAdmin && !$project->members->contains($companyAdmin->id)) {
                    $project->members->push($companyAdmin);
                }
            }

            return $projects;
        }

        return $this->model::all();
    }

    public function getCompletedProjects($query = [])
    {

        $authUser = auth()->user();
        if ($authUser->hasRole($authUser::ROLE_ADMIN)) {
            return $this->model::where('company_id', $authUser->company->id)->where('status', 'completed')->with('members', 'tags', 'stages')->get();
        } else if ($authUser->hasRole($authUser::ROLE_DESIGNER)) {
            return $this->model::where('owner_id', $authUser->id)->where('status', 'completed')
                ->orWhere(function ($query) {
                    $query->whereIn('id', auth()->user()->projectMemberOf()->pluck('projects.id'))
                        ->where('status', 'completed');
                })
                ->with('members', 'tags', 'stages')
                ->get();
        }

        return $this->model::all();
    }

    public function getDelayedProjects($query = [])
    {
        $authUser = auth()->user();
        if ($authUser->hasRole($authUser::ROLE_ADMIN)) {
            return $this->model::where('company_id', $authUser->company->id)->where('status', 'delayed')->with('members', 'tags', 'stages')->get();
        } else if ($authUser->hasRole($authUser::ROLE_DESIGNER)) {
            return $this->model::where('owner_id', $authUser->id)->where('status', 'delayed')
                ->orWhere(function ($query) {
                    $query->whereIn('id', auth()->user()->projectMemberOf()->pluck('projects.id'))
                        ->where('status', 'delayed');
                })
                ->with('members', 'tags', 'stages')
                ->get();
        }

        return $this->model::all();
    }

    public function getArchievedProjects($query = [])
    {
        $authUser = auth()->user();
        if ($authUser->hasRole($authUser::ROLE_ADMIN)) {
            return $this->model::where('company_id', $authUser->company->id)->where('status', 'archieved')->with('members', 'tags', 'stages')->get();
        } else if ($authUser->hasRole($authUser::ROLE_DESIGNER)) {
            return $this->model::where('owner_id', $authUser->id)->where('status', 'archieved')
                ->orWhere(function ($query) {
                    $query->whereIn('id', auth()->user()->projectMemberOf()->pluck('projects.id'))
                        ->where('status', 'archieved');
                })
                ->with('members', 'tags', 'stages')
                ->get();
        }

        return $this->model::all();
    }

    private function generateCodeFromName($name)
    {
        // Convert the name to a URL-friendly slug
        $code = Str::slug($name);

        // Ensure the code is unique within the database
        $originalCode = $code;
        $count = 1;

        while ($this->model::where('code', $code)->exists()) {
            $code = $originalCode . '-' . $count;
            $count++;
        }

        return $code;
    }

    public function updateProject(array $data, $uuid)
    {
        DB::beginTransaction();
        try {
            $project = $this->model::where('uuid', $uuid)->firstOrFail();
            $project->fill(Arr::except($data, ['member_ids', 'tags', 'stages']));
            $project->save();

            $this->syncProjectStages($project, $data);
            $this->syncProjectTags($project, $data);
            if (isset($data['member_ids']) && !empty($data['member_ids'])) {
                $project->members()->sync(array_column($data['member_ids'], 'id'));
            }
            if (isset($data['member_ids'])) {
                if (!empty($data['member_ids'])) {
                    $project->members()->sync(array_column($data['member_ids'], 'id'));
                } else {
                    $project->members()->detach();
                }
            }
            if (isset($data['attachments'])) {
                $attachments = $data['attachments'];
                $this->assignAttachments($project, $attachments);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        DB::commit();
        return response()->json([
            'message' => 'Project is updated successfully.',
            'alert-type' => 'success',
        ], 200);
    }

    private function syncProjectStages($project, $data)
    {
        $currentStages = $project->stages()->pluck('uuid')->toArray();

        // Get requested tag UUIDs
        $requestedStageIds = collect($data['stages'])->pluck('uuid')->toArray();

        // Determine which stages to delete
        $stagesToDelete = array_diff($currentStages, $requestedStageIds);

        // Delete stages not present in the request
        if (!empty($stagesToDelete)) {
            $project->stages()->whereIn('uuid', $stagesToDelete)->delete();
        }

        // Insert new stages (ignoring duplicates)
        $project->stages()->createMany(array_filter($data['stages'], function ($stage) {
            return !isset($stage['uuid']);
        }));
    }

    private function syncProjectTags($project, $data)
    {
        $currentTags = $project->tags()->pluck('uuid')->toArray();

        // Get requested tag UUIDs
        $requestedTagIds = collect($data['tags'])->pluck('uuid')->toArray();

        // Determine which tags to delete
        $tagsToDelete = array_diff($currentTags, $requestedTagIds);

        // Delete tags not present in the request
        if (!empty($tagsToDelete)) {
            $project->tags()->whereIn('uuid', $tagsToDelete)->delete();
        }

        // Insert new tags (ignoring duplicates)
        $project->tags()->createMany(array_filter($data['tags'], function ($tag) {
            return !isset($tag['uuid']);
        }));
    }

    public function memberActiveProjects(User $user)
    {

        $query = $this->model::query();

        if ($user->hasRole($user::ROLE_ADMIN)) {
            $query->where('company_id', $user->company->id);
        } else if ($user->hasRole($user::ROLE_DESIGNER)) {
            $query->where('owner_id', $user->id)->OrwhereIn('id', $user->projectMemberOf()->pluck('projects.id'));
        }

        $projects = $query->with('tasks:id,uuid,project_id,title')->distinct()->get();
        return $projects;
    }

    public function deleteProject(string $uuid)
    {
        return $this->model::where('uuid', $uuid)->delete();
    }


    public function uploadAttachments($request)
    {
        $imageIds = [];

        $subPath = $request->get('sub_path', 'attachments');

        foreach ($request->file('attachments') as $index => $attachment) {

            $path = $this->storeMediaByHashName($attachment, $subPath);

            $imageRecord = ProjectAttachment::create([
                'original_name' => $attachment->getClientOriginalName(),
                'name' => $path, // Save the storage path
                'extension' => $attachment->getClientOriginalExtension(),
                'size' => $attachment->getSize(),
                'is_main' => true
            ]);

            return ProjectAttachmentResource::make($imageRecord);
        }
    }

    public function deleteAttachment($attachmentUuid)
    {
        $deleteStatus = false;
        try {
            $attachment = ProjectAttachment::where('uuid', $attachmentUuid)->first();
            if ($attachment) {
                $imagePath = $attachment?->name;
                $deleteStatus = ProjectAttachment::where('uuid', $attachmentUuid)->forceDelete();

                if ($this->mediaExists($imagePath) && $deleteStatus) {
                    if ($this->deleteMedia($imagePath) || $deleteStatus) {
                        return response()->json(['message' => 'File deleted successfully']);
                    } else {
                        return response()->json(['error' => 'Unable to delete the file'], 500);
                    }
                }
            }
            return response()->json(['error' => 'File not found'], 404);
        } catch (UnableToDeleteFile $e) {

            $deleteStatus = ProjectAttachment::where('uuid', $attachmentUuid)->forceDelete();

            if ($deleteStatus) {
                return response()->json([
                    'message' => 'File deleted successfully',
                    'error' => 'Unable to delete the file: ' . $e->getMessage()
                ], 200);
            }
            return response()->json(['error' => 'Unable to delete the file: ' . $e->getMessage()], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' =>  $th->getMessage()], 404);
        }
    }

    public function getProjectsWithAreasAndMeasurementUnit()
    {
        $authUser = auth()->user();
        if ($authUser->hasRole($authUser::ROLE_ADMIN)) {
            return $this->model::where('company_id', $authUser->company->id)->where('status', $this->model::STATUS_ACTIVE)
                ->with('areas', 'procurementBudget.category', 'projectMaterial', 'currency.baseCurrencyExchangeRates.quoteCurrency:id,code')->get();
        } else if ($authUser->hasRole($authUser::ROLE_DESIGNER)) {
            return $this->model::where('owner_id', $authUser->id)->orWhere(function ($query) {
                $query->whereIn('id', auth()->user()->projectMemberOf()->pluck('projects.id'));
                $query->where('status', $this->model::STATUS_ACTIVE);
                return $query;
            })->with('areas', 'procurementBudget.category', 'projectMaterial', 'currency.baseCurrencyExchangeRates.quoteCurrency:id,code')->get();
        }
        return $this->model::with('areas', 'procurementBudget.category', 'projectMaterial', 'currency.baseCurrencyExchangeRates.quoteCurrency:id,code')->get();
    }
    public function updateDelayedProjects()
    {
        $this->model::where('status', '!=', 'delayed')
            ->where('status', '!=', 'completed')
            ->where('due_date', '<', now())
            ->update(['status' => 'delayed']);
    }

    public function getPorjectsByUserId($user)
    {
        return $this->model::where('owner_id', $user->id)
            ->OrwhereIn('id', $user->projectMemberOf()->pluck('projects.id'))
            ->with('stages')
            ->get();
    }
    public function cloneProject(array $data)
    {

        DB::beginTransaction();
        try {
            $data['code'] = $this->generateCodeFromName($data['name']);
            $data['owner_id'] = auth()->id();
            $data['company_id'] = auth()->user()->company->id;

            $project = $this->model::create($data);
            $project->tags()->createMany($data['tags']);

            if (isset($data['attachments'])) {
                foreach ($data['attachments'] as $attachmentUuid) {
                    $originalAttachment = ProjectAttachment::where('uuid', $attachmentUuid)->first();
                    if ($originalAttachment) {
                        $newAttachment = $originalAttachment->replicate();
                        $newAttachment->project_id = $project->id;
                        $newAttachment->save();
                    }
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        DB::commit();
        return response()->json([
            'message' => 'Project is created successfully.',
            'alert-type' => 'success',
        ], 200);
    }
    public function updateProjectFinancials(array $data, $uuid)
    {
        DB::beginTransaction();
        try {
            $project = $this->model::where('uuid', $uuid)->firstOrFail();

            $this->syncProjectStages($project, $data ?? []);
            $this->syncProcurementBudget($project, $data['procurementBudget'] ?? []);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
        DB::commit();
        return response()->json([
            'message' => 'Project Financial Budget updated successfully.',
            'alert-type' => 'success',
        ], 200);
    }

    private function syncProcurementBudget($project, $procurementBudget)
    {
        $project->procurementBudget()->delete();
        $project->procurementBudget()->createMany($procurementBudget);
    }

    public function getProjectBudgetFinancial(string $uuid)
    {
        return $this->model::select('id', 'uuid', 'currency_id', 'is_procurement_enable', 'start_date', 'due_date')
            ->whereUuid($uuid)
            ->with('stages', 'areas', 'procurementBudget.category', 'currency.baseCurrencyExchangeRates.quoteCurrency:id,code')
            ->first();
    }


    public function projectWithCostingFinancial(string $uuid)
    {
        $data['projectWithProcurementBudget'] = $this->model::select('id', 'currency_id')->whereUuid($uuid)->with('procurementBudget.category', 'currency:id,symbol')->first();
        $getProjectUserLoggedTimeResponse = $this->getProjectUserLoggedTime($uuid);
        // Group the getProjectUserLoggedTimeResponse by stage_name
        $data['stagesBudget'] = $getProjectUserLoggedTimeResponse->groupBy('stage_name')->map(function ($rows, $stage) {
            return [
                'stage_name' => $stage,
                'users' => $rows->map(function ($row) {
                    return [
                        'full_name' => $row->full_name,
                        'hourly_rate' => $row->hourly_rate,
                        'user_id' => $row->user_id,
                        'total_user_hours_logged' => convertFromMinutes($row->total_user_hours_logged),
                        'user_earned' => $row->user_earned,
                        'stage_status' => $row->stage_status,
                    ];
                }),
            ];
        })->values();
        return $data;
    }

    public function getProjectServiceFinancial(string $uuid)
    {
        $data['projectWithCurrency'] = $this->model::select('id', 'currency_id')->whereUuid($uuid)->with('currency:id,symbol')->first();
        $getProjectUserLoggedTimeResponse = $this->getProjectUserLoggedTime($uuid);
        // Group the getProjectUserLoggedTimeResponse by stage_name
        $data['servicesBudget'] = $getProjectUserLoggedTimeResponse->groupBy('stage_name')->map(function ($rows, $stage) {
            $stageCost = 0;
            return [
                'stage_name' => $stage,
                'stage_budget' => floatval($rows[0]?->stage_budget ?? 0),
                'start_date' => $rows[0]->start_date ?? '',
                'due_date' => $rows[0]->due_date ?? '',
                'users' => $rows->map(function ($row) use (&$stageCost) { // passing stageCost by reference so it's value can be updated in use closure
                    $stageCost += floatval($row->user_earned);
                    return [
                        'full_name' => $row->full_name,
                        'hourly_rate' => $row->hourly_rate,
                        'user_id' => $row->user_id,
                        'total_user_hours_logged' => convertFromMinutes($row->total_user_hours_logged),
                        'user_earned' => $row->user_earned,
                        'stage_status' => $row->stage_status,
                    ];
                }),
                'stage_cost' => $stageCost,
                'stage_profit' => (floatval($rows[0]?->stage_budget ?? 0) - $stageCost),
            ];
        })->values();
        return $data;
    }

    private function getProjectUserLoggedTime($projectUuid)
    {
        $query = "
        WITH project_query AS (
            SELECT p.id, p.owner_id AS user_id
            FROM projects p
            WHERE p.uuid = :projectUuid
        ),
        tasks_query AS (
            SELECT pt.id AS project_task_id, pt.stage_id, pt.created_by
            FROM project_tasks pt
            WHERE pt.deleted_at IS NULL
            AND pt.project_id = (SELECT id FROM project_query)
        )

        SELECT
            CONCAT(u.first_name, ' ', u.last_name) AS full_name,
            u.hourly_rate,
            ts.user_id,
            FORMAT(SUM(ts.total_time), 2) AS total_user_hours_logged,
            FORMAT(SUM(ts.total_time) * (u.hourly_rate / 60), 2) AS user_earned,
            ps.stage_name, ps.status stage_status, ps.amount stage_budget, ps.start_date, ps.due_date
        FROM timesheets ts
        INNER JOIN tasks_query tq ON ts.project_task_id = tq.project_task_id
        INNER JOIN users u ON u.id = ts.user_id
        INNER JOIN project_stages ps ON ps.id = tq.stage_id
        WHERE ts.deleted_at IS NULL
        GROUP BY tq.stage_id, ts.user_id
        ORDER BY ps.id ASC;
    ";
        $results = DB::select($query, ['projectUuid' => $projectUuid]);
        // Convert results to a Laravel collection
        return collect($results);
    }

    public function projectWithProcurementFinancial(string $projectUuid)
    {
        $procurementQuery = Procurement::where('project_id', Project::findOrFailByUuid($projectUuid)->id);
        $data['totalProcurementProfit'] = (clone $procurementQuery)->sum('profit');
        $data['procurementBudgetData'] = (clone $procurementQuery)
            ->select('uuid', 'quote_currency_id', 'base_currency_id', 'trade_price', 'markup', 'actual_price', 'quantity', 'total_exclusive_tax', 'total_inclusive_tax', 'product_library_id', 'profit')
            ->with(['product:id,category_id,sku,product_name', 'baseCurrency', 'exchangeCurrency'])->latest()->get()->groupBy(function ($procurement) {
                return $procurement?->product?->category?->name;
            });
        return $data;
    }
}
