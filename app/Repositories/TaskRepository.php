<?php

namespace App\Repositories;

use App\Models\Task;
use App\Interfaces\RepositoryInterfaces\TaskRepositoryInterface;
use App\Models\Project;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\TaskAttachment;
use Illuminate\Support\Arr;
use App\Traits\FileHandler;
use Exception;
use League\Flysystem\UnableToDeleteFile;
use App\Http\Resources\Tenant\Tasks\TaskAttachmentResource;

class TaskRepository implements TaskRepositoryInterface
{
    use FileHandler;
    protected $model;

    public function __construct(Task $Task)
    {
        $this->model = $Task;
    }

    public function findTask($uuid = '')
    {
        if (!$uuid) $uuid = '';
        return $this->model::findOrFailByUuid($uuid);
    }

    public function storeTask($data)
    {
        DB::beginTransaction();

        $response = [
            "error" => true,
            "message" => "Somthing Went Wrong"
        ];

        try {
            $status = getStatus($data['status_uuid']);
            $stage = getStage($data['stage_uuid']);
            if (!empty($data['stage']['project_id'])) {
                $project = Project::findOrFailById($data['stage']['project_id']);
            } else {
                $project = Project::findOrFailByUuid($data['project_uuid'] ?? $data['stage']['project_id']);
            }
            $taskData = [
                "uuid" => fake()->uuid(),
                "task_code" => $this->generateTaskCode($project?->id),
                "title" => $data['title'] ?: fake()->jobTitle(),
                "description" => $data['description'] ?? '',
                "project_id" => $project?->id,
                "stage_id" => $stage?->id,
                "start_date" => $data['start_date'] ?? '',
                "status_id" => $status->id,
                "created_by" => currentUserId()
            ];
            if (!empty($data['due_date'])) {
                $taskData['due_date'] = $data['due_date'];
            }

            $task = $this->model::create($taskData);

            if (!empty($data['member'])) {
                if (isset($data['member']['uuid'])) {
                    $memberUuid = $data['member']['uuid'];
                } elseif (isset($data['member'][0]['uuid'])) {
                    $memberUuid = $data['member'][0]['uuid'];
                } else {
                    $memberUuid = '';
                }
                if ($memberUuid) {
                    $members = getMembers($memberUuid);
                    $this->assignTeamMember($task, $members);
                }
            }

            $attachments = $data['attachments'];
            $this->assignAttachments($task, $attachments);

            if (!empty($task)) {
                $response['error'] = false;
                $response['message'] = "{$task->task_code} Task created successfully";
                session()->flash('message', $response['message']);
            }
        } catch (Exception $e) {

            DB::rollBack();
            $response['message'] = $e->getMessage();
            return response()->json($response, 500);
        }

        DB::commit();

        return response()->json($response, 200);
    }

    public function updateTask($taskUuid, $data)
    {
        DB::beginTransaction();

        $response = [
            "error" => true,
            "message" => "Somthing Went Wrong"
        ];

        try {
            $status = getStatus($data['status_uuid']);
            $stage = getStage($data['stage_uuid']);
            $taskData = [
                "title" => $data['title'],
                "description" => $data['description'],
                "stage_id" => $stage->id,
                "start_date" => $data['start_date'],
                "status_id" => $status->id,
            ];

            if (!empty($data['estimate_time'])) {
                $taskData['estimate_time'] = convertToMinutes(@$data['estimate_time']);
            }

            if (!empty($data['due_date'])) {
                $taskData['due_date'] = $data['due_date'];
            }

            $task = $this->findTask($taskUuid);
            if (!$task) {
                throw new \Exception('Task not found');
            }

            $task->fill($taskData);
            $task->save();
            if (!empty($data['member'])) {
                if (isset($data['member']['uuid'])) {
                    $memberUuid = $data['member']['uuid'];
                } elseif (isset($data['member'][0]['uuid'])) {
                    $memberUuid = $data['member'][0]['uuid'];
                } else {
                    $memberUuid = '';
                }
                if ($memberUuid) {
                    $members = getMembers($memberUuid);
                    $this->assignTeamMember($task, $members);
                }
            }

            $attachments = @$data['attachments'];
            $this->assignAttachments($task, $attachments);

            if (!empty($task)) {
                $response['error'] = false;
                $response['message'] = "{$task->task_code} Task updated successfully";
                session()->flash('message', $response['message']);
            }
        } catch (Exception $e) {

            DB::rollBack();
            $response['message'] = $e->getMessage();
            return response()->json($response, 500);
        }

        DB::commit();

        return response()->json($response, 200);
    }
    public function updateTaskData($taskUuid, $data)
    {
        DB::beginTransaction();

        $response = [
            "error" => true,
            "message" => "Somthing Went Wrong"
        ];
        try {
            $taskData = [];
            if (!empty($data['estimate_time'])) {
                $taskData['estimate_time'] = convertToMinutes(@$data['estimate_time']);
            }

            // if (!empty($data['due_date'])) {
            //     $taskData['due_date'] = $data['due_date'];
            // }
            // if (!empty($data['start_date'])) {
            //     $taskData['start_date'] = $data['start_date'];
            // }

            $task = $this->findTask($taskUuid);
            if (!$task) {
                throw new \Exception('Task not found');
            }
            $task->fill($taskData);
            $task->save();

            if (!empty($data['member'])) {
                if (isset($data['member']['uuid'])) {
                    $memberUuid = $data['member']['uuid'];
                } elseif (isset($data['member'][0]['uuid'])) {
                    $memberUuid = $data['member'][0]['uuid'];
                } else {
                    $memberUuid = '';
                }
                if ($memberUuid) {
                    $members = getMembers($memberUuid);
                    $this->assignTeamMember($task, $members);
                }
            }
            $task = $this->findTask($taskUuid);

            $task->load(['timesheet' => function ($query) {
                $query->whereNotNull('total_time');
            }]);

            $sumTotalTime = $task->timesheet->sum('total_time');

            $totalTIme = $sumTotalTime > 0 ? $sumTotalTime * 60 : 0;
            if (!empty($task)) {
                $response['error'] = false;
                $response['message'] = "{$task->task_code} Task updated successfully";
                $response['total_time'] = $totalTIme;
                session()->flash('message', $response['message']);
            }
        } catch (Exception $e) {

            DB::rollBack();
            $response['message'] = $e->getMessage();
            return response()->json($response, 500);
        }

        DB::commit();

        return response()->json($response, 200);
    }
    private function generateTaskCode($project)
    {
        // $project->project_code
        $taskCount = $this->countTasksByProject($project);
        return 'PX–' . ($taskCount < 1 ? 1 : $taskCount + 1);
    }

    private function countTasksByProject($projectId)
    {
        return $this->model::where('project_id', $projectId)->count();
    }

    private function assignTeamMember($task, $members)
    {
        $membersIds = $members->pluck('id')->toArray();
        $task->users()->sync($membersIds, ['eligible_status' => false]);
    }

    private function assignAttachments($task, $attachments)
    {
        $task->attachments()->whereNotIn("uuid", $attachments)->delete();
        TaskAttachment::whereIn('uuid', $attachments)->update([
            'project_task_id' => $task->id
        ]);
    }

    public function uploadAttachments($request)
    {
        $imageIds = [];

        $subPath = $request->get('sub_path', 'attachments');

        foreach ($request->file('attachments') as $index => $attachment) {

            $path = $this->storeMediaByHashName($attachment, $subPath);

            $imageRecord = TaskAttachment::create([
                'original_name' => $attachment->getClientOriginalName(),
                'name' => $path, // Save the storage path
                'extension' => $attachment->getClientOriginalExtension(),
                'size' => $attachment->getSize(),
                // 'project_task_id' => $task->id, 
            ]);

            $imageIds[] = TaskAttachmentResource::make($imageRecord);
        }

        return $imageIds;
    }


    public function deleteAttachment($attachmentUuid)
    {
        $deleteStatus = false;
        try {
            $attachment = TaskAttachment::where('uuid', $attachmentUuid)->first();
            if ($attachment) {
                $imagePath = $attachment?->name;
                $deleteStatus = TaskAttachment::where('uuid', $attachmentUuid)->delete();
                if ($this->mediaExists($imagePath) && $deleteStatus) {
                    if ($this->deleteMedia($imagePath)) {
                        return response()->json(['message' => 'File deleted successfully']);
                    } else {
                        return response()->json(['error' => 'Unable to delete the file'], 500);
                    }
                }
            }
            return response()->json(['error' => 'File not found'], 404);
        } catch (UnableToDeleteFile $e) {

            $deleteStatus = TaskAttachment::where('uuid', $attachmentUuid)->delete();

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

    public function tasksByProject($projectId, $requestData)
    {
        $query = $this->model::where('project_id', $projectId);

        $user = currentUser();

        if ($user->hasRole(User::ROLE_DESIGNER)) {

            $userId = $user->id;
            $query->where(function ($q) use ($userId) {
                $q->whereHas('users', function ($q) use ($userId) { // users have current user id
                    $q->where('user_id', $userId);
                })
                    ->orWhereHas('users')
                    ->orWhere('created_by', $userId);  // also check created by own user
            });
        }

        $query->with(['status', 'owner', 'users', 'stage', 'project', 'attachments']);
        $query->withSum('timesheet', 'total_time');
        $tasks = $query->get()->sortByDesc('created_at');
        return $tasks;
    }

    public function tasksStatuses()
    {
        return Status::where('flag', 'task')->get();
    }

    public function updateStatus($taskUuid, $statusUuid)
    {
        $taskStatus = getStatus($statusUuid);
        $task = $this->findTask($taskUuid);

        if ($taskStatus->flag == 'task') {
            $task->status_id = $taskStatus->id;
            return $task->save();
        }
        return false;
    }

    public function markFlag($taskUuid, User $user)
    {
        $task = $this->findTask($taskUuid);
        $task->flaged_by = $task->flaged_by == $user->id ? null : $user->id;
        $task->save();
        return $task;
    }

    public function markArchived($request)
    {
        $taskUuid = $request->taskUuid;
        $status = (bool)$request->status;
        $task = $this->findTask($taskUuid);
        $task->archive = !$status;
        return $task->save();
    }

    public function deleteTask(Task $task)
    {
        return $task->delete();
    }

    public function updateTimerStatus($request)
    {
        $task = $this->findTask($request->taskUuid);
        $task->timer_mode = $request->timer_mode;
        return $task->save();
    }

    public function getTasksByProjectIdAndUserId($projectId, $userId)
    {
        return $this->model::where('project_id', $projectId)
            ->where(function ($query) use ($userId) {
                // Either the task is created by the user or the user is related to the task
                $query->where('created_by', $userId)
                    ->orWhereHas('users', function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    });
            })
            ->get();
    }

    public function getTasksByUserId($userId)
    {
        return $this->model::where('created_by', $userId)->with('status', 'project:id,uuid')
            ->withSum(['timesheet as total_time_logged' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }], 'total_time')->get();
    }
}
