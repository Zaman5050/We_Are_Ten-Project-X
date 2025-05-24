@props([
    'serivceFinancialData' => [],
])
<div class="d-flex align-items-center justify-content-between mb-3">
    <h6 class="task-stage-heading">Servcies Budget</h6>
    <button style="padding: 3px 10px" class="btn btn-default border bg-yellow darker-grotesque-bold hover-bg"
        type="button">
        Total profit:
        {{ $serivceFinancialData['projectWithCurrency']['currency']['symbol'] ?? '£' }}{{ array_sum(array_column($serivceFinancialData['servicesBudget']->toArray(), 'stage_profit')) }}
    </button>
</div>
@if (count($serivceFinancialData['servicesBudget']) > 0)
    @foreach ($serivceFinancialData['servicesBudget'] as $servicesWithIndex => $servicesWithBudget)
    <div>
            <div class="d-flex align-items-center justify-content-between" id="shadow-container-services-{{$servicesWithIndex }}">
                <label class="signup-labels table-date-heading mb-0">{{ $servicesWithBudget['stage_name'] }}
                    ({{ formatDateForDatabase($servicesWithBudget['start_date'], 'Y-m-d', 'd F') }} -
                    {{ formatDateForDatabase($servicesWithBudget['due_date'], 'Y-m-d', 'd F') }})</label>
                <div class="d-flex gap-2 flex-wrap justify-content-end">
                    <button style="padding: 3px 10px" class="btn-default border darker-grotesque-bold bg-light-grey br-6"
                        type="button">
                        Budget:
                        {{ $serivceFinancialData['projectWithCurrency']['currency']['symbol'] ?? '£' }}{{ $servicesWithBudget['stage_budget'] }}
                    </button>
                    <button style="padding: 3px 10px" class="btn-default border darker-grotesque-bold bg-grey br-6"
                        type="button">
                        Cost:
                        {{ $serivceFinancialData['projectWithCurrency']['currency']['symbol'] ?? '£' }}{{ $servicesWithBudget['stage_cost'] }}
                    </button>
                    <button style="padding: 3px 10px" class="btn-default border darker-grotesque-bold bg-purple br-6"
                        type="button">
                        Profit:
                        {{ $serivceFinancialData['projectWithCurrency']['currency']['symbol'] ?? '£' }}{{ $servicesWithBudget['stage_profit'] }}
                    </button>

                    <div class="position-relative">
                        <button class="collaps-expand-btn" data-target="#services-table-{{$servicesWithIndex}}"
                            data-Shadow="#shadow-container-services-{{$servicesWithIndex}}">
                            Collapse
                        </button>
                        <img class="collaps-icon" src="/assets/images/arrow-icon.svg" />
                    </div>
                </div>
            </div>
            <div class="task-table-container" id="services-table-{{$servicesWithIndex}}">
                <x-finance.service-table :users="$servicesWithBudget['users']" :currency="$serivceFinancialData['projectWithCurrency']['currency']" />
            </div>
        </div>
    @endforeach
@else
    <x-common.empty-view :message="'Sorry! No service found.'" />
@endif
