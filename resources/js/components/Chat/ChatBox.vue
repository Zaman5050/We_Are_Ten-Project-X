<template>
    <div class="chat-right">
        <div class="chat-right-heading px-3">
            <slot></slot>
        </div>
        <div class="messages-container">
            <div v-for="(message, index) in filteredMessages" :key="index"
                :class="'message-bubble message-' + (props.authUuid === message?.sender?.uuid ? 'send' : 'received') + '-container ' + (message.message_type == 'image' ? 'media-chat' : '')">
                <h6 class="message-title">{{ getSenderName(message?.sender) }}</h6>
                <div v-if="message.message_type == 'image' && message.message" class="message-media">
                    <figure :class="text-start">
                        <div class="text-end">
                            <img style="min-width:160px;border-radius: 6px;" :src="message.attachment" @click="previewImgModalHandle(message.attachment)" type="button" />
                        </div>
                        <figcaption class="message-text mt-2" v-html="formattedMessage(message.message)"></figcaption>
                    </figure>
                </div>
                <div v-else-if="message.message_type == 'image'" class="message-media">
                    <img style="width:160px" :src="message.attachment" @click="previewImgModalHandle(message.attachment)" type="button" />
                </div>
                <p v-else class="message-text" v-html="formattedMessage(message.message)"></p>
                <div class="message-time">{{ message.send_at }}</div>
            </div>
        </div>
        <div class="message-input-container">
            <div v-if="selectdChat?.uuid" :class="['position-relative ', { 'is-invalid': error }]">
                <textarea 
                spellcheck="false"
                autocorrect="off"
                autocapitalize="off"
                class="chat-input"
                :placeholder="chatBoxPlacehplder"
                v-model="chatInput" 
                ref="chatInputRef"
                @keydown.enter="HandleChatMessage"
                ></textarea>
                <img style="
                        position: absolute;
                        left: 18px;
                        top: 16px;
                        cursor: pointer;
                    " src="/assets/images/gif.svg" hidden />
                <label for="chatMediaFile">
                    <img style="position: absolute;
                        left: 18px;
                        top: 16px;
                        cursor: pointer" src="/assets/images/images-upload.svg" />
                </label>
                <input type="file" @change="previewImages" accept="image/png, image/jpeg" ref="chatMediaFile"
                    id="chatMediaFile" class="upload__inputfile-only-name" hidden>
                <img hidden style="
                        position: absolute;
                        left: 72px;
                        top: 16px;
                        cursor: pointer;
                    " src="/assets/images/emoji.svg" />

                <!-- Image Previews -->
                <div v-if="imagePreviews.length" class="image-previews">
                    <div v-for="(image, index) in imagePreviews" :key="index" class="image-preview">
                        <img :src="image" alt="Image preview" />
                        <button type="button" @click="removeImage(index)">&times;</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="chat-img-full-view" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="imageModalCloseHandle"></button>
                    </div>
                    <div class="modal-body pt-0" style="min-height: 500px;display: grid;place-items:center">
                        <img style="width:100%; max-height: 500px; object-fit: contain;" :src="setModalImg ?? 'assets/images/default-img.svg'"  />
                    </div>
                </div>
            </div>
        </div>

    </div>

   
</template>

<script setup>
import { defineProps, ref, watch, onMounted, computed, defineEmits, reactive } from "vue";
import axios from "axios";
import { v4 as uuidv4 } from "uuid";

const props = defineProps({
    authUuid: {
        type: String,
        default: currentAuth.authUuid
    },
    selectdChat: {
        type: Object,
        default: () => { }
    }
});

const getSenderName = (member) => {
    if(!member) return 'Sender';
    return member?.uuid  === currentAuth.authUuid ? 'You': member?.full_name;
}

const error = ref(false);
const images = ref([]);
const setModalImg = ref(null);
const imagePreviews = ref([]);
const replicatePreviewImages = ref([]);
const chatInputRef = ref();
const styleObject = ref({
    height: '100px',
});


const previewImgModalHandle = (img) => {
    setModalImg.value = img;
    var imgModal = new bootstrap.Modal(document.getElementById('chat-img-full-view'));
    imgModal && imgModal.show();
}

const imageModalCloseHandle = () =>{
    setModalImg.value = null;
}

const previewImages = (event) => {
    const files = Array.from(event.target.files);
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviews.value.push(e.target.result);
            images.value.push(file);
            // replicatePreviewImages.value.push({
            //     attachment: e.target.result,
            //     indexUuid: uuidv4()
            // });
        };
        reader.readAsDataURL(file);
    });

    chatInputRef && chatInputRef.value.focus();
 }


const removeDumpyMedia = (index) => {
    replicatePreviewImages.value.splice(index, 1);
}


const removeImage = (index) => {
    imagePreviews.value.splice(index, 1);
    images.value.splice(index, 1);
}

const chatBoxPlacehplder = ref('Start a new message');
const emit = defineEmits(['sendNewMessageToThreat', 'updateChatsMessages'])

const HandleChatMessage = async (event) => {
   

    const text = chatInput.value.trim();

    if (event.shiftKey) {
        // Shift + Enter, allow new line
        return;
    }

    error.value = false;

    if (!isAttachment.value) {
        if (!text) return false;
    }

    const chatUuid = props.selectdChat.uuid || null;

    const formData = new FormData();

    if (text) {
        formData.append('text', text);
    }

    if (isAttachment.value) {
        // Attach images
        images.value.forEach((image, index) => {
            formData.append(`attachments[${index}]`, image);
        });
    } 

    if (chatUuid && props.selectdChat?.project_chat_members?.length > 0) {
        chatInput.value = '';
        chatBoxPlacehplder.value = 'sending....';

        images.value = [];
        imagePreviews.value = [];

        const chatApiUrl = '/chats/' + chatUuid + '/sned-message';

        const options = {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: progressEvent => {
                // Show upload progress (can be connected to a loader or progress bar)
                console.log(`Upload Progress: ${(progressEvent.loaded / progressEvent.total) * 100}%`);
            }
        }

        // const presistChatMedia = imagePreviews.value;
        axios.post(chatApiUrl, formData, isAttachment.value ? options : {})
            .then(result => {
                // const currentThreat = $('#threat-' + chatUuid);
                if (result.data.status) {
                    const newMessage = result.data?.new_message || '';
                    emit('sendNewMessageToThreat', {
                        chatUuid: chatUuid,
                        chat: result.data?.chat,
                        message: newMessage
                    });
                }
                console.log(result);
            }).catch(error => {
                console.log(error);
                error.value = true;
                chatInput.value = text;
            }).finally(() => { })

        chatBoxPlacehplder.value = 'Start a new message';
    }

    event.preventDefault();
}

const isAttachment = computed(() => {
    return images.value.length > 0;
})
const chatInput = ref("");


const formattedMessage = (message) => {
    return message ? message.replace(/(?:\r\n|\r|\n)/g, '') : '';
}

watch(() => props.selectdChat, () => {
    fetchChatMessages();
});

onMounted(() => {
    fetchChatMessages();

});


const scrollToBottom = () => {
    const messagesContainer = document.querySelector('.messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight + 100;
    }
};

const fetchChatMessages = async () => {
    const chatUuid = props.selectdChat.uuid || null;
    if (props.selectdChat?.project_chat_members?.length < 1 || !chatUuid) return false;

    const last_messaged_at = props.selectdChat.last_messaged_at || new Date().toISOString();

    await axios.get('/chats/' + chatUuid + '/get-messages?last_date=' + last_messaged_at)
        .then(result => {

            console.log(result);

            const chat_messages = result.data.chat_messages || [];

            if (chat_messages.length > 0) {
                emit('updateChatsMessages', {
                    chatUuid: chatUuid,
                    message: chat_messages,
                    unseen_messages_count: result.data?.unseen_messages_count
                });
            }


        }).catch(error => {
            console.log(error);
        })
        .finally(() => {
            scrollToBottom();
        });

}


const filteredMessages = computed(() => {
    const messages = props.selectdChat.chat_messages || [];

    return messages;
})

watch(() => filteredMessages.value.length, () => {
    scrollToBottom();
});

</script>

<style scoped>
textarea.chat-input::placeholder{
    font-size: 16px;
}
textarea.chat-input {
    /* font-family: Arial, sans-serif; */
    font-size: 16px;
    line-height: 1.4;
    padding-top: 10px;
    resize: none;
    outline: none;
    padding-left: 42px;
}

textarea:focus {
  border-color: #66afe9;
}
.media-chat{
    background-color: transparent;
    border: 0;
}
.image-previews {
    position: relative;
    display: flex;
    align-items: flex-start;
}

.image-preview {
    position: relative;
    display: inline-flex;
    align-items: center;
    margin: 5px;
    width: 48px;
    height: 50px;
    border: 1px solid #eee;
    border-radius: 5.04px;
    background-color: #fff;
}

.image-preview img {
    max-width: 100%;
    border-radius: 5.04px;
    height: 44px;
    margin: 0 auto;
}

.image-preview button {
    position: absolute;
    top: -6px;
    right: -6px;
    text-align: center;
    line-height: 1.1;
    width: 19.48px;
    height: 19.48px;
    border-radius: 100%;
    font-size: medium;
    color: #fff;
    background-color: #000;
    border: 0;
    font-size: 20px;
    line-height: 0;
}
</style>
