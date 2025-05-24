<div class="header-area">
    <div class="d-flex align-items-center w-100">
        <div class="nav-btn pull-left">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <a href="{{ route('super-admin.dashboard') }}">
            <div class="d-flex align-items-center nav-content-mobile-hide">
                <img class="nav-left-img" src="{{ asset('assets/images/nav-default-img.svg') }}">
                <span class="nav-text">Dashboard</span>
            </div>
        </a>
        <div class="ms-auto d-flex align-items-center">
            {{-- <input style="background-image: url('{{asset('assets/images/search-icon.svg')}}');"
                class="nav-input nav-content-mobile-hide" placeholder="Search"> --}}
            {{-- <img src="{{ asset('bell-icon.svg') }}" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"> --}}
            <svg style="display: none;" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"
             width="18" height="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.10557 14.5624L0.275584 14.0047L0.275584 14.0047L1.10557 14.5624ZM2.26855 10.835L1.26855 10.8242V10.835H2.26855ZM2.29296 8.5814L3.29296 8.59223V8.5814H2.29296ZM16.9006 14.5805L17.7412 14.0388L17.7412 14.0388L16.9006 14.5805ZM15.7814 10.835L14.7814 10.8236V10.835H15.7814ZM15.8058 8.69307L16.8058 8.70447V8.69307H15.8058ZM6.33333 18C5.78105 18 5.33333 18.4477 5.33333 19C5.33333 19.5523 5.78105 20 6.33333 20V18ZM11.6667 20C12.219 20 12.6667 19.5523 12.6667 19C12.6667 18.4477 12.219 18 11.6667 18V20ZM1.93556 15.1202C2.60782 14.1199 3.26855 12.6372 3.26855 10.835H1.26855C1.26855 12.1434 0.786904 13.2438 0.275584 14.0047L1.93556 15.1202ZM3.26849 10.8459L3.2929 8.59223L1.29302 8.57057L1.26861 10.8242L3.26849 10.8459ZM17.7412 14.0388C17.2508 13.2779 16.7814 12.1648 16.7814 10.835H14.7814C14.7814 12.6372 15.415 14.1213 16.0601 15.1222L17.7412 14.0388ZM16.7814 10.8464L16.8058 8.70447L14.8059 8.68168L14.7815 10.8236L16.7814 10.8464ZM16.8058 8.69307C16.8058 4.01072 13.4227 0 9 0V2C12.0948 2 14.8058 4.8779 14.8058 8.69307H16.8058ZM16.4933 16.625C17.2957 16.625 17.7284 16.0028 17.8809 15.5978C18.042 15.1701 18.0779 14.5612 17.7412 14.0388L16.0601 15.1222C16.0102 15.0448 16.0016 14.9807 16.0003 14.9546C15.9989 14.9285 16.0027 14.9102 16.0092 14.893C16.0147 14.8785 16.0364 14.8268 16.1034 14.7673C16.1782 14.7009 16.314 14.625 16.4933 14.625V16.625ZM3.29296 8.5814C3.29296 4.8279 5.95974 2 9 2V0C4.63187 0 1.29296 3.96072 1.29296 8.5814H3.29296ZM1.50763 14.625C1.69077 14.625 1.82791 14.7038 1.90175 14.7706C1.96767 14.8302 1.98774 14.8808 1.99217 14.8929C1.99767 14.908 2.00119 14.9244 1.99964 14.9498C1.9981 14.9749 1.98888 15.0409 1.93556 15.1202L0.275584 14.0047C-0.077099 14.5295 -0.043574 15.1489 0.11355 15.5791C0.261638 15.9845 0.692398 16.625 1.50763 16.625V14.625ZM16.4933 14.625H1.50763V16.625H16.4933V14.625ZM6.33333 20H11.6667V18H6.33333V20Z" fill="#252C32"/>
            </svg>
            <div class="dropdown">
                <a class="dropdown-toggle eliminate-icon" href="#" role="button" id="dropdownMenuLink"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="nav-right-img" src="{{ asset('assets/images/nav-default-img.svg') }}">
                </a>

                <ul class="dropdown-menu nav-drop-down" aria-labelledby="dropdownMenuLink">
                    <li class="nav-drop-down-heading">ACCOUNT</li>
                    <li class="nav-drop-down-email">{{ auth()->user()->email }}</li>
                    <hr style="color: #E5E9EB; opacity: 1;">
                    <li class="nav-drop-down-heading">{{ config('app.name') }}</li>
                    <li  style="display: none;"><a class="dropdown-item" href="#">Profile</a></li>
                    <hr style="color: #E5E9EB; opacity: 1;margin: 8px 0;">
                    <li>
                        <a
                            class="dropdown-item"
                            href="{{ route('super-admin.auth.logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        >
                            Log Out
                        </a>
                    </li>
                </ul>
            </div>

        </div>
        <!-- <div class="dashboard-search-field">
        <input type="text" class="search" placeholder="search" />
      </div> -->
    </div>
    <div class="offcanvas offcanvas-end navbar-offcanvas" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
          <h5 id="offcanvasRightLabel" class="text-uppercase">Notifications</h5>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <p >No notifications available.</p>

            {{-- <div class="d-flex align-items-center mb-3">
                <img style="width: 24px; height:24px; object-fit:cover; border-radius:8px" src="{{ asset('assets/images/nav-default-img.svg') }}">
                <div style="margin-left: 8px">
                    <p class="notification-list">Your leave has been approved</p>
                    <span class="notification-time">59 minutes ago</span>
                </div>
            </div>
            <div class="d-flex align-items-center mb-3">
                <img style="width: 24px; height:24px; object-fit:cover; border-radius:8px" src="{{ asset('assets/images/nav-default-img.svg') }}">
                <div style="margin-left: 8px">
                    <p class="notification-list">Your leave has been approved</p>
                    <span class="notification-time">59 minutes ago</span>
                </div>
            </div> --}}
        </div>
      </div>
</div>

<form id="logout-form" action="{{ route('super-admin.auth.logout') }}" method="POST" class="d-none" >
    @csrf
</form>
