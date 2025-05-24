@props([
    'users' => [],
    'currency' => [],
])

@push('styles')
    <style>
        th {
            text-transform: capitalize;
        }
    </style>
@endpush

<table class="table" id="">
    <thead>
        <tr>
            <th>Team Member</th>
            <th>Hourly Rate</th>
            <th>Time Logged</th>
            <th>Total Cost</th>
            <th>Stage's Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr class="table-body">
                <td class="d-none"></td>
                <td>{{ $user['full_name'] }}</td>
                <td>{{ $costingFinancialData['projectWithProcurementBudget']['currency']['symbol'] ?? '£' }}{{ $user['hourly_rate'] }}
                </td>
                <td>{{ $user['total_user_hours_logged'] }}</td>
                <td>{{ $currency['symbol'] ?? '£' }}{{ $user['user_earned'] }}</td>
                <td style="text-transform: capitalize;">{{ $user['stage_status'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
