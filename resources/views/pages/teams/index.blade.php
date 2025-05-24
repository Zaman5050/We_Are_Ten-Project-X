@extends('layouts.tenant.index')

@section('title', 'Teams')

@section('content')
    <!-- Main inner content -->

    <div class="sales-report-area">
        <div class="dashboard-heading-container pb--40 d-flex justify-content-between">
            <h1 class="dashboard-main-heading mb-0">Teams</h1>
            <div class="d-flex gap-3">
                <div class="dropdown import-drop-down">
                    <a href="{{ tenant_route('teams.create') }}" class="filter-btn import-team-member bg-yellow text-decoration-underline">Invite Team Member</a>
                </div>
            </div>
        </div>

        <div class="dashboard-table-container">
            <table class="table mb-0 dashboard-table">
                <tr>
                    <th width="150px">Full Name</th>
                    <th width="110px">Role</th>
                    <th width="190px">Location</th>
                    <th>Time Zone</th>
                    <th>Active Projects</th>
                    <th width="200px">Upcoming Leave</th>
                    <th width="150px">Total Time Logged</th>
                </tr>
                @foreach ($teams as $team)
                    <tr onclick="tableTr('{{ $team->uuid }}')" style="cursor: pointer;" class="leave-tab-tr">
                        <td class="text-decoration-underline">{{ $team->full_name }}</td>
                        <td class="role-name">{{ $team?->role_name .' - ('. ($team->can_procure ? 'Can procure' : 'Not procure').')' }}</td>
                        <td>{{ $team->location }}</td>
                        <td>{{ $team->timezone }}</td>
                        <td class="text-center"> {{ $team->member_active_projects_count ?: '0' }} </td>
                        <td> 
                            @php
                                $displayedLeaveRanges = [];
                            @endphp
                            @if(count($team->upComingLeaves) > 0)
                            @foreach($team->upComingLeaves as $leave)
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
                        <td> 
                            {{ $team->total_time_logged ? convertFromMinutes($team->total_time_logged) : '---' }} </td>
                    </tr>
                @endforeach
            </table>
        </div>

    </div>

    @includeIf('popups.create-team-member')


    <!-- main content area end -->
@endsection

@push('scripts')
<script>
    function tableTr(uuid){
        var redirectUrl = "{{ tenant_route('teams.show', [ 'uuid' => 'uuid']) }}".replace('uuid', uuid);
        // console.log('ok', redirectUrl);
        window.location.href =redirectUrl ;
    }
</script>
@endpush('scripts')
