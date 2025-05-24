@extends('layouts.tenant.project')

@section('title', 'Project Dashboard')

@section('project-content')
@push('styles')
<style>
nav div:first-of-type {
    display: none !important;
}

</style>
@endpush
<div class="project-details-main-container">
    <div class="project-name-dashboard-container">
        <x-project.dashboard-details :project="$data['project']" />
    </div>
    <div class="project-costing d-none">
        <h3 class="mb-3 total-projects">Project Costing</h3>
        <div class="d-flex justify-content-between mb-2">
            <h3 class="total-projects">Total Budget</h3>
            <span class="">$10,000</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <h3 class="total-projects">Procurement Cost</h3>
            <span class="">$10,000</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <h3 class="total-projects">Profit</h3>
            <span class="">$10,000</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <h3 class="total-projects">Remaining...</h3>
            <span class="tags bg-green">$10,000</span>
        </div>
    </div>
</div>
<div class="row">
    @if(count($data['myTasks']) > 0)
    <div class="col-md-6">
        <div class="project-dashboard-table-container" data-section="my-tasks">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="t-heading">My Tasks</h1>
                <a href="{{ tenant_route('task.index', [
                    'projectUuid' => $data['project']->uuid,
                    'assignee' => auth()->user()->uuid,
                    ]) }}" class="px-4 text-black see-more">
                    <u> See More </u>
                </a>
            </div>
            
            <x-project.task-table :tasks="$data['myTasks']" />
            <div class="text-center mt-3">
                 <button class="btn btn-primary load-more-btn mb-3">Load More</button>
            </div>
        </div>
    </div>
    @endif

    @if(count($data['project']->tasks) > 0)
    <div class="col-md-6">
        <div class="project-dashboard-table-container" data-section="all-tasks">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="t-heading">All Tasks</h1>
                <a href="{{ tenant_route('task.index', [
                    'projectUuid' => $data['project']->uuid,
                    ]) }}" class="px-4 text-black see-more">
                    <u> See More </u>
                </a>
            </div>
            <x-project.task-table :tasks="$data['project']->tasks" :shouldTaskUsersVisible="true" />
                <div class="text-center mt-3">
                    <button class="btn btn-primary load-more-btn mb-3">Load More</button>
               </div>
        </div>
    </div>
    @endif
</div>



@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".project-dashboard-table-container").forEach(container => {
        let currentVisible = 10;
        const tasks = container.querySelectorAll(".task-row");
        const loadMoreBtn = container.querySelector(".load-more-btn");

        // Hide tasks after the first 10
        tasks.forEach((task, index) => {
            if (index >= 10) {
                task.style.display = "none";
            }
        });

        if (tasks.length <= 10) {
            loadMoreBtn.style.display = "none";
        }

        loadMoreBtn?.addEventListener("click", function () {
            let nextLimit = currentVisible + 10;
            for (let i = currentVisible; i < nextLimit && i < tasks.length; i++) {
                tasks[i].style.display = "table-row";
            }
            currentVisible += 10;

            if (currentVisible >= tasks.length) {
                loadMoreBtn.style.display = "none";
            }
        });
    });
});
</script>
@endpush



@endsection
