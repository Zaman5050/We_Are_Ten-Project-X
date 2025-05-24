<template>
    <div
        class="modal fade"
        id="add-participant"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        tabindex="-1"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body pt-0">
                    <h2 class="create-project-title mb-4">Add Participant</h2>

                    <multiselect
                        class="nav-input project-selector"
                        id="To"
                        v-model="selectedParticipant"
                        :close-on-select="false"
                        :options="props.members"
                        placeholder="Select to"
                        label="full_name"
                        track-by="uuid"
                        :multiple="true"
                        :taggable="false"
                        open-direction="bottom"
                        :show-labels="false"
                    >
                        <!-- <template #selection="{ values, search, isOpen }">
                            <span class="multiselect__single" v-if="values.length" v-show="!isOpen">{{ values.length }}
                                members selected</span>
                        </template> -->
                    </multiselect>

                    <ul v-if="selectedParticipant" class="list-group mt-3">
                        <li
                            class="list-group-item"
                            v-for="(member, key) in selectedParticipant"
                            :key="key"
                        >
                            {{ member.full_name }}
                        </li>
                    </ul>
                </div>
                <div class="modal-footer border-0 pt-0 flex-nowrap">
                    <button
                        style="width: 148px"
                        type="button"
                        class="apply-leave-btn"
                        id="addParticipents"
                        @click="handleSubmit"
                    >
                        Add
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
import Multiselect from "vue-multiselect";

import { useToast } from "vue-toastification";

const emit = defineEmits(["handleCreateFolderResponse", "handleIsLoading"]);
const props = defineProps({
    folder_and_files: {
        type: Array,
        default: [],
    },
    members: {
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

const toast = useToast();

const openModal = (data) => {
    state.folderPath = data.path;
    state.breadcrumbName = data.name;
    const modalElement = document.getElementById("add-participant");
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
};

const closeModal = () => {
    const modalElement = document.getElementById("add-participant");
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
    state.folder = "";
    state.folderPath = "";
    state.breadcrumbName = "";
};

const handleSubmit = async () => {
    try {
        let response;
        const data = {
            ...state,
            selectedParticipants: selectedParticipant.value,
        };

        isLoading.value = true;
        emit("handleIsLoading", isLoading.value);
        response = await axios.post(
            `/projects/${route.params.project}/share-dropbox-folder`,
            {
                data,
            }
        );
        console.log(response);
        if (response?.status == 200) {
            isLoading.value = false;
            emit("handleIsLoading", isLoading.value);
            toast.success("User added successfully to the shared folder", {
                timeout: 3000,
                position: "top-right",
            });
            emit("handleCreateFolderResponse", response.data);
            closeModal();
            
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

const selectedParticipant = ref([]);

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
