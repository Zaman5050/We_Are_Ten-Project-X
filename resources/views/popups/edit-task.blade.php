<div class="modal fade" id="edit-task-model" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog create-project-dialoge">
        <div class="modal-content">
            <div class="modal-header border-0 create-new-task-header pb-0">
                <h5 style="font-size: 14px;" class="modal-title ">BC-14</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body create-project-body">
                <h5 class="create-project-title pb-4" oninput="taskTitleInput.value = this.innerText" contenteditable>Type Task Title</h5>
                <form>

                    <input class="d-none" type="text" id="taskTitleInput" name="title">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="sign-up-input-container h-auto mb-3">
                                <label class="signup-labels">PROJECT DESCRIPTION</label>
                                <textarea style="height: 168px; resize: none;" class="signup-inputs py-2 mw-100" placeholder="Project Description"></textarea>
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
                                    <select class="select ">
                                        <option>Stage 3</option>
                                        <option>Stage 2</option>
                                        <option>Stage 1</option>
                                    </select>
                                </div>
                            </div>
                            <div class="sign-up-input-container">
                                <label class="signup-labels">TEAM MEMBERS</label>
                                <div class="create-new-project-select-container">
                                    <select class="select ">
                                        <option>Team Members</option>
                                        <option>Not team Members</option>
                                    </select>
                                </div>
                            </div>
                            <a class="create-one mb-4 d-block" style="font-family:'inter-Regular';font-size: 14px;">Add
                                Another Member</a>

                        </div>
                        <div class="col-md-6">
                            <div class="sign-up-input-container">
                                <label class="signup-labels">START DATE</label>
                                <input name="custom_date_range" placeholder="2/10/2024"
                                    class="dashboard_button range-picker signup-inputs selector-single">
                            </div>
                            <div class="sign-up-input-container">
                                <label class="signup-labels">DUE DATE</label>
                                <input name="custom_date_range" placeholder="2/10/2024"
                                    class="dashboard_button range-picker signup-inputs selector-single">
                            </div>
                            <div class="sign-up-input-container">
                                <label class="signup-labels">STATUS</label>
                                <div class="create-new-project-select-container">
                                    <select class="select ">
                                        <option>To Do</option>
                                        <option>In Progress</option>
                                        <option>In Review</option>
                                        <option>Completed</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6 mb-4">
                            <a style="max-width: 100%" class="log-in">Submit</a>
                        </div>
                        <a class="create-one mb-4 d-block"
                            style="font-family:'inter-Regular';font-size: 14px; text-decoration: none;">Delete Task</a>
                    </div>

                </form>
            </div>
            <!-- <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Send message</button>
        </div> -->
        </div>
    </div>
</div>
