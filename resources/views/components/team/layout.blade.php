@props([
    'member' => [],
    'activeProjectCount' => 0,
])
<div class="sales-report-area">
    <div class="dashboard-heading-container pb--40 d-flex justify-content-between">
        <h1 class="dashboard-main-heading mb-0">{{ $member?->full_name }}</h1>
        <div class="d-flex gap-3">
            <div class="dropdown import-drop-down">
                <a href="{{ tenant_route('member.profile.edit', ['uuid' => $member->uuid]) }}"
                    class="filter-btn import-team-member bg-yellow text-decoration-underline">Edit Team Member</a>
            </div>
        </div>
    </div>

    <div class="dashboard-table-container">
        <div id="team-member-profile">
            <div style="margin: 1px 1px 16px 1px;" class="team-mem-info-sec">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="img-heading-container align-items-center">
                            <div class="">
                                <img style="width: 166.49px;height: 166.49px; object-fit: cover;border-radius:50%"
                                    src="{{ $member->profile_picture ?? asset('assets/images/profile.png') }}">
                            </div>
                            <div class="">
                                <div class="mb-4">
                                    <p class="fs-18 darker-grotesque-bold">Role</p>
                                    <p style="color: #1C1C1C66;">{{ $member->role_name }}</p>
                                </div>
                                <div class="">
                                    <p class="fs-18 darker-grotesque-bold">Location</p>
                                    <p style="color: #1C1C1C66;">{{ $member->location }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-4">
                                    <p class="fs-18 darker-grotesque-bold">Active Projects</p>
                                    <p style="color: rgba(28, 28, 28, 0.4);">{{ $activeProjectCount }}</p>
                                </div>
                                <div>
                                    <p class="fs-18 darker-grotesque-bold">Total Time Logged</p>
                                    <p style="color: rgba(28, 28, 28, 0.4);">
                                        {{ formatSecondsToTimeString($member?->total_time_logged) }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-4">
                                    <p class="fs-18 darker-grotesque-bold">Hourly Rate</p>
                                    <p style="color: rgba(28, 28, 28, 0.4);">{{ $member?->hourly_rate }}</p>
                                </div>
                               
                                <div class="">
                                    <p class="fs-18 darker-grotesque-bold">Timezone</p>
                                    <p style="color: #1C1C1C66;">{{ $member?->timezone }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <div class="d-flex gap-1">
                                        <span
                                            class="leave-btn sick-leaves text-decoration-underline fs-14 darker-grotesque-bold">Sick
                                            Leave: {{ $member?->sick_leaves }}</span>
                                        <span
                                            class="leave-btn casual-leave text-decoration-underline fs-14 darker-grotesque-bold">Casual
                                            Leave: {{ $member?->casual_leaves }}</span>
                                        <span
                                            class="leave-btn annual-leaves text-decoration-underline fs-14 darker-grotesque-bold">Annual
                                            Leave: {{ $member?->annual_leaves }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-heading-container pb--20 d-flex justify-content-between align-items-center">
                <div style="margin-left: 1px" class="dashboard-button-container">
                    <a href="{{ tenant_route('teams.show', ['uuid' => request()->uuid]) }}"
                        class="toggle-button darker-grotesque-bold fs-18 px-2 {{ request()->routeIs('tenant.teams.show') ? 'active-offcanvas-tab' : '' }}">Projects</a>
                    <a href="{{ tenant_route('teams.tasks', ['uuid' => request()->uuid]) }}"
                        class="toggle-button darker-grotesque-bold fs-18 px-2 {{ request()->routeIs('tenant.teams.tasks') ? 'active-offcanvas-tab' : '' }}">Tasks</a>
                    <a href="{{ tenant_route('teams.leaves', ['uuid' => request()->uuid]) }}"
                        class="toggle-button darker-grotesque-bold fs-18 px-2 {{ request()->routeIs('tenant.teams.leaves') ? 'active-offcanvas-tab' : '' }}">Leave</a>
                </div>
            </div>

            <div class="content">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
