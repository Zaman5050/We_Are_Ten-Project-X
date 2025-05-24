@extends('layouts.tenant')

@section('title', 'Leave Listing')

@section('content')
    <div class="sales-report-area">
        <div class="dashboard-heading-container pb--40 d-flex justify-content-between">
            <h1 class="dashboard-main-heading mb-0">Leave Listing</h1>
            <div class="d-flex gap-3">
                <a class="filter-btn" data-bs-toggle="modal" data-bs-target="#apply-leave-popup">Apply For Leave</a>
            </div>
        </div>

        @if($leaves->isEmpty())
            <div class="alert alert-info">
                No leave requests found.
            </div>
        @else
            <div class="dashboard-table-container">
                <table class="table mb-0 dashboard-table">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaves as $leave)
                            <tr>
                                <td>{{ $leave->leave_type }}</td>
                                <td>{{ $leave->start_date->format('d/m/Y') }}</td>
                                <td>{{ $leave->end_date->format('d/m/Y') }}</td>
                                <td>{{ $leave->notes }}</td>
                                <td>
                                    <span class="{{ $leave->status }}">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#approveLeaveModal{{ $leave->id }}"
                                       data-leave-type="{{ $leave->leave_type }}"
                                       data-start-date="{{ $leave->start_date->format('d/m/Y') }}"
                                       data-end-date="{{ $leave->end_date->format('d/m/Y') }}"
                                       data-reason="{{ $leave->notes }}">Approve</a>
                                    <form action="#">
                                        @csrf
                                        @method('DELETE') <!-- Use DELETE for declining -->
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this leave request?');">Decline</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Approve Leave Modal -->
                            <div class="modal fade" id="approveLeaveModal{{ $leave->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="approveLeaveModalLabel{{ $leave->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body pt-0">
                                            <h5 class="modal-title">Approve Leave Request</h5>
                                            <form action="{{ route('leaves.approve', $leave->id) }}" method="POST">
                                                @csrf
                                                <!-- Leave Details Pre-filled in the Modal -->
                                                <div class="mb-3">
                                                    <label class="signup-labels">Leave Type:</label>
                                                    <input type="text" name="leave_type" class="form-control" value="{{ $leave->leave_type }}" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="signup-labels">Start Date:</label>
                                                    <input type="text" name="start_date" class="form-control" value="{{ $leave->start_date->format('d/m/Y') }}" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="signup-labels">End Date:</label>
                                                    <input type="text" name="end_date" class="form-control" value="{{ $leave->end_date->format('d/m/Y') }}" readonly>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="signup-labels">Reason:</label>
                                                    <textarea name="notes" class="form-control" readonly>{{ $leave->notes }}</textarea>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button style="width: 122px;" type="submit" class="apply-leave-btn">Approve</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @includeIf('popups.apply-leave') <!-- Modal for applying leave -->
@endsection

@section('scripts')
    <script>
        // Script to handle the modal data population
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function () {
                const leaveType = this.getAttribute('data-leave-type');
                const startDate = this.getAttribute('data-start-date');
                const endDate = this.getAttribute('data-end-date');
                const reason = this.getAttribute('data-reason');

                const modal = document.getElementById(this.getAttribute('data-bs-target').substring(1));

                modal.querySelector('input[name="leave_type"]').value = leaveType;
                modal.querySelector('input[name="start_date"]').value = startDate;
                modal.querySelector('input[name="end_date"]').value = endDate;
                modal.querySelector('textarea[name="notes"]').value = reason;
            });
        });
    </script>
@endsection
