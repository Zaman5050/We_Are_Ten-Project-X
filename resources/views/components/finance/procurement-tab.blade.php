@props([
    'procurementFinancialData' => [],
])

<procurement-finance :procurement_financial_data="{{ json_encode($procurementFinancialData) }}">
    @if (count($procurementFinancialData['procurementBudgetData']) === 0)
        <x-common.empty-view :message="'Sorry! No procurement found.'" />
    @endif
</procurement-finance>

<div class="row d-none">
    <div class="col-md-5">
        <h6 class="task-stage-heading my-3">
            Additional costs
        </h6>
        <div class="task-table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-50 text-nowrap">
                            supplier
                        </th>
                        <th class="w-50 text-nowrap">title</th>
                        <th class="w-50 text-nowrap">
                            Total Cost
                        </th>
                    </tr>
                </thead>
                <tbody>
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
                </tbody>
            </table>
        </div>
    </div>
</div>
