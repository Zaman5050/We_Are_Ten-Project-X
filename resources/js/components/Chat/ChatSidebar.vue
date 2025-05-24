<template>

    <div class="chat-left">
        <div class="chat-search-container">
            <slot></slot>
        </div>
        <div class="chat-tabs-container">
            <template v-for="(chat, index) in props.chats" :key="index">
                <div :class="['chat-tabs position-relative', { 'chat-tab-active': props.selectdChat?.uuid == chat.uuid }]"
                    :data-chat-uuid="chat.uuid" :id="'threat-' + chat.uuid"
                    @click.stop="$emit('chatClick', { chatUuid: chat.uuid, projectUuid: null })">
                    <h6 class="chat-member-name">{{ chat.title }}</h6>
                    <div class="row align-items-center">
                        <div class="col-4">
                            <MemberAvatarList :users="chat.project_chat_members"
                                :size="chat.project_chat_members?.length" :uindex="index" :you="true"></MemberAvatarList>
                        </div>
                        <div class="col-7 p-0">
                            <p class="chat-member-text" :title="getMemberNames(chat)">{{ getMemberNames(chat) }}</p>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-4 small fst-italic text-body-tertiary last_messaged_at">{{
                            formatAMPM(chat.last_messaged_at) }}</div>
                        <div class="col-7 p-0">
                            <img v-if="chat.last_message === 'isMedia'" src="/assets/images/images-upload.svg" style="filter:invert(1)" />
                            <p v-else class="chat-member-text last_message">{{ chat.last_message }}</p>
                        </div>
                    </div>
                    <span v-if="chat?.unseen_messages_count > 0"
                        class="position-absolute badge chat-badge rounded-pill text-bg-primary">{{
                            chat?.unseen_messages_count }}</span>
                </div>
            </template>
        </div>
    </div>

</template>

<script setup>
import {
    defineEmits,
    defineProps,
    computed

} from "vue";

import MemberAvatarList from "../MemberAvatarList.vue";

defineEmits(['chatClick']);
const props = defineProps({
    selectdChat: {
        type: Object,
        default: () => { }
    },
    chats: {
        type: Array,
        default: () => []
    }
});


const getMemberNames = (chat) => {
    const members = chat?.project_chat_members ?? [];
    const membersWithoutYou = members.filter(m => m.uuid !== currentAuth.authUuid);

    // If any member was removed, return "You and" with other member names
    return membersWithoutYou.length < members.length 
    ? `You and ${membersWithoutYou.map(x => x.full_name).join(', ')}`
    : members.map(x => x.full_name).join(', ');
}

const formatAMPM = (date) => {
    if (!date) return '';
    date = new Date(date);
    let hours = date.getHours();
    let minutes = date.getMinutes();
    let ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    let strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
}

// onMounted(() => {
//     console.log(props.selectdChat)
// })
</script>