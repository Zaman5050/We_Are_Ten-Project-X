@props([
'tasks' => [],
])

<div class="task-table-container mw-760">
    <table class="table">
        <thead>
            <tr>
                <th>Task title</th>
                <th>Start date</th>
                <th>End date</th>
                <th>Time logged</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ( $tasks as $task )
            <tr class="table-body">
                <td class="d-none"></td>
                <td>
                    <a class="text-black project-name" href="{{ tenant_route('task.index', ['projectUuid' => $task?->project?->uuid]) }}">
                        {{ $task->title }}
                    </a>
                </td>
                <td>{{ $task->start_date }}</td>
                <td>{{ $task->due_date }}</td>
                <td>{{ formatSecondsToTimeString($task->total_time_logged) ?? '-' }}</td>
                <td><span class="review-status">{{ $task->status?->title }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
