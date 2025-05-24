<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterfaces\Tenant\TimesheetRepositoryInterface;
use App\Models\Timesheet;
use Illuminate\Support\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TimesheetRepository implements TimesheetRepositoryInterface
{
    protected $model;

    public function __construct(Timesheet $Timesheet)
    {
        $this->model = $Timesheet;
    }

    public function findTimesheet($uuid = '')
    {
        if (!$uuid) {
            $uuid = '';
        }

        return $this->model::findOrFailByUuid($uuid);
    }

    // public function timeLogged($request, $task)
    // {
    //     try {
    //         $startTime = Carbon::parse($request->start_time);
    //         $endTime = Carbon::parse($request->end_time);

    //     } catch (\Throwable $th) {
    //         $timeEnd = $request->end_time;
    //         $endTime = Carbon::now()->setTimeFromTimeString($timeEnd);

    //         $timeString = $request->start_time;
    //         $startTime = Carbon::now()->setTimeFromTimeString($timeString);
    //     }

    //     DB::beginTransaction();

    //     try{

    //         $currentUserId = currentUserId();
    //         $task->load(['users']);
    //         $members = $task->users->pluck('id')->toArray();

    //         $memberPivot = $task->users->pluck('pivot');

    //         if($memberPivot->where('eligible_status', true)->count() === 0){
    //             DB::table('project_task_user')->where('user_id', $currentUserId)->update([
    //                 'eligible_status' => true
    //             ]);
    //         }

    //         /*if(!$task->users()->where('eligible_status', true)->where('user_id', $currentUserId)->where('project_task_id', $task->id)->exists()){
    //             return response()->json(['message' => 'Not Allowed'], 500);
    //         }*/

    //         $timeSheetData = [
    //             'start_time' => $startTime,
    //             'end_time' => $endTime,
    //         ];

    //         $total_time = $request->total_time;

    //         $mappedTimesheetData = array_map(function ($member) use ($timeSheetData, $total_time) {

    //             $innerArr = [];
    //             if ($total_time > 0) {
    //                 // Convert total_time from seconds to minutes
    //                 $totalMinutes = round($total_time / 60, 2);  
    //                 $innerArr['total_time'] = (string) $totalMinutes;
    //             }
    //             $innerArr['user_id'] = $member;

    //             return array_merge($innerArr, $timeSheetData);
    //         }, $members);

    //         // Create the timesheet entry
    //         $timesheets = $task->timesheet()->createMany($mappedTimesheetData);

    //         $task->timer_mode = $request->get('timer_mode', 'idle');
    //         $task->save();

    //         $timesheetsUuids = $timesheets->pluck('uuid')->toArray();

    //         DB::commit();

    //         if($timesheets){
    //             return response()->json([
    //                 'timesheet_uuids' => $timesheetsUuids,
    //                 'message' => 'Time logged successfully'
    //             ], 200);
    //         }
    //         return response()->json(['message' => 'Time logged error'], 500);

    //     }  catch(\Exception $e) {

    //         DB::rollBack();
    //         return response()->json(['message' => $e->getMessage()], 500);
    //     }
    // }
    public function timeLogged($request, $task)
    {
        try {
            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);
        } catch (\Throwable $th) {
            $timeEnd = $request->end_time;
            $endTime = Carbon::now()->setTimeFromTimeString($timeEnd);

            $timeString = $request->start_time;
            $startTime = Carbon::now()->setTimeFromTimeString($timeString);
        }

        DB::beginTransaction();

        try {
            $currentUserId = currentUserId(); // Get the current logged-in user's ID

            $task->load(['users']); // Load users associated with the task

            // Check if the current user is assigned to the task
            $isUserPartOfTask = $task->users->pluck('id')->contains($currentUserId);
            if (!$isUserPartOfTask) {
                return response()->json(['message' => 'You are not assigned to this task.'], 403);
            }

            // Ensure eligible_status is set for the current user
            $task->users()->updateExistingPivot($currentUserId, [
                'eligible_status' => true,
            ]);

            $isEligible = $task->users()
                ->wherePivot('eligible_status', true)
                ->where('users.id', $currentUserId) // Specify the table name for the `id` column
                ->exists();


            if (!$isEligible) {
                return response()->json(['message' => 'Not allowed to log time for this task.'], 403);
            }

            // Prepare the timesheet data for the current user
            $timeSheetData = [
                'start_time' => $startTime,
                'end_time' => null,
                'user_id' => $currentUserId,
            ];

            if ($request->total_time > 0) {
                // Convert total_time from seconds to minutes
                $totalMinutes = round($request->total_time / 60, 2);
                $timeSheetData['total_time'] = (string) $totalMinutes;
            }

            // Create a single timesheet entry for the current user
            // $timesheet = $task->timesheet()->create($timeSheetData);
            $timesheet = $task->timesheet()->create($timeSheetData);
            // Update the task's timer mode
            $task->timer_mode = $request->get('timer_mode', 'idle');
            $task->save();
            // $timesheet = $timesheet->pluck('uuid')->toArray();
            DB::commit();
            if ($timesheet) {
                $timesheetUuids[] = $timesheet->uuid;
                return response()->json([
                    'timesheet_uuids' => $timesheetUuids,
                    'message' => 'Time logged successfully',
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // public function getTimesheets(User $user)
    // {
    //     $userId = $user->id;
    //     $companyId = $user->company_id;

    //     $timesheet = $this->model::query();
    //     $timesheet->whereNotNull('total_time');

    //     if ($user->hasRole(User::ROLE_ADMIN)) {

    //         $timesheet->where(function ($tQuery) use ($companyId) {
    //             $tQuery->whereHas('tasks.project', function ($q) use ($companyId) {
    //                 $q->where('projects.company_id', $companyId);
    //             });
    //         });
    //     } else {

    //         $timesheet->where('user_id', $userId);
    //         $timesheet->where(function ($tQuery) use ($userId) {
    //             $tQuery->whereHas('task.users', function ($q) use ($userId) {
    //                 $q->where('users.id', $userId); // Get time entries of tasks assigned to the user
    //             });
    //         });
    //     }

    //     $timesheets = $timesheet
    //         ->with(['task' => fn($q) => $q->with(['project', 'users', 'status', 'stage'])])
    //         ->get();

    //     return $timesheets->sortByDesc('created_at');
    // }
    public function getTimesheets(User $user, $page, $perPage, $startDate, $endDate)
    {
        $userId = $user->id;
        $companyId = $user->company_id;

        $timesheetQuery = $this->model::query();
        $timesheetQuery->whereNotNull('total_time');

        // Filter for admin or user
        if ($user->hasRole(User::ROLE_ADMIN)) {
            $timesheetQuery->where(function ($tQuery) use ($companyId) {
                $tQuery->whereHas('tasks.project', function ($q) use ($companyId) {
                    $q->where('projects.company_id', $companyId);
                });
            });
        } else {
            $timesheetQuery->where('user_id', $userId);
            $timesheetQuery->where(function ($tQuery) use ($userId) {
                $tQuery->whereHas('task.users', function ($q) use ($userId) {
                    $q->where('users.id', $userId);
                });
            });
        }
        // Filter by date range if provided
        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->format('Y-m-d H:i:s');
            $endDate = Carbon::parse($endDate)->format('Y-m-d H:i:s');
        
            // Debugging
            // dd($startDate, $endDate, perPage, $timesheetQuery);
            $timesheetQuery->whereBetween('start_time', [$startDate, $endDate]);
        }


        // Return paginated timesheets
        return $timesheetQuery->with(['task' => fn($q) => $q->with(['project', 'users', 'status', 'stage'])])
            ->paginate($perPage, ['*'], 'page', $page);
    }


    public function updateTimeLogged($request, $task)
    {
        $currentUserId = currentUserId(); // Get the current logged-in user's ID

        $task->load(['users']); // Load users associated with the task

        // Check if the current user is assigned to the task
        $isUserPartOfTask = $task->users->pluck('id')->contains($currentUserId);
        if (!$isUserPartOfTask) {
            return response()->json([
                'error' => true,
                'message' => 'You are not assigned to this task.'
            ], 200);
        }
        $endTimeRequest =  isset($request->end_time) ? $request->end_time : ($request->get('end_time') ? $request->get('end_time') : '');
        try {
            $endTime = Carbon::parse($endTimeRequest);
        } catch (\Throwable $th) {

            $timeEnd = $endTimeRequest;
            $endTime = Carbon::now()->setTimeFromTimeString($timeEnd);
        }

        DB::beginTransaction();

        try {

            $timesheet_uuids = $request->get('timesheet_uuids', []);
            $timesheets = $this->model::whereIn('uuid', $timesheet_uuids);
            $timesheets = $timesheets->pluck('id')->toArray();

            $timeSheetData = [
                'end_time' => $endTime,
            ];
            $totalTime = isset($request->total_time) ? $request->total_time : ($request->get('total_time') ? $request->get('total_time') : 0);

            $total_time = $totalTime;
            if ($total_time > 0) {
                // Convert total_time from seconds to minutes
                $totalMinutes = round($total_time / 60, 2);
                $timeSheetData['total_time'] = (string) $totalMinutes;
            }
            // update the timesheet entry
            $updateStatus = $this->model->whereIn('id', $timesheets)->update($timeSheetData);

            $task->timer_mode = $request->get('timer_mode', 'idle');
            $task->save();

            DB::commit();

            if ($updateStatus) {
                return response()->json([
                    'timesheet_uuids' => $timesheet_uuids,
                    'message' => 'Time logged successfully'
                ], 200);
            }
            return response()->json(['message' => 'Time logged error'], 500);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['message' => 'Time logged error'], 500);
        }
    }

    public function deleteTimeEntry($request)
    {
        $uuid = $request->uuid;
        $timeEntry = $this->model::findOrFailByUuid($uuid);
        return $timeEntry->delete();
    }

    public function addTimeEntry($data, $task)
    {
        try {
            // Combine the date and time fields
            if($data['start_date'] && $data['end_date']){
                $startDate = Carbon::parse($data['start_date']);
                $endDate = Carbon::parse($data['end_date']);
    
                // Combine date with time
                $startTime = $startDate->setTimeFromTimeString($data['start_time']);
                $endTime = $endDate->setTimeFromTimeString($data['end_time']);
            }else{
                $startTime = Carbon::parse($data['start_time']);
                $endTime = Carbon::parse($data['end_time']);    
            }
           
        } catch (\Throwable $th) {

            $timeEnd = $data['end_time'];
            $endTime = Carbon::now()->setTimeFromTimeString($timeEnd);

            $timeString = $data['start_time'];
            $startTime = Carbon::now()->setTimeFromTimeString($timeString);
        }
        $totalTime = $startTime->diffInMinutes($endTime);

        $timeSheetData = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'project_task_id' => $task->id,
            'is_automatic' => false,
            'total_time' => (float) $totalTime
        ];

        $task->load(['users']);
        $members = $task->users->pluck('id')->toArray();

        $mappedTimesheetData = array_map(function ($member) use ($timeSheetData) {
            $innerArr = [];
            $innerArr['user_id'] = $member;
            return array_merge($innerArr, $timeSheetData);
        }, $members);

        // Create the timesheet entry
        $timesheet = $task->timesheet()->createMany($mappedTimesheetData);

        if ($timesheet) {
            session()->flash('message', 'Time entry added successfully');
            return response()->json([
                'error' => false,
                'message' => 'Time entry added successfully'
            ], 200);
        }
        return response()->json(['error' => true, 'message' => 'Time logged error'], 500);
    }
    public function getTimeSheetData($userId, $taskId)
    {
        $timesheet = $this->model::where('user_id', $userId)
            ->where('project_task_id', $taskId)
            ->whereNull('end_time')
            ->orderBy('id', 'desc')
            ->select('uuid', 'start_time')
            ->first();

        return $timesheet;
    }
}
