<div class="modal fade" id="apply-leave-popup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <leave-modal :authuserid="{{ auth()->id() }}" :authuserleaves="{{ json_encode(auth()->user()->remaining_leaves) }}"/>
            </div>
        </div>
    </div>
</div>
