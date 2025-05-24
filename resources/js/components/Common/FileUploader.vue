<template>
    <vue-loader v-if="isLoading" />
    <div v-if="title_view">
        <div class="upload__box-only-name">
            <div class="upload__btn-box-only-name">
                <label for="addAttachment" style="cursor: pointer">
                    <img src="/assets/images/add-img-icon.svg" /> &nbsp;
                    <u>Add Attachment</u>

                    <input
                        id="addAttachment"
                        type="file"
                        @change="onFilesChange"
                        :accept="acceptedFileTypes"
                        :multiple="multiple"
                        :disabled="disabled"
                        data-max_length="20"
                        class="upload__inputfile-only-name"
                    />
                </label>
            </div>
            <div
                v-if="displayData.length"
                :class="[
                    'upload__file-wrap-only-name',
                    multiple ? 'flex-column-reverse' : '',
                ]"
            >
                <div
                    v-for="(file, index) in displayData"
                    :key="index"
                    :class="['file-container', multiple ? 'pdf-name-box' : '']"
                >
                    <template v-if="file.isImage">
                        <img
                            :src="getFileUrl(file)"
                            :alt="'Image ' + index"
                            class="img-block"
                        />
                    </template>
                    <template v-else>
                        <!-- Display PDFs with an iframe preview and a download/view link -->
                        <div class="pdf-container">
                            <a
                                :href="getFileUrl(file)"
                                target="_blank"
                                style="color: #000"
                                class="file-name"
                                >{{ file.name }}</a
                            >

                            <a
                                :href="getFileUrl(file)"
                                target="_blank"
                                class="file-link"
                            >
                            </a>
                        </div>
                    </template>
                    <button
                        v-if="file.isImage"
                        type="button"
                        @click.prevent="removeFile(index, file)"
                        class="remove-image-button"
                    >
                        &times;
                    </button>
                    <button
                        v-else
                        type="button"
                        @click.prevent="removeFile(index, file)"
                        class="remove-image-button"
                    >
                        <svg
                            data-v-357d04b8=""
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            fill="#ACACAC"
                            class="bi bi-trash text-white"
                            viewBox="0 0 16 16"
                            style="filter: invert(1)"
                        >
                            <path
                                data-v-357d04b8=""
                                d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"
                            ></path>
                            <path
                                data-v-357d04b8=""
                                d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"
                            ></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4" v-else>
        <div class="d-flex justify-content-center">
            <div class="upload__box-only-name w-100">
                <div
                    class="upload__file-wrap-only-name specially-for-name"
                ></div>
                <div
                    :class="[
                        'upload__btn-box-only-name d-flex justify-content-center gap-3',
                        multiple ? 'flex-column-reverse' : '',
                    ]"
                >
                    <div
                        v-if="displayData.length"
                        :class="[
                            'upload__file-wrap-only-name',
                            multiple ? 'flex-column-reverse' : '',
                        ]"
                    >
                        <div
                            v-for="(file, index) in displayData"
                            :key="index"
                            :class="[
                                'file-container',
                                multiple ? 'pdf-name-box' : '',
                            ]"
                        >
                            <template v-if="file.isImage">
                                <img
                                    :src="file.url"
                                    :alt="'Image ' + index"
                                    class="img-block"
                                />
                            </template>
                            <template v-else>
                                <!-- Display PDFs with an iframe preview and a download/view link -->
                                <div class="pdf-container">
                                    <a
                                        :href="file.url"
                                        target="_blank"
                                        style="color: #000"
                                        class="file-name"
                                        >{{ file.name }}</a
                                    >

                                    <a
                                        :href="file.url"
                                        target="_blank"
                                        class="file-link"
                                    >
                                    </a>
                                </div>
                            </template>
                            <button
                                v-if="file.isImage"
                                type="button"
                                @click.prevent="removeFile(index, file)"
                                class="remove-image-button"
                            >
                                &times;
                            </button>
                            <button
                                v-else
                                type="button"
                                @click.prevent="removeFile(index, file)"
                                class="remove-image-button"
                            >
                                <svg
                                    data-v-357d04b8=""
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="#ACACAC"
                                    class="bi bi-trash text-white"
                                    viewBox="0 0 16 16"
                                    style="filter: invert(1)"
                                >
                                    <path
                                        data-v-357d04b8=""
                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"
                                    ></path>
                                    <path
                                        data-v-357d04b8=""
                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"
                                    ></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label
                        class="material-upload w-100 bg-yellow"
                        @dragover.prevent="onDragOver"
                        @dragleave.prevent="onDragLeave"
                        @drop.prevent="onDrop"
                        :class="{ 'drag-over': isDragOver }"
                    >
                        <p>
                            Drag and drop here<br />or
                            <span
                                style="
                                    text-decoration: underline;
                                    font-weight: 700;
                                "
                                >browse files</span
                            >
                        </p>
                        <input
                            type="file"
                            @change="onFilesChange"
                            :accept="acceptedFileTypes"
                            :multiple="multiple"
                            hidden
                            :disabled="disabled"
                        />
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    defineProps,
    defineEmits,
    onMounted,
    watch,
    onUnmounted,
} from "vue";
import axios from "axios";
import VueLoader from "../Common/Loader.vue";
const isLoading = ref(false);
const getFileUrl = (file) => file.media_url || file.url;
import { useToast } from "vue-toastification";
const toast = useToast();

const props = defineProps({
    attachments: Array,
    upload_url: {
        type: String,
        required: true,
    },
    subPath: {
        type: String,
        default: "projects/",
    },
    delete_url: {
        type: String,
        required: true,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: true,
    },
    acceptedFileTypes: {
        type: String,
        default:
            ".dwg, .pdf, .doc, .docx, image/png,image/jpg,, image/jpeg, image/gif",
    },
    title_view: {
        type: Boolean,
        default: false,
    },
});

const files = ref([]);
const displayData = ref([]);
const uploadedFileIds = ref([...props.attachments]);
const emit = defineEmits(["handleAttachmentUpload"]);

onMounted(() => {
    onMounted(() => {
        displayData.value = (props.attachments || []).map((file) => {
            const fileUrl = getFileUrl(file); // Extract the file URL here
            return {
                ...file,
                name: file.original_name,
                isBlob: false,
                isImage:
                    fileUrl.endsWith(".png") ||
                    fileUrl.endsWith(".jpeg") ||
                    fileUrl.endsWith(".jpg") ||
                    fileUrl.endsWith(".gif"),
                isPDF: fileUrl.endsWith(".pdf"),
            };
        });
    });
});

watch(
    () => props.attachments,
    (newAttachments) => {
        if (!newAttachments || newAttachments.length === 0) {
            uploadedFileIds.value = [];
            displayData.value = [];
            return;
        }
        uploadedFileIds.value = [...newAttachments];
        displayData.value = newAttachments.map((file) => {
            const fileUrl = getFileUrl(file);
            return {
                ...file,
                name: file.original_name,
                isBlob: false,
                isImage:
                    fileUrl.endsWith(".png") ||
                    fileUrl.endsWith(".jpeg") ||
                    fileUrl.endsWith(".jpg") ||
                    fileUrl.endsWith(".gif"),
                isPDF: fileUrl.endsWith(".pdf"),
            };
        });
    }
);
const MAX_FILE_SIZE = 20 * 1024 * 1024; // 20MB

const onFilesChange = (event) => {
    const selectedFiles = Array.from(event.target.files);

    const validFiles = selectedFiles.filter((file) => {
        if (file.size > MAX_FILE_SIZE) {
            toast.error(`File "${file.name}" is too large. Max size is 20MB.`, {
                timeout: 3000,
                position: "top-right",
            });
            return false;
        }
        return true;
    });

    // If no valid files remain, stop the function
    if (validFiles.length === 0) {
        return;
    }

    files.value = validFiles;

    const newDisplayData = validFiles.map((file) => ({
        name: file.name,
        url: URL.createObjectURL(file),
        isImage: file.type.startsWith("image"),
        isPDF: file.type === "application/pdf",
        isBlob: true,
    }));

    displayData.value = props.multiple
        ? [...displayData.value, ...newDisplayData]
        : newDisplayData;

    uploadFiles();
};

const uploadFiles = async () => {
    const formData = new FormData();
    formData.append("sub_path", props.subPath);
    files.value.forEach((file) => formData.append("attachments[]", file));

    try {
        isLoading.value = true;
        const response = await axios.post(props.upload_url, formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        const newAttachmentIds = response.data.attachments;

        if (props.multiple) {
            uploadedFileIds.value = [
                ...uploadedFileIds.value,
                ...newAttachmentIds,
            ];
        } else {
            uploadedFileIds.value = [...newAttachmentIds];
        }
        emit("handleAttachmentUpload", uploadedFileIds.value);
        isLoading.value = false;
    } catch (error) {
        isLoading.value = false;
        console.error("File upload failed:", error);
    }
};

const removeFile = async (index, file) => {
    if (!displayData.value.length) return;
    if (file.isBlob) {
        URL.revokeObjectURL(file.url);
        displayData.value.splice(index, 1);
        emit(
            "handleAttachmentUpload",
            displayData.value.map((f) => f.url)
        );
    } else {
        try {
            isLoading.value = true;
            if (props.title_view) {
                await axios.delete(`${props.delete_url}/${file.uuid}`);
            } else {
                await axios.post(props.delete_url, {
                    attachment_url: file.url,
                });
            }
            displayData.value.splice(index, 1);
            uploadedFileIds.value.splice(index, 1);
            emit("handleAttachmentUpload", uploadedFileIds.value);
            isLoading.value = false;
        } catch (error) {
            isLoading.value = false;
            console.error("File removal failed:", error);
        }
    }
};

onUnmounted(() => {
    displayData.value.forEach((file) => {
        const fileUrl = getFileUrl(file);
        if (file.isBlob) URL.revokeObjectURL(fileUrl);
    });
});
const isDragOver = ref(false);

const onDragOver = () => {
    isDragOver.value = true;
};

const onDragLeave = () => {
    isDragOver.value = false;
};

const onDrop = (event) => {
    isDragOver.value = false;

    const droppedFiles = Array.from(event.dataTransfer.files);

    const validFiles = droppedFiles.filter((file) => {
        if (file.size > MAX_FILE_SIZE) {
            toast.error(`File "${file.name}" is too large. Max size is 20MB.`, {
                timeout: 3000,
                position: "top-right",
            });
            return false;
        }
        return true;
    });

    if (validFiles.length === 0) return;

    files.value = validFiles;

    const newDisplayData = validFiles.map((file) => ({
        name: file.name,
        url: URL.createObjectURL(file),
        isImage: file.type.startsWith("image"),
        isPDF: file.type === "application/pdf",
        isBlob: true,
    }));

    displayData.value = props.multiple
        ? [...displayData.value, ...newDisplayData]
        : newDisplayData;

    uploadFiles();
};

</script>

<style scoped>
.remove-image-button {
    position: absolute;
    top: -10px;
    right: -10px;
    background-color: red;
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-bottom: 4px;
    font-size: 23px;
}

.file-container {
    position: relative;
    display: inline-flex;
    border-radius: 4px;
    width: 112px;
}

.upload__file-wrap-only-name img {
    object-fit: contain;
    border-radius: 6px;
}

/* -------------------multi upload image --------------------------------- */
.upload__box-only-name {
    /* padding: 40px; */
    width: 100%;
}

.upload__inputfile-only-name {
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
}

.upload__btn-only-name {
    width: 100% !important;
}

.upload__btn-box-only-name {
    margin-bottom: 10px;
}

.upload__file-wrap-only-name {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 20px;
    /* margin: 0 -10px; */
}

.upload__file-box-only-name {
    /* width: 200px; */
    padding: 4px 12px;
    margin-bottom: 12px;
    border: 1px solid #dde2e4;
    border-radius: 6px;
}

.upload__file-close-only-name {
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.5);
    position: absolute;
    top: 3px;
    right: 10px;
    text-align: center;
    line-height: 13px;
    z-index: 1;
    cursor: pointer;
}

.upload__file-close-only-name:after {
    content: "\2716";
    font-size: 11px;
    color: white;
}

.upload__file-wrap-only-name.specially-for-images div.img-bg {
    width: 110px;
    height: 96px;
    border-radius: 6px;
}

.img-bg-only-name {
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    position: relative;
    /* padding-bottom: 100%; */
}

.material-upload {
    height: 96px;
    width: 260px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    background: #d9d9d9;
    flex-direction: column;
    cursor: pointer;
}
.pdf-name-box {
    border: 1px solid #dde2e4;
    padding: 3px 11px;
    width: 100%;
}
.pdf-name-box button.remove-image-button {
    top: 6px;
    right: 8px;
    background: #fff !important;
    color: #000 !important;
    font-size: 33px;
}
.drag-over {
    border: 2px dashed #000;
    background-color: #fff9c4;
}

</style>
