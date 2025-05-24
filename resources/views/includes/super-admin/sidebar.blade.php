<div class="sidebar-menu">
    <div class="sidebar-header">
        <a href="{{ route('super-admin.dashboard') }}">
            <div class="d-flex align-items-center">
                <img class="nav-sidebar-left-img" src="{{ asset('assets/images/x-logo.svg') }}" style="display: none;">
                <div class=" ms-2">
                    <h6 class="sidebar-logo-heading">{{ config('app.name') }}</h6>
                    <span class="sidebar-logo-text">{{ auth()->user()->full_name ?? 'User' }}</span>
                </div>
            </div>
        </a>
    </div>

    <div class="main-menu">
        <div class="menu-inner1">
            <nav>
                <ul class="metismenu">
                    <li class="side-bar-link {{ request()->routeIs('super-admin.dashboard') ?'nav-active':''}}">
                        <a href="{{ route('super-admin.dashboard') }}"  >
                            <img class="greenIcon" src="{{ asset('assets/images/icon/dashboard.svg') }}" />
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
