<div class="dashboard-table-container">
    <table class="table mb-0 dashboard-table" id="myTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Project Name</th>
                <th>Project Type</th>
                <th>Address</th>
                <th>Currency </th>
                <th>Stage</th>
                <th>Completion</th>
            </tr>
        </thead>
        @foreach ($projects as $project)
        <tbody>
            <tr>
                <td>{{ $project->id }}</td>
                <td><u><a class="text-black text-decoration-none" href="{{ tenant_route('projects.show',['project' => $project->uuid]) }}">{{ $project->name }}</a></u></td>
                <td>{{ $project->type }}</td>
                <td>{{ $project->address }}</td>
                <td> <span class="mx-3">{{ $project->currency->symbol}}</span></td>
                <td> <span class="stage text-uppercase text-decoration-underline">{{ $project->stages[0]->stage_name ?? '' }}</span></td>
                <td>
                    <div class="container" style="display: flex; align-items: center; gap:10px">
                        <div class="circle x-small" data-fill="{{ intval($project->project_completion_percentage) }}" style="--color: #252C32;">
                            <div class="bar"></div>
                        </div>
                        <span style="position: relative;">
                            <span>{{ $project->project_completion_percentage }}%</span>
                        </span>
                    </div>
                </td>
            </tr>
        </tbody>
        @endforeach
    </table>
</div>
