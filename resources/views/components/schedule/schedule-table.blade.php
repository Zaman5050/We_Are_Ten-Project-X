@props([
'categoriesWithSchedules' => [],
])
<div class="selected-material-container">
    @php($index=0)
    @foreach ($categoriesWithSchedules as $category => $categoryWithSchedules)
    @php($index +=1)
    <div class="" id="shadow-container-{{$index}}">
        <div class="d-flex justify-content-between align-items-center ">
            <h6 style="color:#000" class="task-stage-heading">{{ $category }} ({{count($categoryWithSchedules)}})</h6>
            <div class="collaps-container">
                <button class="collaps-expand-btn" data-target="#schedule-table-{{$index}}" data-Shadow="#shadow-container-{{$index}}">
                    Collapse
                </button>
                <img class="collaps-icon" src="{{ asset('assets/images/arrow-icon.svg') }}">
            </div>
        </div>
        <div class="task-table-container">
            <table class="schedule-table table my-2" id="schedule-table-{{$index}}">
                <tbody>
                    @foreach ($categoryWithSchedules as $schdeule)
                    <tr class="table-body myDiv" id="div1">
                        <td>
                            <label class="check-container selected-material-row">
                                <input type="checkbox" class="myCheckbox" id="checkbox1" data-target="div1">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td class="pe-0">
                            <div style="width:110px">

                                <img style="width: 110.3px; height: 117px; object-fit: cover; border-radius: 5.76px;"
                                    src="{{ isset($schdeule->material->cover_image) && count($schdeule->material->cover_image) > 0 ? $schdeule->material->cover_image[0]['url'] : asset('assets/images/project-list-default.svg') }}">
                            </div>
                        </td>
                        <td class="ps-0">
                            <div style="height: 96.9px; padding-left: 11px;"
                                class="d-flex flex-column justify-content-between">
                                <p>{{ $category }}</p>
                                <p>{{ $schdeule->material->sku }}</p>
                            </div>
                        </td>
                        <td>
                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                <div class="">
                                    <p style="white-space: nowrap;">{{ $schdeule->material->item_name }}</p>
                                    <span style="color: #C4C4C4;white-space: nowrap;">Product Name</span>
                                </div>
                                <div class="">
                                    <p style="white-space: nowrap;">{{ $schdeule->material->brand_name ?? '-'}}</p>
                                    <span style="color: #C4C4C4;white-space: nowrap;">Brand</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                <div class="">
                                    <p style="white-space: nowrap;">{{ $schdeule->material->width ?? '-' }}</p>
                                    <span style="color: #C4C4C4;white-space: nowrap;">Width (MM)</span>
                                </div>
                                <div class="">
                                    <p style="white-space: nowrap;">{{ $schdeule->material->color ?? '-' }}</p>
                                    <span style="color: #C4C4C4;white-space: nowrap;">Colour</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                <div class="">
                                    <p style="white-space: nowrap;">{{ $schdeule->material->length ?? '-' }}</p>
                                    <span style="color: #C4C4C4;white-space: nowrap;">Length (MM)</span>
                                </div>
                                <div class="">
                                    <p style="white-space: nowrap;">{{ $schdeule->material->height ?? '-' }}</p>
                                    <span style="color: #C4C4C4;white-space: nowrap;">Heigh (MM)</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                <div class="">
                                    <p style="white-space: nowrap;">{{ $schdeule->material->depth ?? '-' }}</p>
                                    <span style="color: #C4C4C4;white-space: nowrap;">Depth (MM)</span>
                                </div>
                                <div class="">
                                    <p style="white-space: nowrap;">{{ $schdeule->material->finish ?? '-' }}</p>
                                    <span style="color: #C4C4C4;white-space: nowrap;">Finish</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                <div class="">
                                    <div class="dropdown three-dots-dropdown">
                                        <p style="width: fit-content; "
                                            class="d-flex justify-content-end filter-btn border-0 gap-3 darker-grotesque-regular"
                                            >
                                            Areas with Quantity
                                    </p>
                                        <ul 
                                            class="dropdown-menu border-0 schedule-area-dropdown" aria-labelledby="dropdownMenuLink">
                                            @foreach ($schdeule->areas ?? [] as $area)
                                            <li><a>{{ $area->area_name }} {{ $area?->pivot?->quantity }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @if( count($schdeule->areas) > 0 )
                                        @foreach ($schdeule->areas ?? [] as $area)
                                            <li>    
                                                <span>{{ $area->area_name }}</span>
                                                <span class="mx-3"> {{ $area?->pivot?->quantity  }}</span> 
                                            </li>
                                        @endforeach
                                    @endif
                                </div>
                                <div>

                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="gap: 28px;" class="d-flex flex-column justify-content-between">
                                <schedule-detail-button :schedule="{{ json_encode($schdeule) }}" />
                            </div>

                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
    @endforeach


</div>
@push('scripts')
<script>
    $(document).ready(function() {

        let collapsButton = document.querySelectorAll('.collaps-expand-btn');
        collapsButton.forEach((item) => {


            item.addEventListener('click', function(e) {
                console.log(item)
                let img = e.target.nextElementSibling;
                var button = this;
                let dataId = item.getAttribute('data-target');
                let dataShadow = item.getAttribute('data-Shadow');

                let element = document.querySelector(dataId);
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