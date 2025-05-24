<template>
    <div class="chat-left">
        <div class="chat-search-container">
            <slot></slot>
        </div>

        <!-- Project Chats Section -->
        <div class="chat-tabs-container">
            <div
                class="accordion"
                v-for="(project, key) in props.projects"
                :key="key"
                :id="'accordionChat-' + key"
                v-if="props.chats.length > 0"
            >
                <div class="accordion-item" v-if="project?.chats?.length > 0">
                    <h2
                        :class="['accordion-header ']"
                        @click="handleToggleCollapse(project.uuid)"
                    >
                        <button
                            :class="
                                'accordion-button  chat-member-name mb-0 chat-tabs ' +
                                (isCollapsed(project.uuid)
                                    ? 'collapsed'
                                    : 'active')
                            "
                            type="button"
                            data-bs-toggle="collapse"
                            :data-bs-target="'#panelsStayOpen-collapse' + key"
                            :aria-expanded="!isCollapsed(project.uuid)"
                            :aria-controls="'panelsStayOpen-collapse' + key"
                        >
                            {{ project.name }}
                            <span
                                v-if="getMessageCount(project) > 0"
                                class="position-absolute badge rounded-pill text-bg-primary"
                                >{{ getMessageCount(project) }}</span
                            >
                        </button>
                    </h2>
                    <div
                        :id="'panelsStayOpen-collapse' + key"
                        :class="[
                            'accordion-collapse collapse ',
                            { show: !isCollapsed(project.uuid) },
                        ]"
                        :aria-labelledby="'panelsStayOpen-collapse' + key"
                        :data-bs-parent="'accordionChat-' + key"
                    >
                        <div class="accordion-body chat-tabs p-0">
                            <template
                                v-for="(chat, index) in getProjectChats(
                                    project
                                )"
                                :key="key + '_' + index"
                            >
                                <!-- Existing project chat structure -->
                                <div
                                    :class="[
                                        'chat-tabs position-relative',
                                        {
                                            'chat-tab-active':
                                                props.selectdChat?.uuid ==
                                                chat.uuid,
                                        },
                                    ]"
                                    :data-chat-uuid="chat.uuid"
                                    :id="'threat-' + chat.uuid"
                                    @click.stop="
                                        $emit('chatClick', {
                                            chatUuid: chat.uuid,
                                            projectUuid: project.uuid,
                                        })
                                    "
                                >
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <MemberAvatarList
                                                :you="true"
                                                :users="
                                                    chat.project_chat_members
                                                "
                                                :size="
                                                    chat.project_chat_members
                                                        ?.length
                                                "
                                                :uindex="index"
                                            >
                                            </MemberAvatarList>
                                        </div>
                                        <div class="col-7 p-0">
                                            <p
                                                class="chat-member-text"
                                                :title="getMemberNames(chat)"
                                            >
                                                {{ getMemberNames(chat) }}
                                                <span v-if="isUserOnline(chat)" class="online-dot"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div
                                            class="col-4 small fst-italic text-body-tertiary last_messaged_at"
                                        >
                                            {{
                                                formatAMPM(
                                                    chat.last_messaged_at
                                                )
                                            }}
                                        </div>
                                        <div class="col-7 p-0">
                                            <img
                                                v-if="
                                                    chat.last_message ===
                                                    'isMedia'
                                                "
                                                src="/assets/images/images-upload.svg"
                                                style="filter: invert(1)"
                                            />
                                            <p
                                                v-else
                                                class="chat-member-text last_message"
                                            >
                                                {{ chat.last_message }}
                                            </p>
                                        </div>
                                    </div>
                                    <span
                                        v-if="chat?.unseen_messages_count > 0"
                                        class="position-absolute badge chat-badge rounded-pill text-bg-primary"
                                        >{{ chat?.unseen_messages_count }}</span
                                    >
                                    <span
                                        style="display: none"
                                        class="new-message-text position-absolute badge chat-badge rounded-pill text-bg-primary"
                                        >New message is received</span
                                    >
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineEmits, defineProps, ref, onMounted, computed } from "vue";

import MemberAvatarList from "../MemberAvatarList.vue";

const emit = defineEmits(["chatClick", "clickThreat"]);
const props = defineProps({
    isProjectChat: {
        type: Boolean,
        default: true,
    },
    selectdChat: {
        type: Object,
        default: () => {},
    },
    projects: {
        type: Array,
        default: () => [],
    },
    chats: {
        type: Array,
        default: () => [],
    },

    directChats: {
        type: Array,
        default: () => [],
    },
});

const getMessageCount = (project) => {
    return project?.chats.filter((c) => c.unseen_messages_count > 0).length;
};

// const getProjectChats = (project) => {

//     if(project.name.startsWith('NA')) return project.chats;

//     if (props.chats.length > 0) {
//         return props.chats.filter(c => c?.project?.uuid === project.uuid);
//     }
//     return project.chats;
// }
const getProjectChats = (project) => {
    // console.log("Direct Chats: ", props.chats);
    if (project.name === "Direct Chat") {
        const directChats = props.chats.filter(
            (c) => c?.uuid === project?.uuid
        );
        if (directChats.length > 0) {
            return directChats;
        }
        // Fallback to filtering chats with project_id === 0
        return props.chats.filter((c) => c.project_id === 0);
    }
    return props.chats.filter(
        (c) => c?.project?.uuid === project.uuid && c.project_id !== 0
    );
};

const getMemberNames = (chat) => {
    const members = chat?.project_chat_members ?? [];
    const membersWithoutYou = members.filter(
        (m) => m.uuid !== currentAuth.authUuid
    );

    // If any member was removed, return "You and" with other member names
    return membersWithoutYou.length < members.length
        ? `You and ${membersWithoutYou.map((x) => x.full_name).join(", ")}`
        : members.map((x) => x.full_name).join(", ");
};

const formatAMPM = (date) => {
    if (!date) return "";
    date = new Date(date);
    let hours = date.getHours();
    let minutes = date.getMinutes();
    let ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? "0" + minutes : minutes;
    let strTime = hours + ":" + minutes + " " + ampm;
    return strTime;
};

const gropuChatCollapsed = ref({});
const onlineUsers = ref([]);
onMounted(() => {
    const savedState = localStorage.getItem("gropuChatCollapsed");
    if (savedState) {
        gropuChatCollapsed.value = JSON.parse(savedState);
    }

    window.Echo.join("presence-chat")
    .here((users) => {
        console.log("Online users:", users); // helpful debug
        onlineUsers.value = users;
    })
    .joining((user) => {
        onlineUsers.value.push(user);
    })
    .leaving((user) => {
        onlineUsers.value = onlineUsers.value.filter(
            (u) => u.uuid !== user.uuid
        );
    });

});

const handleToggleCollapse = (projectUuid) => {
    gropuChatCollapsed.value[projectUuid] =
        !gropuChatCollapsed.value[projectUuid];
    localStorage.setItem(
        "gropuChatCollapsed",
        JSON.stringify(gropuChatCollapsed.value)
    );

    if (!gropuChatCollapsed.value[projectUuid]) {
        emit("clickThreat", projectUuid);
    }
};
const isCollapsed = (id) => {
    return gropuChatCollapsed.value[id] || false;
};
const isUserOnline = (chat) => {
    const otherMembers = chat.project_chat_members.filter(m => m.uuid !== currentAuth.authUuid);
    return otherMembers.some(m =>
        onlineUsers.value.find(u => u.uuid === m.uuid)
    );
};

</script>

<style scoped>
.chat-tabs > .badge {
    right: 60px;
}
.chat-tabs.active > .badge {
    display: none;
    opacity: 0;
}
.accordion-button:focus {
    box-shadow: none;
    border: none;
}
.accordion-button:not(.collapsed) {
    background-color: transparent;
    box-shadow: none;
}
.accordion-item,
.accordion-button {
    border-radius: 0 !important;
    border: none;
}
.accordion-item {
    border-bottom: 1px solid #e5e9eb !important;
}
.chat-tabs {
    border: none;
}
.online-dot {
    width: 8px;
    height: 8px;
    background-color: #28a745;
    border-radius: 50%;
    display: inline-block;
    margin-left: 5px;
}

</style>
