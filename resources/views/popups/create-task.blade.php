<div class="modal fade" id="create-new-task" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog create-project-dialoge">
        <div class="modal-content">
            <div class="modal-header border-0 create-new-task-header pb-0">
                <h5 style="font-size: 14px;" class="modal-title" id="task_code">BC-14</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body create-project-body">
                <h5 class="create-project-title mb-4" oninput="task_title.value = this.textContent" contenteditable>Type
                    Task Title</h5>
                <form action="{{ tenant_route('task.store') }}" method="POST" id="addTaskSubmitForm">
                    @csrf

                    <input type="hidden" id="task_title" name="title">
                    <input type="hidden" id="project_id" name="project_id" value="{{ @$projectId }}">

                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="sign-up-input-container h-auto mb-3">
                                <label for="description" class="signup-labels">PROJECT DESCRIPTION</label>
                                <textarea style="height: 168px; resize: none;" class="signup-inputs py-2 mw-100" placeholder="Project Description"
                                    name="description" id="description"></textarea>
                            </div>
                            <div class="sign-up-input-container mb-4" style="height: auto;">
                                <label for="addImage2" class="signup-labels">
                                    <img src="{{ asset('assets/images/add-img-icon.svg') }}">
                                    add Image
                                </label>
                                <input class="d-none" type="file" id="addImage2">
                            </div>
                            <div class="image-wrapper2"> </div>
                        </div>
                        <div class="col-md-6">
                            <div class="sign-up-input-container">
                                <label class="signup-labels">LABEL</label>
                                <div class="create-new-project-select-container">
                                    <select class="select signup-inputs" id="stage_uuid" name="stage_uuid">
                                        @foreach ($stages as $uuid => $stage)
                                            <option value="{{ $stage }}">stage {{ $stage }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="sign-up-input-container teamMemberWrap">
                                <label class="signup-labels">TEAM MEMBERS</label>
                                <input type="hidden" id="assignedMembers">
                                <div class="create-new-project-select-container">
                                    <select class="select " id="members">
                                        <option data-display="Select">Nothing</option>
                                        @foreach ($teamMembers as $member)
                                            <option value="{{ $member['uuid']}}">{{ $member['full_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <a class="create-one mb-4 d-block" style="font-family:'inter-Regular';font-size: 14px;">Add
                                Another Member</a>

                            <ul id="assignMemberList">

                            </ul>

                        </div>
                        <div class="col-md-6">
                            <div class="sign-up-input-container">
                                <label class="signup-labels">START DATE</label>
                                <input placeholder="2/10/2024" name="start_date" id="start_date"
                                    class="dashboard_button range-picker signup-inputs selector-single">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="sign-up-input-container">
                                <label class="signup-labels">DUE DATE</label>
                                <input placeholder="2/10/2024" name="due_date" id="due_date"
                                    class="dashboard_button range-picker signup-inputs selector-single">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="sign-up-input-container">
                                <label for="status_uuid" class="signup-labels">STATUS</label>
                                <div class="create-new-project-select-container">
                                    <select class="select signup-inputs" name="status_uuid" id="status_uuid">
                                        @foreach ($taskStatuses as $uuid => $statusTitle)
                                            <option value="{{ $uuid }}">{{ $statusTitle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                        </div>
                        <p class="col-12 my-3 res-error invalid-feedback"></p>
                        <div class="col-md-6 mb-4">
                            <button class="log-in submit-btn" style="max-width: 100%" type="submit">Submit</button>
                        </div>
                        <a class="create-one mb-4 d-block"
                            style="font-family:'inter-Regular';font-size: 14px; text-decoration: none;">Delete Task</a>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(() => {

            $('.add-new-task-btn').on('click', function(e) {
                resetErrMsg();
                $('#create-new-task').modal('show');
            })

            $('#addTaskSubmitForm').on('submit', function(e) {

                e.preventDefault();

                var formData = $(this).serialize();
                var submitBtn = $(this).find('.submit-btn');
                console.log(formData)

                $.ajax({
                    url: $(this).attr('action'), // Replace with your route
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    beforeSend: function(data) {
                        resetErrMsg();
                        submitBtn.attr('disabled', 'disabled');
                    },
                    success: function(response) {
                        console.log(response);
                        if(!response.error){
                            window.location.reload();
                        }else{
                            $('.res-success').text(response.message).show();
                            $('#addTaskSubmitForm')[0].reset();
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);


                        if (xhr.status == 422) {
                            var errorBag = xhr.responseJSON.errors;
                            Object.keys(errorBag).map((field) => {
                                $('#' + field).addClass('is-invalid');
                                $('#' + field + '+ .invalid-feedback').text(errorBag[
                                    field][0]);
                            });
                        } else if (xhr.status == 500) {
                            $('.res-error').text(xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText).show();
                        }
                    },
                    complete: function() {
                        submitBtn.attr('disabled', false);
                    }
                });

            });


            $('#members').on('change', function(e){

                // Get current values and the new value
                let assignedMemberList = $('#assignedMembers').val().split(',');
                let newMembers = $(this).val().split(',');

                // Combine current values and new values
                assignedMemberList = assignedMemberList.concat(newMembers);

                // Filter out empty strings and ensure unique values using Set
                assignedMemberList = [...new Set(assignedMemberList.filter(x => !!x))];

                // Update the input field
                $('#assignedMembers').val(assignedMemberList);

                var assignedMemberListHtml = '';

                var membersInputs = $('input[name="members[]"]');
                membersInputs && membersInputs.remove();

                assignedMemberList.map((member) => {
                    console.log(member)
                    $('.teamMemberWrap').append('<input type="hidden" name="members[]" value="'+member+'" />');
                    assignedMemberListHtml += '<li>'+member+'</li>';
                });

                // assignedMemberListHtml = assignedMemberList.map(x => '<li>'+x+'</li>');

                $('#assignMemberList').html(assignedMemberListHtml);
            });

        });
        

        function resetErrMsg() {
            $('.res-error, .res-success').text('').hide();
            $('#addTaskSubmitForm .signup-inputs').removeClass('is-invalid');
            $('#addTaskSubmitForm :is(.invalid-feedback, .valid-feedback)').text('');
        }
    </script>
@endpush
