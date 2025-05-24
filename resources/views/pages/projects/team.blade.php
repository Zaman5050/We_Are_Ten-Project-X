@extends('layouts.tenant.project')

@section('title', 'Project Team')

@section('project-content')

<div class="dashboard-table-container">
    <table class="table mb-0 dashboard-table">
        <tr>
            <th>Full Name</th>
            <th>Email Address</th>
            <th>Phone Number</th>
            <th>Role</th>
            <th>Location</th>
            <th>Time Zone</th>
            <th width="190px">Upcoming Leave</th>
        </tr>
        <tr
        @if(isset($project->owner))
            onclick="tableTr('{{ $project->owner->uuid }}', '{{ tenant_route('profile.edit', ['uuid' => $project->owner->uuid]) }}', '{{ tenant_route('teams.show', ['uuid' => $project->owner->uuid]) }}')" 
            style="{{ auth()->user()->role_name == 'admin' ? 'cursor: pointer;' : '' }}" 
        @endif
        >
           <td class="{{ auth()->user()->role_name == 'admin' ? 'text-decoration-underline' : '' }}">{{ $project->owner?->full_name }}</td>
            <td>{{ $project->owner?->email }}</td>
            <td>{{ $project->owner?->phone_number }}</td>
            @if ($project->owner)
                 <td> <span class="text-capitalize">Project Owner</span> </td>

            @endif
            <td>{{ $project->owner?->location }}</td>
            <td>{{ $project->owner?->timezone }}</td>
            <td> 
                @php
                    $displayedLeaveRanges = [];
                @endphp

                @if(count($project->owner?->upComingLeaves ?? []) > 0)
                    @foreach($project->owner?->upComingLeaves ?? [] as $leave)
                        @php
                            $leaveRange = $leave->start_date === $leave->end_date 
                                ? $leave->start_date 
                                : $leave->start_date . ' - ' . $leave->end_date;
                        @endphp

                        @if(!in_array($leaveRange, $displayedLeaveRanges))
                            {{ $leaveRange }}
                            @php
                                $displayedLeaveRanges[] = $leaveRange;
                            @endphp
                            @break
                        @endif
                    @endforeach
                @endif
            </td>
        </tr>

        @foreach ($project->members as $member)
        <tr onclick="tableTr('{{ $member->uuid }}', '{{ tenant_route('profile.edit', ['uuid' => $member->uuid]) }}', '{{ tenant_route('teams.show', ['uuid' => $member->uuid]) }}')" style="{{ auth()->user()->role_name == 'admin' ? 'cursor: pointer;' : '' }}" >
            <td class="{{ auth()->user()->role_name == 'admin' ? 'text-decoration-underline' : '' }}">{{ $member?->full_name }}</td>
            <td>{{ $member?->email }}</td>
            <td>{{ $member?->phone_number }}</td>
            <td> <span class="text-capitalize" > {{ $member::ROLE_DESIGNER }} </span> - <span>{{ $member->can('can procure') ? '(can procure)': '(cannot procure)' }}</td>
            <td>{{ $member?->location }}</td>
            <td>{{ $member?->timezone }}</td>
            <td> 
                @php
                    $displayedLeaveRanges = [];
                @endphp

                @if(count($member->upComingLeaves) > 0)
                    @foreach($member->upComingLeaves as $leave)
                        @php
                            $leaveRange = $leave->start_date === $leave->end_date 
                                ? $leave->start_date 
                                : $leave->start_date . ' - ' . $leave->end_date;
                        @endphp

                        @if(!in_array($leaveRange, $displayedLeaveRanges))
                            {{ $leaveRange }}
                            @php
                                $displayedLeaveRanges[] = $leaveRange;
                            @endphp
                            @break
                        @endif
                    @endforeach
                @else
                    ---
                @endif
            </td>
        </tr>
        @endforeach

    </table>
</div>


@endsection

@push('scripts')

<script>
    function tableTr(uuid, profileUrl, teamUrl) {
        if(@json(auth()->user()->role_name) == 'admin'){
            var userUuid = @json(auth()->user()->uuid); // Passing the user UUID from Blade to JS
            var redirectUrl = (uuid == userUuid) ? profileUrl : teamUrl;
            window.location.href = redirectUrl;
        }
    }
</script>
@endpush('scripts')