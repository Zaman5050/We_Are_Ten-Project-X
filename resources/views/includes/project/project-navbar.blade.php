<ul class="nav nav-tabs project-dashboard-tabs mb-5">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request()->routeIs('tenant.projects.show') ? 'active' : '' }}">
            <a class="text-black"
                href="{{ tenant_route('projects.show', ['project' => request()->project ?? request()->projectUuid]) }}">
                Dashboard
            </a>
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request()->routeIs('tenant.projects.details') ? 'active' : '' }}">
            <a class="text-black"
                href="{{ tenant_route('projects.details', ['project' => request()->project ?? request()->projectUuid]) }}">
                Details
            </a>
        </button>
    </li>
    @can('isAdmin')
        <li class="nav-item" role="presentation">
            <button
                class="nav-link {{ in_array(Route::currentRouteName(), [
                    'tenant.project.financials.budget',
                    'tenant.project.financials.costing',
                    'tenant.project.financials.procurement',
                    'tenant.project.financials.services',
                ])
                    ? 'active'
                    : '' }}">
                <a class="text-black"
                    href="{{ tenant_route('project.financials.budget', ['project' => request()->project ?? request()->projectUuid]) }}">
                    Financials
                </a>
            </button>
        </li>
    @endcan
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request()->routeIs('tenant.project-areas.index') ? 'active' : '' }}">
            <a class="text-black"
                href="{{ tenant_route('project-areas.index', ['project' => request()->project ?? request()->projectUuid]) }}">
                Areas
            </a>
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request()->routeIs('tenant.projects.files') ? 'active' : '' }}">
            <a class="text-black"
                href="{{ tenant_route('projects.files', ['project' => request()->project ?? request()->projectUuid]) }}">
                Files
            </a>
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request()->routeIs('tenant.projects.team') ? 'active' : '' }}">
            <a class="text-black"
                href="{{ tenant_route('projects.team', ['project' => request()->project ?? request()->projectUuid]) }}">
                Team
            </a>
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request()->routeIs('tenant.task.index') ? 'active' : '' }}">
            <a class="text-black"
                href="{{ tenant_route('task.index', ['projectUuid' => request()->project ?? request()->projectUuid]) }}">
                Tasks
            </a>
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request()->routeIs('tenant.projects.chats') ? 'active' : '' }}">
            <a class="text-black"
                href="{{ tenant_route('projects.chats', ['project' => request()->project ?? request()->projectUuid]) }}">
                Chats
            </a>
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link {{ request()->routeIs('tenant.projects.schedules.index') ? 'active' : '' }}">
            <a class="text-black"
                href="{{ tenant_route('projects.schedules.index', ['project' => request()->project ?? request()->projectUuid]) }}">
                Schedule
            </a>
        </button>
    </li>

    @can('isProcure')
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ request()->routeIs('tenant.projects.procurements.index') ? 'active' : '' }}">
                <a class="text-black"
                    href="{{ tenant_route('projects.procurements.index', ['project' => request()->project ?? request()->projectUuid]) }}">
                    Procurement
                </a>
            </button>
        </li>
    @endcan

</ul>
