<template>
    <div>
        <vue-loader v-if="isLoading" />
        <div class="upload__box-only-name">
            <div v-if="!imageDisplayData" class="upload__btn-box-only-name">
                <slot></slot>

                <input
                    id="addAttachment"
                    type="file"
                    @change="onFilesChange"
                    accept="image/png, image/gif, image/jpeg"
                    class="upload__inputfile-only-name"
                    hidden
                />
            </div>

            <div v-if="imageDisplayData" class="upload__img-wrap-only-name">
                <div class="img-container">
                    <!-- Display blob image -->
                    <img
                        :src="imageDisplayData.url"
                        alt="Selected Image"
                        class="img-block"
                    />

                    <!-- Cross button to remove the image -->
                    <button
                        type="button"
                        @click.prevent="removeImage"
                        class="remove-image-button"
                    >
                        &times;
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    defineEmits,
    defineProps,
    watch,
    onMounted,
    onUnmounted,
} from "vue";
import axios from "axios";
import VueLoader from "../Common/Loader.vue";
import { useToast } from "vue-toastification";

const isLoading = ref(false);
const toast = useToast();

const props = defineProps({
    attachments: {
        type: Array,
        default: () => [],
    },
    subPath: {
        type: String,
        default: "projects/",
    },
});

const image = ref(null); // Holds the new file selected by the user
const imageDisplayData = ref(null); // Holds the display data (URL or name)
const uploadedImageId = ref(props.attachments[0]?.uuid || null); // Track already uploaded image ID
const emit = defineEmits(["imagesUploaded"]);

onMounted(() => {
    // Initialize imageDisplayData with the existing attachment's name if available
    if (props.attachments.length > 0) {
        const attachment = props.attachments[0];
        imageDisplayData.value = {
            url: attachment.media_url,
            uuid: attachment.uuid,
            isBlob: false,
        };
    }
});

onUnmounted(() => {
    imageDisplayData.value = null;
});

// Watch for changes in props.attachments and update imageDisplayData
watch(
    () => props.attachments,
    (newAttachments) => {
        if (newAttachments.length > 0) {
            const attachment = newAttachments[0];
            uploadedImageId.value = attachment.uuid;
            imageDisplayData.value = {
                url: attachment.media_url,
                uuid: attachment.uuid,
                isBlob: false,
            };
        } else {
            uploadedImageId.value = null;
            imageDisplayData.value = null;
        }
    }
);

const MAX_FILE_SIZE = 20 * 1024 * 1024; // 20MB

const onFilesChange = (event) => {
    const fileInput = event.target;
    const file = event.target.files[0];
    if (!file) return; // No file selected
    const MAX_FILE_SIZE = 20 * 1024 * 1024; // 20MB
    if (file.size > MAX_FILE_SIZE) {
        toast.error("You cannot upload an image larger than 20MB.", {
            timeout: 3000,
            position: "top-right",
        });
        fileInput.value = "";
        return;
    }
    image.value = file;

    // Create object URL for the new file and set as display data
    imageDisplayData.value = {
        url: URL.createObjectURL(file),
        uuid: "",
        isBlob: true,
    };

    // Automatically upload the file after selection
    uploadImage();
};

const uploadImage = async () => {
    if (!image.value) return;

    const formData = new FormData();
    formData.append("sub_path", props.subPath);
    formData.append("attachments[]", image.value);

    try {
        isLoading.value = true;

        const response = await axios.post("/projects/upload", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        console.log("Image uploaded successfully:", response.data.attachments);

        // Update the parent component with the new attachment ID
        const newAttachmentId = response.data.attachments; // Assuming the first attachment
        uploadedImageId.value = newAttachmentId;
        emit("imagesUploaded", newAttachmentId);

        // Update the display data with the uploaded image
        imageDisplayData.value = {
            url: response.data.attachments.media_url,
            uuid: response.data.attachments.uuid,
            isBlob: false,
        };
        isLoading.value = false;
    } catch (error) {
        console.error("Image upload failed:", error);
        isLoading.value = false;
    }
};

const removeImage = async () => {
    if (imageDisplayData.value.isBlob) {
        // If the image is a blob (not yet uploaded), just remove it from the state
        URL.revokeObjectURL(imageDisplayData.value.url); // Revoke the blob URL
        imageDisplayData.value = null;
        image.value = null;
    } else {
        // If the image is already uploaded, send a request to delete it from the server
        try {
            isLoading.value = true;

            await axios.delete(
                `/projects/attachment/${imageDisplayData.value.uuid}`
            );
            isLoading.value = false;
            imageDisplayData.value = null;
            uploadedImageId.value = null;
            emit("imagesUploaded", null);
        } catch (error) {
            console.error("Image removal failed:", error);
            isLoading.value = false;
        } finally {
            imageDisplayData.value = null;
            uploadedImageId.value = null;
            isLoading.value = false;
        }
    }
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

.img-container {
    position: relative;
    display: inline-flex;
    border-radius: 4px;
    width: 112px;
}

.upload__img-wrap-only-name img {
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

.upload__img-wrap-only-name {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 20px;
    /* margin: 0 -10px; */
}

.upload__img-box-only-name {
    /* width: 200px; */
    padding: 4px 12px;
    margin-bottom: 12px;
    border: 1px solid #dde2e4;
    border-radius: 6px;
}

.upload__img-close-only-name {
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

.upload__img-close-only-name:after {
    content: "\2716";
    font-size: 11px;
    color: white;
}

.upload__img-wrap-only-name.specially-for-images div.img-bg {
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
</style>
