<template>
    <div
        class="modal modal-xl fade"
        id="upload-files-on-dropbox-modal"
        role="dialog"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        tabindex="-1"
        aria-labelledby="UploadFilesOnDropboxModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" style="max-width: 800px;">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button
                        type="button"
                        class="btn-close"
                        @click="closeModal(), resetState()"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body pt-0">
                    <vue3-dropzone
                        v-model="files"
                        :accept="['png', 'jpg', 'jpeg']"
                        :showSelectButton="true"
                        v-if="!isLoading"
                    >
                    </vue3-dropzone>
                </div>
                <div class="modal-footer border-0 pt-0 flex-nowrap">
                    <button
                        style="width: 50%; background: #fff; color: #000"
                        type="button"
                        class="apply-leave-btn"
                        @click="closeModal(), resetState()"
                    >
                        Cancel
                    </button>
                    <button
                        style="width: 50%"
                        type="button"
                        @click="handleSubmit"
                        class="apply-leave-btn"
                        :disabled="isLoading"
                    >
                        Upload Files
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineExpose, defineEmits, ref, watch, onMounted } from "vue";
import axios from "axios";
import { useRoute } from "vue-router";
import { useToast } from "vue-toastification";
import Vue3Dropzone from "@jaxtheprime/vue3-dropzone";
import "@jaxtheprime/vue3-dropzone/dist/style.css";

const emit = defineEmits(["handleUploadFilesResponse", "handleIsLoading"]);

const path = ref([]);
const breadcrumbName = ref([]);
const route = useRoute();
const files = ref([]);
const isLoading = ref(false);

const toast = useToast();

const openModal = (currentPath) => {
    breadcrumbName.value = currentPath.name;
    path.value = currentPath.path;
    const modalElement = document.getElementById(
        "upload-files-on-dropbox-modal"
    );
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
};

const closeModal = () => {
    const modalElement = document.getElementById(
        "upload-files-on-dropbox-modal"
    );
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
};

const resetState = () => {
    files.value = [];
};

const handleSubmit = async () => {
    try {
        if (files.value.length === 0) {
            return toast.warning("Kindly select file first.", {
                timeout: 3000,
                position: "top-right",
            });
        }
        closeModal();
        isLoading.value = true;
        emit("handleIsLoading", isLoading.value);
        const formData = new FormData();
        // Append files to FormData
        files.value.forEach((item, index) => {
            if (item.file instanceof File || item.file instanceof Blob) {
                formData.append(
                    `files[${index}]`,
                    item.file,
                    item.file.name || `file_${index}`
                );
            } else {
                console.error(`Invalid file at index ${index}:`, item.file);
            }
        });
        breadcrumbName.value;
        formData.append("path", path.value);
        formData.append("breadcrumbName", breadcrumbName.value);
        const response = await axios.post(
            `/projects/${route.params.project}/upload-dropbox-files`,
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        if (response?.data) {
            isLoading.value = false;
            emit("handleIsLoading", isLoading.value);
            emit("handleUploadFilesResponse", response.data?.content);
            closeModal();
            resetState();
            toast.success(response.data.message || "Success!", {
                timeout: 3000,
                position: "top-right",
            });
        }
    } catch (error) {
        isLoading.value = false;
        console.error(error);
        if (error.response?.status === 400) {
            toast.warning(error?.response?.data?.message || "Bad request!", {
                timeout: 3000,
                position: "top-right",
            });
        } else if (error.response?.status === 422) {
            if (error.response?.data?.errors) {
                syncErrorsWithVuelidate(error.response?.data?.errors);
            } else {
                console.error("Unexpected error:", error);
            }
        } else if (error.response?.status === 500) {
            toast.error(error?.response?.data?.message || "Server Error!", {
                timeout: 3000,
                position: "top-right",
            });
        } else {
            toast.error(error, {
                timeout: 3000,
                position: "top-right",
            });
        }
    }
};

defineExpose({
    openModal,
});
</script>
<style lang="scss">
.preview-container {
    width: auto !important;
    display: flex !important;
    flex-direction: row !important;
    .preview__multiple {
        width: 200px !important;
        height: 200px Im !important;
    }
}
.preview-container img {
    width: auto !important;
    height: auto !important;
}
</style>
