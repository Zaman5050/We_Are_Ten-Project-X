<div class="modal fade" id="change-password-model" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 px-5">
                <div class="d-flex flex-column gap-3">
                <h5 class="modal-title create-project-title" id="staticBackdropLabel">Change Password</h5>
                    <form method="POST" action="{{ route('super-admin.company.changePassword', @$adminUuid) }}"
                        id="submitChangePassword" name="submitChangePassword" class="g-2">
                        @csrf

                        <div class="sign-up-input-container">
                            <label class="signup-labels">Old PASSWORD</label>
                            <input class="bg-input signup-inputs" placeholder="********" type="password"
                                autocomplete="new-password" name="old_password" id="old_password">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="sign-up-input-container">
                            <label class="signup-labels">NEW PASSWORD</label>
                            <input class="bg-input signup-inputs" placeholder="********" type="password"
                                autocomplete="new-password" name="password" id="password">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="sign-up-input-container">
                            <label class="signup-labels">CONFIRM PASSWORD</label>
                            <input class="bg-input signup-inputs" placeholder="********" type="password"
                                name="password_confirmation" id="password_confirmation" autocomplete="new-password">
                            <div class="invalid-feedback"></div>
                        </div>
                    </form>

                    <p class="res-error invalid-feedback"></p>
                    <p class="res-success valid-feedback"></p>

                </div>
            </div>
            <div class="modal-footer border-0 pt-0 flex-nowrap px-5 pb-4">
                <button style="width: 50%; background: #fff; color: #000;" id="closeBtn" type="button"
                    class="apply-leave-btn" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                <button type="submit" style="width: 50%;" form="submitChangePassword" id="changePasswordBtn"
                    class="apply-leave-btn">Save</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(() => {

            $('.handleChangePassword').on('click', function(e) {
                resetErrMsg();
                $('#change-password-model').modal('show');
                var userUuid = $(this).attr('data-uuid') || null;
                var dynamicUrl = "{{ config('app.url') }}" + "change-password/" + userUuid;
                console.log(userUuid, dynamicUrl);
                $('#submitChangePassword').attr('action', dynamicUrl);
            })

            $('#submitChangePassword').on('submit', function(e) {

                e.preventDefault();

                var formData = $(this).serialize();
                // console.log(formData)

                $.ajax({
                    url: $(this).attr('action'), // Replace with your route
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function(data) {
                        resetErrMsg();
                        $('#changePasswordBtn').attr('disabled', 'disabled');
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.error) {
                            $('.res-error').text(response.message).show();
                        } else {

                            if (response.redirect) {
                                window.location.reload();
                            } else {
                                $('.res-success').text(response.message).show();
                                $('#submitChangePassword')[0].reset();
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);

                        $('#changePasswordBtn').attr('disabled', false);

                        if (xhr.status == 422) {
                            var errorBag = xhr.responseJSON.errors;
                            Object.keys(errorBag).map((field) => {
                                $('#' + field).addClass('is-invalid');
                                $('#' + field + '+ .invalid-feedback').text(errorBag[
                                    field][0]);
                            });
                        } else if (xhr.status == 500) {
                            $('.res-error').text(xhr.responseJSON.message).show();
                        }
                    },
                    // complete: function() {}
                });

            })

        });

        function resetErrMsg() {
            $('.res-error, .res-success').text('').hide();
            $('#submitChangePassword .signup-inputs').removeClass('is-invalid');
            $('#submitChangePassword .invalid-feedback').text('');
            $('#submitChangePassword .valid-feedback').text('');
        }
    </script>
@endpush
