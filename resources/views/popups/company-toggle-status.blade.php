<div class="modal fade" id="toggle-company-status" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <!-- <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5> -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 text-center">
                <div class="d-flex flex-column gap-3">
                    <p class="delete-warning fw-bold text-capitalize">
                        You are about to <span class="companyStatusText"></span> your company account.  
                    </p>
                    <p class="">
                        Are you sure you want to <span class="companyStatusText"></span> the account?
                        <br>
                        You can <span class="companyStatusText-revers"></span> 
                    </p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 flex-nowrap px-5">
                <button style="width: 50%; background: #fff; color: #000;" type="button" class="apply-leave-btn"
                    id="closeModal">No</button>
                <button style="width: 50%;" type="button" class="apply-leave-btn" id="updateCompanyStatus"
                    data-company_uuid="{{ @$companyUuid }}">Yes</button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        $(document).ready(() => {

            $('.handleToggleStatus').click((e) => {

                const isActive = $(e.currentTarget).data('company_status') == 'active';
                $('.companyStatusText').text(isActive ? 'deactivate' : 'reactivate');
                $('.companyStatusText-revers').text(isActive ?  'reactivate it anytime by logging in again.' : 'deactivate it at anytime if you encounter any issues.');

                const companyUuid = $(e.currentTarget).data('uuid') || '';
                $('#updateCompanyStatus').attr('data-company_uuid', companyUuid);

                $('#toggle-company-status').modal('show');
            })

            $('#closeModal').click(() => {
                $('#toggle-company-status').modal('hide');
            })

            $('#updateCompanyStatus').click((e) => {
                const companyUuid = $(e.currentTarget).data('company_uuid');
                window.location.href = `/companies/toggle-status/${companyUuid}`;
            })

        })
    </script>
@endpush
