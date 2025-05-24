<div class="header-area">
    <div class="d-flex align-items-center w-100">
        <div class="nav-btn pull-left">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="d-flex align-items-center nav-content-mobile-hide">
            <a href="{{ tenant_route('dashboard') }}">
                <img class="nav-left-img mr-3" src="{{ asset('assets/images/nav-default-img.svg') }}" alt="Home">
            </a>

            @if (count(Breadcrumbs::get()))
            &emsp;
            <nav aria-label="breadcrumb"
                style="--bs-breadcrumb-divider:  url(&#34;data:image/svg+xml,%3Csvg%20width%3D%226%22%20height%3D%2211%22%20viewBox%3D%220%200%206%2011%22%20fill%3D%22none%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M0.720703%201.04443L5.2207%205.54443L0.720703%2010.0444%22%20stroke%3D%22%23B0BABF%22%2F%3E%3C%2Fsvg%3E&#34;)">
                <ol class="breadcrumb m-0">
                    @php($currentRouteName = Route::currentRouteName())
                    @foreach (Breadcrumbs::get() as $breadcrumb)
                    @if ($breadcrumb['url'])
                    <li
                        class="breadcrumb-item {{ $currentRouteName == @$breadcrumb['route_name'] ? 'active' : '' }}">
                        <a href="{{ @$breadcrumb['url'] }}"
                            class="nav-text m-0">{{ @$breadcrumb['name'] }}</a>
                    </li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @$breadcrumb['name'] }}</li>
                    @endif
                    @endforeach
                </ol>
            </nav>
            @else
            <a href="{{ tenant_route('dashboard') }}">
                <span class="nav-text">Dashboard</span>
            </a>
            
            @endif

        </div>
        <div class="ms-auto d-flex align-items-center">
            <div>
                <navbar-notification :notifications="{{ json_encode($notifications) }}" :authUser="{{ auth()->user() }}" />
            </div>
            <div class="dropdown">
                <a class="dropdown-toggle eliminate-icon" href="#" role="button" id="dropdownMenuLink"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    @if($avatar = auth()?->user()?->profile_picture)
                    <img class="nav-right-img" src="{{ $avatar }}">
                    @else
                    &nbsp;&nbsp;
                    <avatar name="{{auth()?->user()?->short_name}}" />
                    @endif
                </a>

                <ul class="dropdown-menu nav-drop-down" aria-labelledby="dropdownMenuLink">
                    <li class="nav-drop-down-heading">ACCOUNT</li>
                    <li class="nav-drop-down-email">{{ auth()->user()->email }}</li>
                    <hr style="color: #E5E9EB; opacity: 1;">
                    <li class="nav-drop-down-heading">{{ config('app.name') }}</li>
                    <li><a class="dropdown-item" href="{{tenant_route('profile.edit', ['uuid' => auth()?->user()?->uuid] )}}">Profile</a></li>
                    <hr style="color: #E5E9EB; opacity: 1;margin: 8px 0;">
                    <li>
                        <form id="logout-form" action="{{ tenant_route('auth.logout') }}" method="POST">
                            @csrf
                            <a onclick="logoutBtn.click()" class="dropdown-item">Log Out</a>
                            <button type="submit" id="logoutBtn" class="d-none">logout</button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>

    </div>
</div>


@push('scripts')
@endpush