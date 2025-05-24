@extends('layouts.tenant.index')

@section('title', 'Timesheet')

@section('content')
    <!-- Main inner content -->
    <timesheet
        :timesheets="{{ json_encode($timesheets->items()) }}" 
        :pagination="{{ json_encode([
            'currentPage' => $timesheets->currentPage(),
            'lastPage' => $timesheets->lastPage(),
            'total' => $timesheets->total(),
            'perPage' => $timesheets->perPage(),
        ]) }}"
        :members="{{ json_encode($memberList) }}" 
        :member-projects="{{ json_encode($memberProjects) }}"
    ></timesheet>
    <!-- main content area end -->
@endsection