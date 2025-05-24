<div class="sales-report-area">
    <div class="dashboard-button-container mb-5">
        @includeIf('includes.finance.finance-navbar')
    </div>
    <div class="tab-content">
        <div class="tab-pane fade show active">
            {{ $slot }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {

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