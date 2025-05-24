@props([
'leaves' => [],
])


<table class="table" id="leave-history">
    <thead>
        <tr>
            <th>Leave Type</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Reason</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($leaves as $leave)
        <tr>
            <td>{{ $leave->leave_type }}</td>
            <td>{{ $leave->start_date }}</td>
            <td>{{ $leave->end_date }}</td>
            <td>{{ $leave->notes }}</td>
            <td>
                <span
                    class="{{ $leave->status }}"
                    style="color: {{ $leave->status == 'approved' ? 'green' :
                ($leave->status == 'declined' ? 'red' : 'inherit') }}">
                    {{ ucfirst($leave->status) }}
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>