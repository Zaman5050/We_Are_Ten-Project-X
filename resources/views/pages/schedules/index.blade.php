@extends('layouts.tenant.project')

@section('title', 'Schedules')

@section('project-content')

    <div class="dashboard-heading-container pb--40 d-flex justify-content-between">
        <h1 class="dashboard-main-heading mb-0"></h1>
        <div class="d-flex gap-3">
            <div class="dropdown import-drop-down export-selected-dropdown">
                <a class="filter-btn import-creat-project dropdown-toggle bg-yellow" role="button" id="dropdownMenuLinkExport"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Export Selected
                    <img class="ms-2" src="{{ asset('assets/images/arrow-icon.svg') }}">
                </a>
                <ul style="width: 100%; min-width: 138px; padding: 15px 18px; box-shadow: rgba(26, 32, 36, 0.32) 0px 0px 2px 0px; "
                    class="dropdown-menu border-0 left-50" aria-labelledby="dropdownMenuLinkExport">
                    {{-- <li>
                    <a style="cursor: pointer;" class="dropdown-item mb-2">
                        Export as PDF
                    </a>
                </li> --}}
                    <li>
                        <a style="cursor: pointer;" class="dropdown-item mb-2"
                            href="{{ tenant_route('projects.schedules.export-excel', ['project' => request()->project]) }}">
                            Export as Excel Sheet
                        </a>
                    </li>
                </ul>
            </div>
            <div class="dropdown import-drop-down">
                <a class="filter-btn import-creat-project dropdown-toggle bg-yellow" role="button" id="dropdownMenuLinkAdd"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Add Material
                    <img class="ms-2" src="{{ asset('assets/images/arrow-icon.svg') }}">
                </a>
                <ul style="width: 100%; min-width: 138px; padding: 15px 18px; box-shadow: rgba(26, 32, 36, 0.32) 0px 0px 2px 0px;"
                    class="dropdown-menu border-0" aria-labelledby="dropdownMenuLinkAdd">
                    <li>
                        <add-from-material-library-button :project_areas="{{ json_encode($project_areas) }}"
                        />
                    </li>
                    <li>
                        <add-custom-schedule-button :project_areas="{{ json_encode($project_areas) }}"
                            :project="{{ json_encode($project) }}" />
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @if (count($categoriesWithSchedules) == 0)
        <x-schedule.add-material />
    @else
        <x-schedule.schedule-table :categoriesWithSchedules="$categoriesWithSchedules" />
    @endif
    <project-schedule :categories_with_materials="{{ json_encode($categoriesWithMaterials) }}"
        :project_areas="{{ json_encode($project_areas) }}" :suppliers="{{ json_encode($suppliers) }}"
        :categories="{{ json_encode($categories) }}"></project-schedule>

@endsection
