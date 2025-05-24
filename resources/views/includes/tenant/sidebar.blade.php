@php
    $projectsRouteNames = [
        'index',
        'show',
        'task.index',
        'details',
        'files',
        'team',
        'chats',
        'schedules.index',
        'procurements.index',
        'project-areas.index',
    ];

@endphp
<div class="sidebar-menu">
    <div class="sidebar-header">
        <a href="{{ tenant_route('dashboard') }}">
            <div class="d-flex align-items-center">
                
                    <img class="nav-sidebar-left-img" src="{{ asset('assets/images/x-logo.svg') }}" alt="Home" style="display: none;">
                
                <div class="ms-2">
                    <h6 class="sidebar-logo-heading">{{ auth()?->user()?->company?->name }}</h6>
                    <span class="sidebar-logo-text">{{ auth()->user()->full_name ?? 'User' }}</span>
                </div>
            </div>
        </a>
    </div>

    <div class="main-menu">
        <div class="menu-inner1">
            <nav>
                <ul class="metismenu">

                    <li class=" side-bar-link {{ request()->routeIs('tenant.dashboard') ? 'nav-active' : '' }}">
                        <a href="{{ tenant_route('dashboard') }}" class="">
                            <img class="greenIcon" src="{{ asset('assets/images/icon/dashboard.svg') }}" />
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li
                        class=" side-bar-link {{ highilight_tenant_route(request()->route()->getName() === 'tenant.project-areas.index' ? 'tenant.' : 'tenant.projects.', $projectsRouteNames, ['tenant.task.index']) ? 'nav-active' : '' }}">
                        <a href="{{ tenant_route('projects.index') }}">
                            <img class="greenIcon" src="{{ asset('assets/images/icon/projects.svg') }}" />
                            <span>Projects</span>
                        </a>
                    </li>

                    <li
                        class=" side-bar-link {{ highilight_tenant_route('tenant.timesheet.', ['index']) ? 'nav-active' : '' }}">
                        <a href="{{ tenant_route('timesheet.index') }}">
                            <img class="greenIcon" src="{{ asset('assets/images/icon/time-sheets.svg') }}" />
                            <span>Time Sheets</span>
                        </a>
                    </li>

                    <hr style="color: #E5E9EB; opacity: 1;">

                    <!-- Library Section -->
                    <!-- Library Section -->
                    <li
                        class="for-sub-menu side-bar-link {{ request()->routeIs('tenant.supplier.index') || request()->routeIs('tenant.product-library.index') || request()->routeIs('tenant.material-library.index') ? 'nav-active' : '' }}">
                        <a id="side-sub-menu" class="d-flex justify-content-between">
                            <span class="ms-0">
                                <img class="greenIcon" src="{{ asset('assets/images/icon/library.svg') }}" />
                                <span>Library</span>
                            </span>
                            <img id="sidebar-arrow" style="filter: none;"
                                src="{{ asset('assets/images/arrow-icon.svg') }}">
                        </a>
                        <!-- Keep the submenu open if one of the routes is active -->
                        <div id="side-sub-menu-container"
                            style="{{ request()->routeIs('tenant.supplier.index') || request()->routeIs('tenant.product-library.index') || request()->routeIs('tenant.material-library.index') ? 'display: block;' : 'display: none;' }}">
                            <ul class="list-unstyled p-0 sidebar-submenu">
                                <li
                                    class="side-bar-link {{ request()->routeIs('tenant.supplier.index') ? 'nav-active' : '' }}">
                                    <a class="py-2" href="{{ tenant_route('supplier.index') }}">
                                        <span>Supplier Library</span>
                                    </a>
                                </li>
                                <li
                                    class="side-bar-link {{ request()->routeIs('tenant.material-library.index') ? 'nav-active' : '' }}">
                                    <a class="py-2" href="{{ tenant_route('material-library.index') }}">
                                        <span>Material Library</span>
                                    </a>
                                </li>

                                @if (auth()->user()->hasRole(auth()->user()::ROLE_ADMIN) || auth()->user()->hasPermissionTo('can procure'))
                                    <li
                                        class="side-bar-link nav-link {{ request()->routeIs('tenant.product-library.index') ? 'nav-active' : '' }}">
                                        <a class="py-2" href="{{ tenant_route('product-library.index') }}">
                                            <span>Product Library</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>


                    @can('isAdmin')
                        <li
                            class=" side-bar-link {{ request()->routeIs('tenant.teams.index', 'tenant.teams.create', 'tenant.teams.show') ? 'nav-active' : '' }}">
                            <a href="{{ tenant_route('teams.index') }}">
                                <img class="greenIcon" src="{{ asset('assets/images/icon/team.svg') }}" />
                                <span>Teams</span>
                            </a>
                        </li>
                    @endcan


                    <li class=" side-bar-link {{ request()->routeIs('tenant.chats.index') ? 'nav-active' : '' }}">
                        <a href="{{ tenant_route('chats.index') }}">
                            <img class="greenIcon" src="{{ asset('assets/images/icon/chats.svg') }}" />
                            <span>Chats</span>
                        </a>
                    </li>
                    @can('isAdmin')
                        <li
                            class="for-sub-menu side-bar-link  {{ in_array(Route::currentRouteName(), [
                                'tenant.settings.taxes.index',
                                'tenant.settings.exchange-rate.index',
                                'tenant.settings.exchange-rate.create',
                                'tenant.categories.index',
                            ])
                                ? 'nav-active'
                                : '' }}">
                            <a id="side-sub-menu-setting" class="d-flex justify-content-between">
                                <span class="ms-0">
                                    <img class="greenIcon" src="{{ asset('assets/images/icon/library.svg') }}" />
                                    <span>Settings</span>
                                </span>
                                <img id="sidebar-arrow-setting" style="filter: none;"
                                    src="{{ asset('assets/images/arrow-icon.svg') }}">
                            </a>
                            <!-- Keep the submenu open if one of the routes is active -->
                            <div id="side-sub-menu-container-setting" style="{{ request()->routeIs('tenant.settings.exchange-rate.index') || request()->routeIs('tenant.categories.index') ? 'display: block;' : 'display: none;' }}">
                                <ul class="list-unstyled p-0 sidebar-submenu">
                                    <li
                                        class="mt-2 side-bar-link d-none {{ request()->routeIs('tenant.settings.taxes.index') ? 'nav-active' : '' }}">
                                        <a class="py-2" href="{{ tenant_route('settings.taxes.index') }}">
                                            <span>Taxes</span>
                                        </a>
                                    </li>
                                    <li
                                        class="side-bar-link mt-2 {{ request()->routeIs('tenant.settings.exchange-rate.index') ? 'nav-active' : '' }}">
                                        <a class="py-2" href="{{ tenant_route('settings.exchange-rate.index') }}">
                                            <span>Exchange Rate</span>
                                        </a>
                                    </li>
                                    <li
                                        class=" side-bar-link mt-2 {{ request()->routeIs('tenant.categories.index') ? 'nav-active' : '' }}">
                                        <a class="py-2" href="{{ tenant_route('categories.index') }}">
                                            <span>Category</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endcan
                </ul>
            </nav>
        </div>
    </div>
</div>
@push('scripts')
@endpush
