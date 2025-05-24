@extends('layouts.tenant.index')

@section('title', 'Projects')
@section('content')
    <!-- Main inner content -->

    <div class="sales-report-area">
        <div class="dashboard-heading-container pb--40 d-flex justify-content-between">
            <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                
                    <li class="nav-item" role="presentation">
                        <a class="summary-finalcial px-2 dashboard-main-heading mb-0" 
                           onclick="updateQueryParam('tab', 'active')" 
                           id="active-tab" 
                           data-bs-toggle="tab" 
                           href="#active" 
                           role="tab" 
                           aria-controls="active" 
                           aria-selected="false">
                            Projects
                        </a>
                    </li>
                <li class="nav-item" role="presentation">
                    <a class="summary-finalcial px-2 py-0 filter-btn dashboard-main-heading mb-0" 
                       id="draft-tab" 
                       onclick="updateQueryParam('tab', 'draft')" 
                       data-bs-toggle="tab" 
                       href="#draft" 
                       role="tab" 
                       aria-controls="draft" 
                       aria-selected="false">
                        Drafts
                    </a>
                </li>
            </ul>

            <div class="d-flex gap-3">
                <create-project-button />
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="projectTabsContent">
            <!-- Active Projects -->
            <div class="tab-pane fade" id="active" role="tabpanel" aria-labelledby="active-tab">
                <div class="project-listing-whole-container {{ count($projects) == 0 ? 'd-flex justify-content-center align-items-center' : '' }}">
                    @if(count($projects) > 0)
                        @foreach ($projects as $project)
                            <x-project.card :project="$project" />
                        @endforeach
                    @else
                        <x-project.create />
                    @endif
                </div>
            </div>

            <!-- Draft Projects -->
            @if(count($draftProjects) > 0)
            <div class="tab-pane fade" id="draft" role="tabpanel" aria-labelledby="draft-tab">
                <div class="project-listing-whole-container {{ count($draftProjects) == 0 ? 'd-flex justify-content-center align-items-center' : '' }}">
                    <project-card :projects="{{ json_encode($draftProjects) }}" />
                </div>
            </div>
            @endif
        </div>
    </div>

    <x-project.modal />

    <!-- main content area end -->
@endsection

<script>
    // Function to update query parameter
    function updateQueryParam(key, value) {
        const url = new URL(window.location.href);
        url.searchParams.set(key, value);
        window.history.pushState({}, '', url);
    }

    // Set the correct tab on page load based on the query parameter
    document.addEventListener('DOMContentLoaded', function () {
        const url = new URL(window.location.href);
        const tab = url.searchParams.get('tab') || 'active'; // Default to 'active'
        
        // Remove 'active' class from all tabs and contents
        document.querySelectorAll('.nav-tabs .nav-item .active').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('show', 'active'));

        // Set the correct tab and content active
        const tabLink = document.querySelector(`#${tab}-tab`);
        const tabContent = document.querySelector(`#${tab}`);
        
        if (tabLink && tabContent) {
            tabLink.classList.add('active');
            tabLink.setAttribute('aria-selected', 'true');
            tabContent.classList.add('show', 'active');
        }
    });
</script>
