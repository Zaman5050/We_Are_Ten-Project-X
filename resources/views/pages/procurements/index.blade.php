@extends('layouts.tenant.project')

@section('title', 'Procurements')

@section('project-content')

    <div class="dashboard-heading-container pb--40">
        @if ($project->procurementBudget->isNotEmpty())

            <div class="dashboard-table-container for-shadow br-6 mb-4">
                <table class="table mb-0 dashboard-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Budget</th>
                            <th>Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($project->procurementBudget as $budget)
                            <tr>
                                <td>{{ $budget->category->name }}</td>
                                <td>{{ $budget->quantity }} ({{ $budget->remainingQuantity }})</td>
                                <td>{{ $budget->amount }}</td>
                                <td>{{ $budget->remaining }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div class="d-flex justify-content-between align-items-center">
            <div class="dashboard-button-container mb-3">
                <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class=" active summary-finalcial px-2" id="home-tab" data-bs-toggle="tab"
                            data-bs-target="#home" type="button" role="tab" aria-controls="home"
                            aria-selected="true">Summary</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class=" summary-finalcial px-2" id="profile-tab" data-bs-toggle="tab"
                            data-bs-target="#profile" type="button" role="tab" aria-controls="profile"
                            aria-selected="false">Financials</button>
                    </li>
                </ul>
            </div>
            <div class="d-flex gap-3">
                <div class="dropdown import-drop-down export-selected-dropdown">
                    @if ($project->start_date == null && $project->due_date == null )                        
                        <a class="filter-btn import-creat-project dropdown-toggle bg-yellow" role="button"
                            id="dropdownMenuLinkExport" data-bs-toggle="dropdown" aria-expanded="false">
                            Export Selected
                            <img class="ms-2" src="{{ asset('assets/images/arrow-icon.svg') }}">
                        </a>
                    @endif
                    <ul style="width: 100%; min-width: 138px; padding: 15px 18px; box-shadow: rgba(26, 32, 36, 0.32) 0px 0px 2px 0px;"
                        class="dropdown-menu border-0" aria-labelledby="dropdownMenuLinkExport">
                        @if (false)
                            <li>
                                <a style="cursor: pointer;" class="dropdown-item mb-2">
                                    Export as PDF
                                </a>
                            </li>
                        @endif
                        <li>
                            <a style="cursor: pointer;" class="dropdown-item mb-2"
                                href="{{ tenant_route('projects.procurements.export-excel', ['project' => request()->project]) }}">
                                Export as Excel Sheet
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown import-drop-down">
                    <a class="filter-btn import-creat-project dropdown-toggle bg-yellow" role="button"
                        id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">Add Product<img class="ms-2"
                            src="{{ asset('assets/images/arrow-icon.svg') }}"></a>
                    <ul style="width: 100%; min-width: 261px !important; padding: 15px 18px; box-shadow: rgba(26, 32, 36, 0.32) 0px 0px 2px 0px;"
                        class="dropdown-menu border-0" aria-labelledby="dropdownMenuLink">
                        <li>
                            <add-procurement-from-product-library-button
                                :project="{{ json_encode($project) }}" />
                        </li>
                        <li>
                            <add-custom-procurement-button
                                :project="{{ json_encode($project) }}" />
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if (count($areasWithProcurements) == 0)
        <x-common.empty-view :message="'You have not added any Procurements yet.'">
            <add-custom-procurement-button :project="{{ json_encode($project) }}"  class="text-decoration-underline w-auto cursor-pointer">
            </add-custom-procurement-button>
            to get started
        </x-common.empty-view>
    @else
        <x-procurement.procurement-table :areasWithProcurements="$areasWithProcurements"  :project="$project" /> 
    @endif
    <project-procurement :categories_with_products="{{ json_encode($areasWithProducts) }}"
        :project_areas="{{ json_encode($project_areas) }}" :suppliers="{{ json_encode($suppliers) }}"
        :currencies="{{ json_encode($activeCurrencies) }}" :project="{{ json_encode($project) }}"
        :categories="{{ json_encode($categories) }}" />

@endsection
