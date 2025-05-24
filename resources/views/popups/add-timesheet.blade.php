<div class="modal fade " id="add-time-entry-popup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header border-0">
                <!-- <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5> -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <h5 class="modal-title">Add Time Entry</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="sign-up-input-container">
                            <label class="signup-labels">START DATE</label>
                            <input name="custom_date_range " placeholder="2/10/2024"
                                class="dashboard_button range-picker signup-inputs selector flatpickr-input mw-100"
                                type="text" readonly="readonly">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="sign-up-input-container">
                            <label class="signup-labels">END DATE</label>
                            <input name="custom_date_range " placeholder="02/10/2024"
                                class="dashboard_button range-picker signup-inputs selector flatpickr-input mw-100"
                                type="text" readonly="readonly">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="sign-up-input-container">
                            <label class="signup-labels">PROJECT</label>
                            <div class="create-new-project-select-container">
                                <select class="select " style="display: none;">
                                    <option>Insite</option>
                                    <option>LawFlip</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="sign-up-input-container">
                            <label class="signup-labels">TASK</label>
                            <div class="create-new-project-select-container">
                                <select class="select " style="display: none;">
                                    <option>Design Moodboard</option>
                                    <option>Sick</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="sign-up-input-container only-time-picker">
                            <label class="signup-labels">START TIME</label>
                            <input class="signup-inputs selector2 mw-100" placeholder="11:05 ">

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="sign-up-input-container only-time-picker">
                            <label class="signup-labels">END TIME</label>
                            <input class="signup-inputs selector2 mw-100" placeholder="Enter End Time ">

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="sign-up-input-container ">
                            <label class="signup-labels">HOURS SPENT</label>
                            <input class="signup-inputs mw-100" placeholder="10">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="sign-up-input-container">
                            <label class="signup-labels">MINUTES SPENT</label>
                            <input class="signup-inputs mw-100" placeholder="10">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-start flex-column align-items-start">
                <a style="max-width: 417.5px;" class="log-in">Submit</a>
                <a class="create-one my-4 d-block" style="font-family:'inter-Regular';font-size: 14px;">Delete Entry</a>
            </div>

        </div>
    </div>
</div>


@push('scripts')
    <script>
        $(".selector2").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            // time_24hr: true
        });
    </script>
@endpush
