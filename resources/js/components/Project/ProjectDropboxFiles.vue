<template>
    <div>
        <vue-loader v-if="isLoading" />
        <div class="d-flex justify-content-between flex-column">
            <div class="d-flex justify-content-between">
                <input
                    v-if="isdropboxconnected"
                    class="nav-input nav-content-mobile-hide"
                    placeholder="Search"
                    style="
                        background-image: url('http://ilsa.project-x.localhost/assets/images/search-icon.svg');
                    "
                />
                <button
                    class="btn btn-default border bg-yellow darker-grotesque-bold hover-bg ms-auto"
                    type="button"
                    @click="handleConnectToDropbox"
                >
                    {{
                        isdropboxconnected
                            ? "Disconnect From Dropbox"
                            : "Connect To Dropbox"
                    }}
                </button>
            </div>
            <div class="mt-3" v-if="isdropboxconnected && navLinks.length > 0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li
                            class="breadcrumb-item"
                            v-for="(link, index) in navLinks"
                            :key="index"
                            @click="
                                () => {
                                    if (!link.isActive) handleFolderClick(link);
                                }
                            "
                        >
                            <span
                                :class="[
                                    'breadcrumb-item',
                                    link.isActive ? 'active' : '',
                                ]"
                                >{{ link.name.split("/").pop() }}</span
                            >
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="mb-4 d-flex flex-row gap-5">
            <button
                class="apply-leave-btn"
                @click="handleCreateFolderClick"
                v-if="isdropboxconnected"
            >
                Create Folder
            </button>
            <button
                class="apply-leave-btn"
                @click="handleUploadFilesClick"
                v-if="isdropboxconnected"
            >
                Upload Files
            </button>
            <button
                class="apply-leave-btn"
                @click="handleShareFilesClick"
                v-if="isdropboxconnected"
            >
                Share Files
            </button>
        </div>

        <div class="create-project-container" v-if="state.content.length === 0">
            <div class="text-center">
                <img
                    style="width: 209px; height: 214px; object-fit: cover"
                    src="/assets/images/create-project-default.png"
                />
                <h2 style="margin-bottom: 12px" class="signup-headings">
                    You Don’t Have Any Files Yet.
                </h2>
            </div>
        </div>

        <div class="d-flex flex-column gap-5" v-else>
            <div class="project-listing-whole-container">
                <div
                    class="project-listing-main-container"
                    v-for="(row, index) in state.content"
                    :key="index"
                    @dblclick="handleFolderClick(row)"
                >
                    <div class="project-listing-box">
                        <div class="text-end mb-3">
                            <div class="">
                                <button
                                    class="edit-dell-btn"
                                    @click="openDeleteModal(row)"
                                >
                                    <img src="/assets/images/delete-icon.svg" />
                                </button>
                            </div>
                        </div>
                        <img
                            class="project-list-img"
                            :src="
                                row.type == 'file' &&
                                [
                                    'image/png',
                                    'image/jpeg',
                                    'image/webp',
                                ].includes(row.mimeType)
                                    ? row.link
                                    : '/assets/images/project-list-default.svg'
                            "
                        />

                        <div
                            class="mb-3 d-flex justify-content-between gap-3 align-items-center"
                        >
                            <p style="font-size: 14px">
                                {{ row.path.split("/").pop() }}
                            </p>
                        </div>
                        <div class="mb-3 d-flex justify-content-between gap-3">
                            <div style="font-size: 14px" class="">
                                <p style="color: #6e7c87">
                                    Last Edited by: {{ getFullName }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <delete-modal
        ref="deleteModal"
        :description="deleteDescription"
        :deleteContent="deleteContent"
        :modal_id="'delete-task-modal'"
        :url="'/delete-from-dropbox'"
    />
    <create-dropbox-folder-modal
        ref="createDropboxFolderModalRef"
        @handleCreateFolderResponse="handleCreateFolderResponse"
        :folder_and_files="state.content"
        @handleIsLoading="handleIsLoading"
    />
    <upload-files-on-dropbox-modal
        ref="uploadFilesOnDropboxModalRef"
        @handleUploadFilesResponse="handleUploadFilesResponse"
        @handleIsLoading="handleIsLoading"
    />
    <share-files-on-dropbox-modal
        ref="shareFilesOnDropboxModalRef"
        @handleShareFilesResponse="handleShareFilesResponse"
        :members="props.members"
        :folder_and_files="state.content"
        @handleIsLoading="handleIsLoading"
    />
</template>

<script setup>
import { nextTick, onMounted, reactive, ref, watch } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import VueLoader from "../Common/Loader.vue";
import DeleteModal from "@/components/Common/DeleteModal.vue";
import CreateDropboxFolderModal from "@/components/Project/CreateDropboxFolderModal.vue";
import UploadFilesOnDropboxModal from "@/components/Project/UploadFilesOnDropboxModal.vue";
import ShareFilesOnDropboxModal from "@/components/Project/ShareFilesOnDropboxModal.vue";
import { useToast } from "vue-toastification";
import { useUserStore } from "../../stores/userStore";
import { storeToRefs } from "pinia";

const route = useRoute();
const toast = useToast();
const isLoading = ref(false);
const userStore = useUserStore();
const props = defineProps({
    contentlist: {
        type: Array,
        default: [],
    },
    isdropboxconnected: {
        default: 0,
    },
    members: {
        type: Array,
        default: () => [],
    },
});

const state = reactive({
    content: props.contentlist,
});

const navLinks = ref([]);
const { getFullName } = storeToRefs(userStore);

const createDropboxFolderModalRef = ref(null);
const uploadFilesOnDropboxModalRef = ref(null);
const shareFilesOnDropboxModalRef = ref(null);

const handleFolderClick = async (link) => {
    if (link.type == "file") return window.open(link.link, "_blank");
    isLoading.value = true;
    syncNavLinks(link);
    try {
        const response = await axios.get(
            `/projects/${route.params.project}/files`,
            {
                params: {
                    data: link,
                },
                headers: {
                    "Content-Type": "application/json", // Specify JSON content type
                    "X-Requested-With": "XMLHttpRequest", // Ensure AJAX request is recognized
                },
            }
        );
        if (response.data) {
            state.content = response.data.contentList;
        }
    } catch (error) {
        console.error("Error creating project:", error);
    } finally {
        isLoading.value = false;
    }
};

const handleConnectToDropbox = async () => {
    isLoading.value = true;
    try {
        let response;
        if (props.isdropboxconnected) {
            response = await axios.post("/disconnect-dropbox");
        } else {
            response = await axios.post("/get-dropbox-authurl", {
                project_uuid: route.params.project,
            });
        }
        if (response.data) {
            toast.success(response.data.message || "Success!", {
                timeout: 3000,
                position: "top-right",
            });
            nextTick(() => {
                if (props.isdropboxconnected) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 3000);
                } else {
                    window.location.href = response.data.dropboxAuthUrl;
                }
            });
        }
    } catch (error) {
        console.error("Error creating project:", error);
    } finally {
        isLoading.value = false;
    }
};

const deleteModal = ref(null);
const deleteDescription = ref("");
const deleteContent = ref("");

const openDeleteModal = (content) => {
    const fileName = content.path.split("/").pop();
    deleteDescription.value = `Are you sure you want to delete ${fileName}?`;
    deleteContent.value = content;
    deleteModal.value.openModal();
};

const syncNavLinks = (link) => {
    if (link.breadcrumbLink) {
        navLinks.value = link.breadcrumbLink
            .substring(1)
            .split("/")
            .map(({ path }, index) => ({
                name:
                    navLinks.value[index].path === "/"
                        ? "Home"
                        : navLinks.value[index].name,
                path: navLinks.value[index].path,
                breadcrumbLink: navLinks.value[index].breadcrumbLink,
                isActive:
                    link.breadcrumbLink.split("/").length - 1 === index
                        ? true
                        : false,
            }));
    } else {
        navLinks.value = navLinks.value.map((currentLink) => ({
            ...currentLink,
            isActive: false,
        }));
        navLinks.value.push({
            name: link.path,
            path: `/${link.path}`,
            breadcrumbLink:
                navLinks.value.length > 1
                    ? `${
                          navLinks.value[navLinks.value.length - 1]
                              .breadcrumbLink
                      }/${link.path}`
                    : `/Home/${link.path}`,
            isActive: true,
        });
    }
};
const handleCreateFolderClick = () => {
    const lastDirectory = navLinks.value[navLinks.value.length - 1];
    createDropboxFolderModalRef.value.openModal(lastDirectory);
};
const handleUploadFilesClick = () => {
    const lastDirectory = navLinks.value[navLinks.value.length - 1];
    uploadFilesOnDropboxModalRef.value.openModal(lastDirectory);
};

const handleShareFilesClick = () => {
    const lastDirectory = navLinks.value[navLinks.value.length - 1];
    shareFilesOnDropboxModalRef.value.openModal(lastDirectory);
};

const handleUploadFilesResponse = (content) => {
    state.content = content;
};

const handleShareFilesResponse = (response) => {
    state.content.push({
        type: "dir",
        path: response?.data?.path_display,
    });
};

const handleIsLoading = (value) => {
    isLoading.value = value;
};

onMounted(() => {
    nextTick(() => updateNavLinks());
});
const handleCreateFolderResponse = (response) => {
    state.content.push({
        type: "dir",
        path: response?.data?.path_display,
    });
};
const updateNavLinks = () => {
    if (route.params.project) {
        navLinks.value.push({
            name: "Home",
            path: `/${route.params.project}`,
            breadcrumbLink: "/",
            isActive: true,
        });
    } else {
        console.warn("Route params not available");
    }
};
watch(() => route.params.project, updateNavLinks);
</script>
<style lang="scss" scoped>
.project-listing-box,
.breadcrumb-item {
    cursor: pointer;
    &.active {
        cursor: not-allowed;
    }
}
</style>
