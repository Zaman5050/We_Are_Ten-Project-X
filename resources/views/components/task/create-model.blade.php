
@props([
    'project',
    'members',
    'taskStatuses',
    'stages',
])

<div class="modal fade" id="create-new-task" data-bs-backdrop="static" data-bs-keyboard="false" 
    aria-labelledby="staticBackdropLabel" aria-hidden="true" tabindex="0">
    <div class="modal-dialog create-project-dialoge">
        <store-task
            action="{{ tenant_route('task.store') }}"
            :project="{{ json_encode($project) }}"
            :members="{{ json_encode($members) }}"
            :task-statuses="{{ json_encode($taskStatuses) }}" 
            :stages="{{ json_encode($stages) }}"
        />
            
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(() => {
 

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
                        if (!response.error) {
                            window.location.reload();
                        } else {
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
                                console.log($('#' + field))
                                $('#' + field + '+ .invalid-feedback').text(errorBag[
                                    field][0]);
                                $('#' + field + ' + div +.invalid-feedback').text(errorBag[
                                    field][0]);
                            });
                        } else if (xhr.status == 500) {
                            $('.res-error').text(xhr.responseJSON ? xhr.responseJSON.message :
                                xhr.statusText).show();
                        }
                    },
                    complete: function() {
                        submitBtn.attr('disabled', false);
                    }
                });

            });


            $('#members').on('change', function(e) {

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
                    $('.teamMemberWrap').append('<input type="hidden" name="members[]" value="' +
                        member + '" />');
                    assignedMemberListHtml += '<li>' + member + '</li>';
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
