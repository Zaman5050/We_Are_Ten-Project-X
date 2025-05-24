@props([
    'areasWithProcurements' => [],
    'project' => [],
])
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
        <div class="selected-material-container">
            @php($index = 0)
            @foreach ($areasWithProcurements as $area => $procurements)
                @php($index += 1)
                <div class="" id="shadow-container-summary-{{ $index }}">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <h6 style="color:#000" class="task-stage-heading">{{ $area }}
                            ({{ count($procurements) }})</h6>
                        <div class="collaps-container">
                            <button class="collaps-expand-btn" data-target="#task-table-summary-{{ $index }}"
                                data-Shadow="#shadow-container-summary-{{ $index }}">Collapse</button>
                            <img class="collaps-icon"
                                src="{{ asset('assets/images/arrow-icon.svg') }}">
                        </div>
                    </div>
                    <div class="task-table-container">
                        <table style="border-collapse: separate;border-spacing: 0 8px;" class="table my-2"
                            id="task-table-summary-{{ $index }}">
                            <tbody>
                                @foreach ($procurements as $procurement)
                                    <tr class="table-body myDiv">
                                        <td>
                                            <label class="check-container selected-material-row">
                                                <input type="checkbox" class="myCheckbox" id="checkbox1"
                                                    data-target="div1">
                                                <span class="checkmark"></span>
                                            </label>
                                        </td>
                                        <td class="pe-0">
                                            <div style="width:110px">
                                                <img style="width: 110.3px; height: 117px; object-fit: cover; border-radius: 5.76px;"
                                                    src="{{ isset($procurement->product->cover_image) && count($procurement->product->cover_image) > 0 ? $procurement->product->cover_image[0]['url'] : asset('assets/images/project-list-default.svg') }}">
                                            </div>
                                        </td>
                                        <td class="ps-0">
                                            <div style="height: 96.9px; padding-left: 11px;"
                                                class="d-flex flex-column justify-content-between">
                                                <p>{{ $procurement->product->category->name ?? '' }}</p>
                                                <p>{{ $procurement->product->sku ?? '' }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                                <div class="">
                                                    <p style="white-space: nowrap;">
                                                        {{ $procurement->product->product_name ?? '' }}</p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Product
                                                        Name</span>
                                                </div>
                                                <div class="">
                                                    <p style="white-space: nowrap;">
                                                        {{ $procurement->product->brand_name ?? '-'}}</p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Brand</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                                <div class="">
                                                    <p style="white-space: nowrap;">{{ $procurement->product->width ?? '-' }}
                                                    </p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Width (MM)</span>
                                                </div>
                                                <div class="">
                                                    <p style="white-space: nowrap;">{{ $procurement->product->color ?? '-' }}
                                                    </p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Colour</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                                <div class="">
                                                    <p style="white-space: nowrap;">{{ $procurement->product->length ?? '-' }}
                                                    </p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Length (MM)</span>
                                                </div>
                                                <div class="">
                                                    <p style="white-space: nowrap;">{{ $procurement->product->height ?? '-' }}
                                                    </p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Heigh (MM)</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                                <div class="">
                                                    <p style="white-space: nowrap;">{{ $procurement->product->depth ?? '-' }}
                                                    </p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Depth (MM)</span>
                                                </div>
                                                <div class="">
                                                    <p style="white-space: nowrap;">{{ $procurement->product->finish ?? '-' }}
                                                    </p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Finish</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                                <div class="">
                                                    <p style="white-space: nowrap;">
                                                        {{ $procurement->production_lead_time ?? '' }} Weeks</p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Production Lead
                                                        Time</span>
                                                </div>
                                                <div class="">
                                                    <p style="white-space: nowrap;">
                                                        {{ $procurement->shipping_lead_time ?? '' }} Weeks</p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Shipping Lead
                                                        Time</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                                <div class="">
                                                    <p style="white-space: nowrap;">{{ $procurement->quantity ?? '' }}</p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Quantity</span>
                                                </div>
                                                <div class="">
                                                    <p style="white-space: nowrap;">
                                                        {{ $procurement->product->material ?? '-' }}</p>
                                                    <span style="color: #C4C4C4;white-space: nowrap;">Material</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                                <div class="">
                                                    <div class="dropdown three-dots-dropdown">
                                                        <div style="width: fit-content; " class=" border-0 p-0">
                                                            Areas
                                                        </div>
                                                    </div>
                                                    <span
                                                        style="color: #C4C4C4;white-space: nowrap;">{{ $procurement->projectArea->area_name ?? '' }}</span>
                                                </div>
                                                <div>

                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <procurement-detail-button
                                                :procurement="{{ json_encode($procurement) }}" />
                                        </td>
                                        <td>
                                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                                <div class="dashboard-select-container long-select">
                                                    <form
                                                        action="{{ tenant_route('projects.procurements.status', ['project' => request()->project, 'procurement' => $procurement->uuid]) }}"
                                                        method="GET"
                                                        id="status-form"
                                                    >
                                                        <select
                                                            class="select"
                                                            name="status"
                                                            id="status-select"
                                                            onchange="this.form.submit()"
                                                        >
                                                            @foreach(\App\Models\Procurement::STATUSES as $status)
                                                                <option value="{{ $status }}" {{ $procurement->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                </div>
                                                

                                                <div style="gap: 28px;"
                                                    class="d-flex flex-column justify-content-between align-items-center">
                                                    <div class="dropdown three-dots-dropdown">
                                                        <a style="width: fit-content; "
                                                            class="d-flex justify-content-end filter-btn border-0 dropdown-toggle gap-3"
                                                            href="#" role="button" id="dropdownMenuLink"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <img src="{{ asset('assets/images/three-dots.svg') }}">
                                                        </a>
                                                        <ul class="dropdown-menu procurement-dropdown border-0"
                                                            aria-labelledby="dropdownMenuLink">
                                                            <li>
                                                                <form
                                                                    action="{{ tenant_route('projects.procurements.duplicate', ['project' => request()->project, 'procurement' => $procurement->uuid]) }}"
                                                                    method="POST"
                                                                >
                                                                    @csrf
                                                                    @method('POST')
                                                                    <button class="dropdown-item mb-2" type="submit">
                                                                        Duplicate
                                                                    </button>
                                                                </form>

                                                            </li>
                                                            <li>
                                                                <form
                                                                    action="{{ tenant_route('projects.procurements.destroy', ['project' => request()->project, 'procurement' => $procurement->uuid]) }}"
                                                                    method="POST"
                                                                >
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button style="color: #F8624E;" class="dropdown-item mb-2" type="submit">
                                                                        Delete
                                                                    </button>
                                                                </form>

                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="p-0"></td>
                                    <td colspan="2" class="ps-0"><a class="create-one  d-block"
                                            style="font-family: inter-Regular; "><add-custom-procurement-button  :project="{{ json_encode($project) }}" /></a>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>
<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
    <div class="selected-material-container">

        @php($index = 0)
        @foreach ($areasWithProcurements as $area => $procurements)
            @php($index += 1)
            <div class="" id="shadow-container-financial-{{ $index }}">
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <h6 style="color:#000" class="task-stage-heading">{{ $area }}
                        ({{ count($procurements) }})</h6>
                    <div class="collaps-container">
                        <button class="collaps-expand-btn" data-target="#task-table-financial-{{ $index }}"
                            data-Shadow="#shadow-container-financial-{{ $index }}">Collapse</button>
                        <img class="collaps-icon"
                            src="{{ asset('assets/images/arrow-icon.svg') }}">
                    </div>
                </div>
                <div class="task-table-container">
                    <table style="border-collapse: separate;border-spacing: 0 8px;" class="table my-2"
                        id="task-table-financial-{{ $index }}">
                        <tbody>
                            @foreach ($procurements as $procurement)
                                <tr class="table-body myDiv">
                                    <td>
                                        <label class="check-container selected-material-row">
                                            <input type="checkbox" class="myCheckbox" id="checkbox1"
                                                data-target="div1">
                                            <span class="checkmark"></span>
                                        </label>
                                    </td>
                                    <td class="pe-0">
                                        <div style="width:110px">
                                            <img style="width: 110.3px; height: 117px; object-fit: cover; border-radius: 5.76px;"
                                                src="{{ isset($procurement->product->cover_image) && count($procurement->product->cover_image) > 0 ? $procurement->product->cover_image[0]['url'] : asset('assets/images/project-list-default.svg') }}">
                                        </div>
                                    </td>
                                    <td class="ps-0">
                                        <div style="height: 96.9px; padding-left: 11px;"
                                            class="d-flex flex-column justify-content-between">
                                            <p >
                                                {{ $procurement->product->category->name ?? '' }}</p>
                                            <p >{{ $procurement->product->sku ?? '' }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                            <div class="">
                                                <p style="white-space: nowrap;">
                                                    {{ $procurement->product->product_name ?? ''}}</p>
                                                <span
                                                    style="color: #C4C4C4;white-space: nowrap;">Product
                                                    Name</span>
                                            </div>
                                            <div class="">
                                                <p style="white-space: nowrap;">
                                                    {{ $procurement?->product?->supplier->name ?? '' }}</p>
                                                <span
                                                    style="color: #C4C4C4;white-space: nowrap;">Supplier</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                            <div class="">
                                                <p style="white-space: nowrap;">
                                                    {{ $procurement?->exchangeCurrency?->symbol ?? '' }}</p>
                                                <span
                                                    style="color: #C4C4C4;white-space: nowrap;">Exchange
                                                    Currency</span>
                                            </div>
                                            <div class="">
                                                <p style="white-space: nowrap;">
                                                    {{ $procurement->exchange_rate }}</p>
                                                <span
                                                    style="color: #C4C4C4;white-space: nowrap;">Conversion
                                                    Rate</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                            <div class="" style="display: none;">
                                                <p style="font-size: 14px;white-space: nowrap;">
                                                    {{ $procurement?->baseCurrency?->symbol ?? '' }}
                                                    {{ $procurement->budget }}</p>
                                                <span
                                                    style="color: #C4C4C4;white-space: nowrap;">Budget</span>
                                            </div>
                                            <div class="">
                                                <p style="white-space: nowrap;">
                                                    {{ $procurement?->exchangeCurrency?->symbol ?? '' }}
                                                    {{ $procurement->retail_price }}</p>
                                                <span
                                                    style="color: #C4C4C4;white-space: nowrap;">Retail
                                                    Price</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                            <div class="">
                                                <p style="white-space: nowrap;">
                                                    {{ $procurement->trade_discount }}%</p>
                                                <span style="color: #C4C4C4;white-space: nowrap;">Trade
                                                    Discount</span>
                                            </div>
                                            <div class="">
                                                <p style="white-space: nowrap;">
                                                    {{ $procurement?->exchangeCurrency?->symbol ?? '' }}
                                                    {{ $procurement->trade_price }}</p>
                                                <span style="color: #C4C4C4;white-space: nowrap;">Trade
                                                    Price</span>
                                            </div>
                                            <div class="">
                                                <p style="font-size: 14px;white-space: nowrap;">
                                                    {{ $procurement->trade_discount }}%</p>
                                                <span style="font-size: 12px;color: #C4C4C4;white-space: nowrap;">Trade
                                                    Discount</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                            <div class="">
                                                <p style="white-space: nowrap;">
                                                    {{ $procurement?->baseCurrency?->symbol ?? '' }}
                                                    {{ $procurement->actual_price }}</p>
                                                <span
                                                    style="color: #C4C4C4;white-space: nowrap;">Actual
                                                    Price</span>
                                            </div>
                                            <div class="">
                                                <p style="white-space: nowrap;">
                                                    {{ $procurement->trade_terms }}%</p>
                                                <span
                                                    style="color: #C4C4C4;white-space: nowrap;">Upfront
                                                    Percentage</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <procurement-detail-button :procurement="{{ json_encode($procurement) }}" />
                                    </td>
                                    <td>
                                        <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                            <div class="dashboard-select-container long-select">
                                                <form
                                                        action="{{ tenant_route('projects.procurements.status', ['project' => request()->project, 'procurement' => $procurement->uuid]) }}"
                                                        method="GET"
                                                        id="status-form"
                                                    >
                                                        <select 
                                                            style="display: none;"
                                                            class="select"
                                                            name="status"
                                                            id="status-select"
                                                            onchange="this.form.submit()"
                                                        >
                                                            @foreach(\App\Models\Procurement::STATUSES as $status)
                                                                <option value="{{ $status }}" {{ $procurement->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                
                                            </div>

                                            <div style="gap: 28px;"
                                                class="d-flex flex-column justify-content-between align-items-center d-none">
                                                <div class="dropdown three-dots-dropdown">
                                                    <a style="width: fit-content; "
                                                        class="d-flex justify-content-end filter-btn border-0 dropdown-toggle gap-3"
                                                        href="#" role="button" id="dropdownMenuLink"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <img src="{{ asset('assets/images/three-dots.svg') }}">
                                                    </a>
                                                    <ul class="dropdown-menu procurement-dropdown border-0"
                                                        aria-labelledby="dropdownMenuLink">
                                                        <li><a class="dropdown-item" href="#">Comment</a></li>
                                                        <li><a class="dropdown-item" href="#">Archive</a></li>
                                                        <li><a class="dropdown-item" href="#">Duplicate</a></li>
                                                        <li><a class="dropdown-item color-danger"
                                                                href="#">Delete</a></li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="p-0"></td>
                                <td colspan="2" class="ps-0"><a class="create-one  d-block"
                                        style="font-family: inter-Regular; "><add-custom-procurement-button  :project="{{ json_encode($project) }}"  /></a>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

    </div>
</div>
</div>







@push('scripts')
    <script>
        $(document).ready(function() {
            $('select').niceSelect();
            let collapsButton = document.querySelectorAll('.collaps-expand-btn');
            collapsButton.forEach((item) => {


                item.addEventListener('click', function(e) {
                    let img = e.target.nextElementSibling
                    let button = this;
                    let dataId = item.getAttribute('data-target');
                    let element = document.querySelector(dataId);
                    let dataShadow = item.getAttribute('data-Shadow');
                    let icon = document.querySelector('.collaps-icon');
                    let shadowElement = document.querySelector(dataShadow);

                    if (button.textContent === 'Expand') {

                        element.classList.remove('custom-collapsed');

                        button.textContent = 'Collapse';
                        img.style.transform = 'rotate(180deg)'
                        shadowElement.classList.remove('shadow-container')

                    } else {

                        element.classList.add('custom-collapsed');
                        button.textContent = 'Expand';
                        img.style.transform = 'rotate(0deg)'
                        shadowElement.classList.add('shadow-container')

                    }
                });

            })


        })
    </script>
@endpush
