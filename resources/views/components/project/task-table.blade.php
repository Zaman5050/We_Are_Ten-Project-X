@props([
'tasks' => [],
'members' => [],
'shouldTaskUsersVisible' => false,
])

<table class="table">
    <tr>
        <th>ID</th>
        <th>Task Title</th>
        <th>Stage</th>
        <th>Completion</th>
        @if($shouldTaskUsersVisible)
        <th>Members</th>
        @endif
    </tr>
    @foreach($tasks as $index => $task)
    <tr class="task-row"> <!-- Add this class -->
        <td>{{ $index + 1 }}</td> 
        <td>{{ $task->title }}</td>
        <td>
            @if($task?->stage)
                <span style="background: #F8B44E;" class="tags">{{ $task?->stage?->stage_name }}</span>
            @else
                ---
            @endif
        </td>
        <td>
            <div class="container" style="display: flex; align-items: center; gap:10px">
                @if (intval($task->completion_percentage) == 100)
                    <img src="{{ asset('assets/images/chart-container.png') }}" alt="Completion Exclamation" class="completion-image">
                @else
                    <div class="circle x-small" data-fill="{{ intval($task->completion_percentage) }}" style="--color: #252C32;">
                        <div class="bar"></div>
                    </div>
                @endif
                <span style="@if (intval($task->completion_percentage) == 100) color: #F8624E; @endif"> {{ $task->completion_percentage }} %</span>
            </div>
        </td>
        @if($shouldTaskUsersVisible)
        <td>
            <member-avatar-list :users="{{ $task->users }}" :size="{{ count($task->users) }}"
                :uindex="{{$task->id}}"></member-avatar-list>
        </td>
        @endif
    </tr>
    @endforeach
</table>
