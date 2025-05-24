<div class="modal fade" id="create-new-project" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog create-project-dialoge">
        <div class="modal-content">
            <div class="modal-header create-project-header">
                <h5 class="modal-title create-project-title" id="exampleModalLabel">Create New Project</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body create-project-body">
                <create-project
                    :designers="{{ $designers }}"
                    :currencies="{{ $activeCurrencies }}"
                    :check-auth-user="{{ json_encode(auth()->user()->hasRole(auth()->user()::ROLE_ADMIN)) }}"
                  />
            </div>
            <!-- <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Send message</button>
        </div> -->
        </div>
    </div>
</div>
