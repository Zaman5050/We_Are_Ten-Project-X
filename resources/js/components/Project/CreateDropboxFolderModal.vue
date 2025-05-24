<template>
    <div
        class="modal fade"
        id="dropbox-create-folder"
        role="dialog"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        tabindex="-1"
        aria-labelledby="DropboxCreateFolderLabel"
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
                    <div class="sign-up-input-container">
                        <label for="projectAddress" class="signup-labels"
                            >Folder Name</label
                        >
                        <input
                            class="signup-inputs mw-100"
                            placeholder="Enter Folder Name"
                            type="text"
                            v-model="state.folder"
                            @input="v$.$reset()"
                        />
                        <input-validation-error-message :v="v$?.folder" />
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 flex-nowrap">
                    <button
                        style="width: 50%; background: #fff; color: #000"
                        type="button"
                        class="apply-leave-btn"
                        @click="closeModal"
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
                        Create Folder
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    defineExpose,
    reactive,
    computed,
    defineEmits,
    ref,
    watch,
    onMounted,
} from "vue";
import axios from "axios";
import { useRoute } from "vue-router";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers } from "@vuelidate/validators";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { useToast } from "vue-toastification";

const emit = defineEmits(["handleCreateFolderResponse", "handleIsLoading"]);
const props = defineProps({
    folder_and_files: {
        type: Array,
        default: [],
    },
});

const folderAndFiles = ref([]);
const route = useRoute();
const state = reactive({
    folder: "",
    folderPath: "",
    breadcrumbName: "",
});
const isLoading = ref(false);

const rules = computed(() => {
    return {
        folder: {
            required: helpers.withMessage("Folder name is required.", required),
            uniqueFolderName,
        },
    };
});
const uniqueFolderName = helpers.withMessage(
    "Folder already exist.",
    (value) => {
        // If value is empty, validation should not fail here (let `required` handle it).
        if (!value) return true;
        // Check if the folder name already exists in folderAndFiles.
        return !folderAndFiles.value.some(
            (link) =>
                link.type === "dir" &&
                link?.path
                    ?.split(`${route.params.project}/`)
                    ?.pop()
                    ?.toLowerCase() === value.trim().toLowerCase()
        );
    }
);
const v$ = useVuelidate(rules, state);
const toast = useToast();

const openModal = (data) => {
    state.folderPath = data.path;
    state.breadcrumbName = data.name;
    const modalElement = document.getElementById("dropbox-create-folder");
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
};

const closeModal = () => {
    const modalElement = document.getElementById("dropbox-create-folder");
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
    state.folder = "";
    state.folderPath = "";
    state.breadcrumbName = "";
    v$.value.$reset();
};

const handleSubmit = async () => {
    try {
        v$.value.$touch();
        if (v$.value.$pending) return;
        if (v$.value.$invalid) {
            return false;
        }
        isLoading.value = true;
        emit("handleIsLoading", isLoading.value);
        const response = await axios.post(
            `/projects/${route.params.project}/create-dropbox-folder`,
            {
                ...state,
            }
        );
        if (response?.data) {
            isLoading.value = false;
            emit("handleIsLoading", isLoading.value);
            emit("handleCreateFolderResponse", response.data);
            closeModal();
            toast.success(response.data.message || "Success!", {
                timeout: 3000,
                position: "top-right",
            });
        }
    } catch (error) {
        isLoading.value = false;
        emit("handleIsLoading", isLoading.value);
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

onMounted(() => {
    folderAndFiles.value = props.folder_and_files;
});

watch(
    props.folder_and_files,
    (value) => {
        folderAndFiles.value = value;
    },
    { deep: true }
);

defineExpose({
    openModal,
});
</script>
