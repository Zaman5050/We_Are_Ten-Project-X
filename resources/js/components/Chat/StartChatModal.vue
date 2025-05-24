<template>
    <div
        class="modal fade"
        id="start-chat-modal"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        tabindex="-1"
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
                <div class="modal-body">
                    <h2 class="create-project-title mb-4">Start New Chat</h2>
                    <div class="mb-3">
                        <label class="form-label">Chat Type</label>
                        <select v-model="chatType" class="form-select">
                            <option value="direct">Direct Chat</option>
                            <option value="project">Within Project Chat</option>
                        </select>
                    </div>
                    <div v-if="chatType === 'project'" class="mb-3">
                        <label class="form-label">Select Project</label>
                        <multiselect
                            class="nav-input project-selector"
                            v-model="selectedProject"
                            :options="filteredProjects"
                            placeholder="Select a project"
                            label="name"
                            track-by="uuid"
                            :multiple="false"
                        ></multiselect>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Add Participants</label>
                        <multiselect
                            class="nav-input project-selector"
                            v-model="selectedParticipants"
                            :options="filterParticipantsForModal"
                            placeholder="Select members"
                            label="full_name"
                            track-by="uuid"
                            :multiple="true"
                            :taggable="true"
                        ></multiselect>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="HandleStartChat"
                    >
                        Start Chat
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, computed, defineEmits,watch } from "vue";
import Multiselect from "vue-multiselect";
import axios from "axios";
import { v4 as uuidv4 } from "uuid";

const props = defineProps({
    members: {
        type: Array,
        default: () => [],
    },
    projects: {
        type: Array,
        default: () => [],
    },
    chats: {
        type: Array,
        default: () => [],
    },
});
const chatType = ref("direct");
const selectedProject = ref(null);
const selectedParticipants = ref([]);
const selectdChat = ref({});
const emit = defineEmits(["chatCreated"]);

const filterParticipantsForModal = computed(() => {
    if (chatType.value === "project") {
        if (!selectedProject.value) {
            return [];
        }
        return (selectedProject.value.members ?? []).filter(
            (member) => member.uuid !== currentAuth?.authUuid
        );
    }
    // For "direct" chat type, return all members excluding the current user
    return props.members.filter((member) => member.uuid !== currentAuth?.authUuid);
});
const filterProjectsForModal = computed(() => {
    if (chatType.value === "project") {
        if (!selectedProject.value) {
            return [];
        }
        return (selectedProject.value.members ?? []).filter(
            (member) => member.uuid !== currentAuth?.authUuid
        );
    }
    // For "direct" chat type, return all members excluding the current user
    return props.members.filter((member) => member.uuid !== currentAuth?.authUuid);
});


const HandleStartChat = async () => {
    const _uuidv4 = uuidv4();
    const chatObj = {
        id: 0,
        uuid: _uuidv4,
        project_id: selectedProject?.value?.id ?? 0,
        title: "New Chat",
        last_message: "",
        last_messaged_at: "",
        project_chat_members_count: 0,
        unseen_messages_count: 0,
        project_chat_members: [],
        chat_messages: [],
        // "project": {}
    };
    selectdChat.value = chatObj;
    emit(
        "participantClose",
        selectedParticipants.value,
        selectedProject.value,
        selectdChat.value
    );
    $("#start-chat-modal").modal("hide");
    resetModalState();
    return;

};
const resetModalState = () => {
    chatType.value = "direct";
    selectedParticipants.value = [];
    selectedProject.value = null;
};
watch(chatType, (newType, oldType) => {
    if (newType !== oldType) {
        selectedParticipants.value = []; // Clear participants when chat type changes
        selectedProject.value = null; // Reset the project selection if needed
    }
});
const filteredProjects = computed(() => {
    return props.projects.filter(project => project.name !== "Direct Chat");
});
</script>
