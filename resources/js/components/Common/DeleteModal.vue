<template>
    <slot></slot>
    <div
        class="modal fade"
        :id="modal_id"
        role="dialog"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        tabindex="-1"
        :aria-labelledby="`${modal_id}Label`"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button
                        type="button"
                        class="btn-close"
                        @click="closeModal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="delete-warning">{{ description }}</p>
                </div>
                <div class="modal-footer border-0 pt-0 flex-nowrap">
                    <button
                        style="width: 50%; background: #fff; color: #000"
                        type="button"
                        class="apply-leave-btn"
                        @click="closeModal"
                    >
                        No
                    </button>
                    <button
                        style="width: 50%"
                        type="button"
                        @click="handleSubmit"
                        class="apply-leave-btn"
                    >
                        Yes
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineExpose, ref } from "vue";
import axios from "axios";
const props = defineProps({
    description: {
        type: String,
        default: "Are you sure you want to delete this?",
    },
    modal_id: {
        type: String,
        default: "delete-modal",
    },
    url: {
        type: String,
        required: true,
    },
    deleteContent: {
        type: Array,
        default: [],
    },
});

const openModal = () => {
    const modalElement = document.getElementById(props.modal_id);
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
};

const closeModal = () => {
    const modalElement = document.getElementById(props.modal_id);
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
};

const handleSubmit = async () => {
    try {
        const response = await axios.delete(props.url, {
            data: { content: props.deleteContent },
            headers: {
                "Content-Type": "application/json",
            },
        });
        if (response?.data) {
            closeModal();
            if (response?.data?.redirectedUrl) {
                return (window.location.href = response?.data?.redirectedUrl);
            }
            window.location.reload();
        }
    } catch (error) {
        console.error("Member removal failed:", error);
        closeModal();
    }
};

defineExpose({
    openModal,
});
</script>
