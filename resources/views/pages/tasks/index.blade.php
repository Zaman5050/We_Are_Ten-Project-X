@extends('layouts.tenant.project')

@section('title', 'Tasks')

@section('project-content')        
    <task-listings
        :tasks="{{ json_encode($project->tasks) }}"
        :stages="{{ json_encode($project->stages) }}"
        :task-statuses="{{ json_encode($taskStatuses) }}"
        :members="{{json_encode($teamMembers)}}"
    ></task-listings>

    <!-- main content area end -->

    <x-task.create-model :project="$project" :taskStatuses="$taskStatuses" :stages="$stages" :members="$teamMembers" />

    @includeIf('popups.delete-tasks')

    @push('styles')
        <style>
            .action-wrap {
                min-width: 77px
            }

            .task-ID {
                white-space: nowrap
            }

            .task-title {
                max-width: 200px;
                cursor: pointer;
            }

            .task-title:hover {
                text-decoration: underline;
            }

            .create-project-title:focus {
                outline: 2px solid lightblue;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $('#addImage').on('change', function(evt) {
                var selectedImage = evt.currentTarget.files[0];
                var imageWrapper = document.querySelector('.image-wrapper');
                var theImage = document.createElement('img');
                imageWrapper.innerHTML = '';

                var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp)$/;
                if (regex.test(selectedImage.name.toLowerCase())) {
                    if (typeof(FileReader) != 'undefined') {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            theImage.id = 'new-selected-image';
                            theImage.src = e.target.result;
                            imageWrapper.appendChild(theImage);
                        }
                        //
                        reader.readAsDataURL(selectedImage);
                    } else {
                        //-- Let the user knwo they cannot peform this as browser out of date
                        console.log('browser support issue');
                    }
                } else {
                    //-- no image so let the user knwo we need one...
                    $(this).prop('value', null);
                    console.log('please select and image file');
                }

            });


            $('#addImage2').on('change', function(evt) {
                var selectedImage = evt.currentTarget.files[0];
                var imageWrapper = document.querySelector('.image-wrapper2');
                var theImage = document.createElement('img');
                imageWrapper.innerHTML = '';

                var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp)$/;
                if (regex.test(selectedImage.name.toLowerCase())) {
                    if (typeof(FileReader) != 'undefined') {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            theImage.id = 'new-selected-image';
                            theImage.src = e.target.result;
                            imageWrapper.appendChild(theImage);
                        }
                        //
                        reader.readAsDataURL(selectedImage);
                    } else {
                        //-- Let the user knwo they cannot peform this as browser out of date
                        console.log('browser support issue');
                    }
                } else {
                    //-- no image so let the user knwo we need one...
                    $(this).prop('value', null);
                    console.log('please select and image file');
                }

            });




            $(document).ready(function() {

                $(document).on('click', '.taskEdit', function(e) {
                    e.preventDefault();
                    openModel('create-new-task');
                });

               
                $(document).on('click', '.taskDeleted', function(e) {
                    e.preventDefault();
                    var taskUuid = $(this).attr('data-uuid') || null;
                    var deleteUrl = BASE_URL + '/task/delete/' + taskUuid
                    $('form#deleteTaskForm').attr('action', deleteUrl);
                });


                $('.markArchived').on('click', function(e) {
                    e.preventDefault();
                    var taskTr = $('tr#' + $(this).attr('data-tr'));
                    var taskUuid = taskTr && taskTr.attr('data-task-uuid');
                    console.log(taskUuid);

                    $.ajax({
                        url: BASE_URL + '/task/mark-archived/' + taskUuid,
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);

                            if (response == 1) {
                                taskTr.remove();
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr);
                        },
                    });
                });

                $('.markFlag').on('click', function(e) {
                    e.preventDefault();
                    var taskTr = $('tr#' + $(this).attr('data-tr'));
                    var taskUuid = taskTr && taskTr.attr('data-task-uuid');
                    console.log(taskUuid);

                    $.ajax({
                        url: BASE_URL + '/task/mark-flag/' + taskUuid,
                        method: 'GET',
                        dataType: 'json',
                        beforeSend: function(data) {

                        },
                        success: function(response) {
                            console.log(response);

                            if (response.status) {
                                if (taskTr.find('img.red-flag').length === 0) {
                                    taskTr.find('.action-wrap').prepend(
                                        "<img class=red-flag src={{ asset('assets/images/red-flag.svg') }}>"
                                    );
                                }
                            } else {
                                taskTr.find('img.red-flag').remove();
                            }

                        },
                        error: function(xhr) {
                            console.log(xhr);
                        },
                        // complete: function() {}
                    });
                });

                // update status of task
                $('.status-dropdown > li > a').on('click', function(e) {
                    e.preventDefault();
                    var taskTr = $('tr#' + $(this).attr('data-tr'));
                    var taskUuid = taskTr && taskTr.attr('data-task-uuid');
                    var statusUuid = $(this).attr('data-value') || 'null';
                    var updatedStatus = $(this).text();
                    $.ajax({
                        url: BASE_URL + '/task/update-status/' + taskUuid + '/' +
                            statusUuid, // Replace with your route
                        method: 'GET',
                        dataType: 'json',
                        beforeSend: function(data) {},
                        success: function(response) {
                            console.log(response, taskTr.find('.status-title'));
                            if (response == 1) {
                                taskTr.find('span.status-title').text(updatedStatus);
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr);
                        },
                        // complete: function() {}
                    });
                });


                $(".selector").flatpickr({
                    mode: "range",
                    dateFormat: "m-d-Y",
                });
                $(".selector-single").flatpickr({
                    // mode: "range",
                    dateFormat: "Y-m-d"
                });
            });


            $(document).ready(function() {
                // Show the first tab and its content by default
                $('.tab-img').first().addClass('active-sub-tab');
                $('.tab-div').first().show();

                $('.tab-img').click(function() {
                    // Remove 'active' class from all buttons
                    $('.tab-img').removeClass('active-sub-tab');
                    // Add 'active' class to the clicked button
                    $(this).addClass('active-sub-tab');

                    // Hide all tab divs
                    $('.tab-div').hide();
                    // Show the selected tab content
                    $('#' + $(this).data('tab')).show();
                });
            });


            function openModel(id) {
                var bsModel = document.getElementById(id);
                bsModel && new bootstrap.Modal(bsModel).show();
            }

            function hideModel(id) {
                var bsModel = document.getElementById(id);
                bsModel && new bootstrap.Modal(bsModel).hide();
            }

            function setTaskInModel(task) {

                $('#description').val(task.description);

            }
        </script>
    @endpush
@endsection
