@props([
'projects' => [],
])

<div class="dashboard-table-container">
    <table class="table mb-0 dashboard-table" id="myTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Project Name</th>
                <th>Project Type</th>
                <th>Time Logged</th>
                <th>Stage</th>
                <th>Completion</th>
            </tr>
        </thead>
        <tbody>        
            @foreach ($projects as $project)                
            <tr>
                <td>{{ $project->short_name }}</td>
                <td>
                    <a class="text-black project-name" href="{{ tenant_route('projects.show', ['project' => $project->uuid]) }}">
                        {{ $project->name }}
                    </a>
                </td>
                <td>{{ $project->type }}</td>
                <td>{{ $project->project_logged_time['timeLogged'] ?? '' }}</td>
                <td><span class="stage text-uppercase">{{ $project->stages[0]->stage_name ?? '-' }}</span></td>
                <td>
                    <div class="container" style="display: flex; align-items: center; gap: 10px;">
                        <div class="circle x-small" data-fill="0" style="--color: #252C32;">
                            <div class="bar"></div>
                        </div>
                        <span style="position: relative;"><span>{{ $project->project_completion_percentage }}%</span></span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>