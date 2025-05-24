<div class="modal fade " id="delete-task" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <!-- <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5> -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="delete-warning">ARE YOU SURE YOU WANT TO DELETE THIS TASK ?</p>
            </div>
            <div class="modal-footer border-0 pt-0 flex-nowrap">
                <button style="width: 50%; background: #fff; color: #000;" type="button"
                    class="apply-leave-btn"  data-bs-dismiss="modal" aria-label="Close">No</button>
                <button style="width: 50%;" type="submit"  onclick="deleteTaskForm.submit()"  class="apply-leave-btn">Yes</button>
                <form action="{{ tenant_route('task.delete', ['taskUuid' => 'null' ]) }}" id="deleteTaskForm" hidden>
                    <button type="submit" >Yes</button>
                </form>
            </div>
        </div>
    </div>
</div>
