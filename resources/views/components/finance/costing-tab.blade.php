@props([
    'costingFinancialData' => [],
])

<div class="row">
    <div class="col-md-5">
        <label class="signup-labels table-date-heading">Procurement Budget
            ({{ count(json_decode($costingFinancialData['projectWithProcurementBudget'], true)['procurement_budget'] ?? []) }})</label>
        <div class="task-table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-50">Category</th>
                        <th class="w-50">Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count(json_decode($costingFinancialData['projectWithProcurementBudget'], true)['procurement_budget'] ?? []) > 0)
                        @foreach (json_decode($costingFinancialData['projectWithProcurementBudget'], true)['procurement_budget'] ?? [] as $budget)
                            <tr class="table-body">
                                <td class="d-none"></td>
                                <td style="text-transform: capitalize;color:#1A1B1F;">{{ $budget['category']['name'] ?? '' }}</td>
                                <td>{{ $costingFinancialData['projectWithProcurementBudget']['currency']['symbol'] ?? '£' }}{{ $budget['amount'] ?? '' }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="table-body">
                            <td class="d-none"></td>
                            <td> No category available! </td>
                        </tr>
                    @endif

                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-5 d-none">
        <label class="signup-labels table-date-heading">Additional Cost (4)</label>
        <div class="task-table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-33">Supplier</th>
                        <th class="w-33">Title</th>
                        <th class="w-33">Total Cost</th>
                    </tr>
                </thead>
                <tr class="table-body">
                    <td class="d-none"></td>
                    <td>Frama</td>
                    <td>Delivery Cost</td>
                    <td>$2,000</td>
                </tr>
                <tr class="table-body">
                    <td class="d-none"></td>
                    <td>Frama</td>
                    <td>Delivery Cost</td>
                    <td>$2,000</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div>
    <label class="signup-labels table-date-heading">Services</label>
    @if (count($costingFinancialData['stagesBudget']) > 0)
        @foreach ($costingFinancialData['stagesBudget'] as $stageWithBudget)
            <div>
                <div class="d-flex justify-content-between align-items-center" id="shadow-container-financials-1">
                    <h6 class="task-stage-heading">{{ $stageWithBudget['stage_name'] }}</h6>
                    <div class="position-relative">
                        <button class="collaps-expand-btn" data-target="#serivce-financials-table"
                            data-Shadow="#shadow-container-financials-1">
                            Collapse
                        </button>
                        <img class="collaps-icon" src="/assets/images/arrow-icon.svg" />
                    </div>
                </div>
                <div class="task-table-container">
                    <x-finance.service-table :users="$stageWithBudget['users']" :currency="$costingFinancialData['projectWithProcurementBudget']['currency']" />
                </div>
            </div>
        @endforeach
    @else
        <x-common.empty-view :message="'Sorry! No service found.'" />
    @endif
</div>
