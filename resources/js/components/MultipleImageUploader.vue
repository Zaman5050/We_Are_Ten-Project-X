<template>
    <div>
        <div class="upload__box-only-name">
            <div class="upload__btn-box-only-name">
                <label for="addAttachment">
                    <img src="/assets/images/add-img-icon.svg"> &nbsp;
                    <u>Add Attachment</u>
                </label>

                <input id="addAttachment" type="file" @change="onFilesChange"
                    accept="image/png, image/gif, image/jpeg" multiple data-max_length="20"
                    class="upload__inputfile-only-name" hidden>
            </div>

            <div v-if="imageDisplayData.length" class="upload__img-wrap-only-name">
                <div v-for="(image, index) in imageDisplayData" :key="index" class="img-container">
                    <!-- Display blob images -->
                    <img :src="image.url" :alt="'Selected Image '+ index" class="img-block" />
                    
                    <!-- Cross button to remove the image -->
                    <button type="button" @click.prevent="removeImage(index, image)" class="remove-image-button">&times;</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineEmits, defineProps, watch, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    attachments: {
        type: Array,
        default: () => [],
    },
    subPath: {
        type: String,
        default: 'projects/'
    }
});

const images = ref([])             // Holds the new files selected by the user
const imageDisplayData = ref([])   // Holds the display data (URLs or names)
const uploadedImageIds = ref([...props.attachments]) // Track already uploaded image IDs
const emit = defineEmits(['imagesUploaded'])

onMounted(() => {
    // Initialize imageDisplayData with existing attachments' names
    imageDisplayData.value = props.attachments.map(attachment => ({
        url: attachment.media_url,
        uuid: attachment.uuid,
        isBlob: false,
    }));
});

onUnmounted(() => {
    imageDisplayData.value = []
});

// Watch for changes in props.attachments and update imageDisplayData
watch(() => props.attachments, (newAttachments) => {
    uploadedImageIds.value = [...newAttachments];
    imageDisplayData.value = newAttachments.map(attachment => ({
        url: attachment.media_url,
        uuid: attachment.uuid,
        isBlob: false,
    }));
});

const onFilesChange = (event) => {
    const files = Array.from(event.target.files)
    images.value = files

    // Create object URLs for new files and add to display data
    const newImageDisplayData = files.map(file => ({
        url: URL.createObjectURL(file),
        uuid: '',
        isBlob: true,
    }));

    // Merge existing and new images for display
    imageDisplayData.value = [
        ...imageDisplayData.value.filter(img => !img.isBlob), // Keep only existing ones
        ...newImageDisplayData
    ];

    // Automatically upload files after selection
    uploadImages();
}

const uploadImages = async () => {
    if (!images.value.length) return

    const formData = new FormData()
    formData.append('sub_path', props.subPath)
    images.value.forEach(image => formData.append('attachments[]', image))

    try {
        const response = await axios.post('/task/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })

        console.log('Images uploaded successfully:', response.data)

        // Update the parent component with new attachment IDs
        const newAttachmentIds = response.data.attachments
        uploadedImageIds.value = [...uploadedImageIds.value, ...newAttachmentIds]
        emit('imagesUploaded', uploadedImageIds.value)
    } catch (error) {
        console.error('Image upload failed:', error)
    }
}

const removeImage = async (index, image) => {
    console.log(image)
    // If the image is a blob (not yet uploaded), just remove it from the state
    if (image.isBlob) {
        URL.revokeObjectURL(image.url); // Revoke the blob URL
        imageDisplayData.value.splice(index, 1);
    } else {
        // If the image is already uploaded, send a request to delete it from the server
        try {
            await axios.delete(`/task/attachment/${image.uuid}`);
            imageDisplayData.value.splice(index, 1);
            uploadedImageIds.value.splice(index, 1);
            emit('imagesUploaded', uploadedImageIds.value);
        } catch (error) {
            console.error('Image removal failed:', error);
        }
    }
}

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
    gap: 20px
        /* margin: 0 -10px; */
}

.upload__img-box-only-name {
    /* width: 200px; */
    padding: 4px 12px;
    margin-bottom: 12px;
    border: 1px solid #DDE2E4;
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
    content: '\2716';
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
    background: #D9D9D9;
    flex-direction: column;
    cursor: pointer;
}
</style>