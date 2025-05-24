<template>
    <div class="row-cols-1">
        <div
            :class="
                'dashboard-heading-container d-flex justify-content-' +
                (isProjectChat ? 'end' : 'between pb--40')
            "
        >
            <h1 v-if="!isProjectChat" class="dashboard-main-heading mb-0">
                Chats
            </h1>
            <button
                v-if="!isProjectChat"
                class="filter-btn mb-2 float-end bg-yellow"
                data-bs-toggle="modal"
                data-bs-target="#start-chat-modal"
                :disabled="checkThreatMessageLength"
            >
                Start new chat
            </button>
            <button
                v-if="isProjectChat"
                class="filter-btn mb-2 float-end bg-yellow"
                @click="HandleStartNewChat"
                :disabled="checkThreatMessageLength"
            >
                Start new chat
            </button>
        </div>
        <div class="" v-if="props.chats.length === 0">
            <div class="create-project-container">
                <div class="text-center">
                    <img
                        src="/assets/images/add-material-default.png"
                        style="width: 217px; height: 214px; object-fit: cover"
                    />
                    <h2 class="signup-headings" style="margin-bottom: 12px">
                        No Chats Yet.
                    </h2>
                    <span
                        class="create-to-start d-flex justify-content-center gap-1 fs-18"
                        >Start a conversation to see your messages here</span
                    >
                </div>
            </div>
        </div>
        <div class="chat-container" v-else>
            <ChatSidebar
                v-if="isProjectChat"
                :is-project-chat="isProjectChat"
                :selectd-chat="selectdChat"
                :chats="filteredChats"
                @chatClick="HandelCurrentSelectedChat"
            >
                <div class="d-flex align-items-center">
                    <input
                        style="
                            background-image: url('/assets/images/search-icon.svg');
                            max-width: 100%;
                        "
                        v-model="searchChat"
                        class="nav-input p-task-input me-0"
                        placeholder="Search"
                    />
                    <!-- <img src="/assets/images/filter-icon.svg" /> -->
                </div>
            </ChatSidebar>
            <GroupChatSidebar
                v-else
                :is-project-chat="isProjectChat"
                :selectd-chat="selectdChat"
                :projects="filteredProjects"
                :chats="filteredChats"
                :directChats="directChats"
                @chatClick="HandelCurrentSelectedChat"
                @clickThreat="HandelCurrentSelectedThreat"
            >
                <div class="d-flex align-items-center">
                    <input
                        style="
                            background-image: url('/assets/images/search-icon.svg');
                            max-width: 100%;
                        "
                        v-model="searchChat"
                        class="nav-input p-task-input me-0"
                        placeholder="Search"
                    />
                    <!-- <img src="/assets/images/filter-icon.svg" /> -->
                </div>
            </GroupChatSidebar>
            <ChatBox
                @sendNewMessageToThreat="handleCaptureNewMessage"
                @updateChatsMessages="handleFetchChatMessages"
                :selectd-chat="selectdChat"
            >
                <div
                    v-if="selectdChat?.uuid"
                    :class="
                        'chat-sec-multiselect d-flex gap-3 align-items-center justify-content-' +
                        (!selectdChatHaveMembers ? 'start' : 'between')
                    "
                >
                    <template v-if="!selectdChatHaveMembers">
                        <multiselect
                            v-if="!isProjectChat"
                            class="nav-input project-selector w-50"
                            v-model="selectdProject"
                            :options="props.projects"
                            placeholder="Select project"
                            label="name"
                            track-by="uuid"
                            :multiple="false"
                            @close="HandleSelectedProjectClose"
                            open-direction="bottom"
                            role="combobox"
                            selectLabel=""
                            deselectLabel=""
                        >
                        </multiselect>

                        <div
                            :class="
                                'multiselect-with-tags position-relative chat-to-member ' +
                                (isProjectChat ? 'w-75' : 'w-50')
                            "
                        >
                            <label
                                style="color: #757575"
                                for="To"
                                class="to-in-multiselect"
                            >
                                To: &emsp;</label
                            >
                            <multiselect
                                class="nav-input project-selector"
                                id="To"
                                v-model="state.selectedParticipant"
                                @close="HandleParticipantClose"
                                :close-on-select="false"
                                :options="filterParticipants"
                                placeholder=""
                                tag-placeholder="add as new tag"
                                label="full_name"
                                track-by="uuid"
                                :multiple="true"
                                :taggable="true"
                                open-direction="bottom"
                                :show-labels="false"
                            >
                                <!-- <template #selection="{ values, search, isOpen }">
                                    <span
                                        class="multiselect__single"
                                        v-if="values.length"
                                        v-show="!isOpen"
                                        >{{ values.length }} members selected</span
                                    >
                                </template> -->
                            </multiselect>
                        </div>
                    </template>
                    <template v-else-if="selectdChat?.uuid">
                        <h5 class="chat-heading">{{ selectdChat.title }}</h5>
                        <button
                            class="filter-btn h-32 float-end bg-yellow"
                            data-bs-toggle="modal"
                            data-bs-target="#add-participant"
                        >
                            Add Participant
                        </button>
                    </template>
                </div>
            </ChatBox>
        </div>

        <AddParticipantsPopup
            @updateParticipents="addParticipantsHandle"
            :chat="selectdChat"
            :members="filterParticipants"
            :selected-members="filteredSelectedMembers"
        ></AddParticipantsPopup>
        <StartChatModal
            :projects="projects"
            :members="members"
            :chats="chats"
            @participantClose="HandleParticipantNew"
            v-if="!isProjectChat"
        />
    </div>
</template>

<script setup>
import { ref, defineProps, computed, reactive, onMounted } from "vue";
import axios from "axios";
import ChatBox from "./ChatBox.vue";
import ChatSidebar from "./ChatSidebar.vue";
import GroupChatSidebar from "./GroupChatSidebar.vue";
import { v4 as uuidv4 } from "uuid";
import Multiselect from "vue-multiselect";
import AddParticipantsPopup from "./AddParticipants.vue";
import StartChatModal from "./StartChatModal.vue";
import { useToast } from "vue-toastification";

const selectdChat = ref({});
const selectdProject = ref();
const searchChat = ref("");
const toast = useToast();

const props = defineProps({
    type: {
        type: String,
        default: "project",
    },
    project: {
        type: Object,
        default: () => {},
    },
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
    direct: {
        type: Array,
        default: () => [],
    },
});

const isProjectChat = computed(() => {
    return props.type === "project";
});

const state = reactive({
    selectedParticipant: [],
    projectMembers: props.members,
    chats: props.chats,
    directChats: props.direct,
    projects: isProjectChat.value ? [props.project] : props.projects,
});

const filteredChats = computed(() => {
    let chats = state.chats;

    if (searchChat.value.length > 1) {
        chats = filterMessagesBySearch(chats, searchChat.value);
    }

    // if(selectdProject.value) return chats.filter(c => c?.project?.uuid === selectdProject?.value?.uuid).sort(sortedChats);

    chats = chats.sort(sortedChats);
    return chats;
});

const filteredProjects = computed(() => {
    let projects = state.projects.filter((p) => !!p);
    return projects;
});

const filterMessagesBySearch = (chats, searchText) => {
    const search = searchText.toLowerCase();

    return chats.filter((chat) => {
        const titleMatch = chat.title.toLowerCase().includes(search);
        const lastMessageMatch = chat?.last_message
            ?.toLowerCase()
            ?.includes(search);

        const memberMatch = chat.project_chat_members.some((m) =>
            m?.full_name?.toLowerCase().includes(search)
        );

        const messageMatch = chat.chat_messages.some(
            (c) =>
                c?.message?.toLowerCase()?.includes(search) ||
                c.sender?.full_name?.toLowerCase().includes(search)
        );

        // Return true if any of the conditions match
        return titleMatch || lastMessageMatch || memberMatch || messageMatch;
    });
};

const sortedChats = (a, b) => {
    const timeA = new Date(a.last_messaged_at).getTime();
    const timeB = new Date(b.last_messaged_at).getTime();
    return timeB - timeA; // Sort by latest messages first
};

const selectdChatHaveMembers = computed(() => {
    return selectdChat.value.project_chat_members_count > 0;
});

const handleFetchChatMessages = (data) => {
    const getChatIndex = state.chats.findIndex((c) => c.uuid == data.chatUuid);

    if (getChatIndex != -1) {
        const currentSelectdMessages = state.chats[getChatIndex].chat_messages;

        const newMessages = [
            ...new Set(currentSelectdMessages),
            ...new Set(data.message),
        ].filter(
            (obj, index, self) =>
                index === self.findIndex((t) => t.uuid === obj.uuid)
        );

        state.chats[getChatIndex].chat_messages = newMessages;
        state.chats[getChatIndex].unseen_messages_count =
            data?.unseen_messages_count;
    }
};

const handleCaptureNewMessage = (data) => {
    // console.log('handleCaptureNewMessage',data);
    const getChatIndex = state.chats.findIndex((c) => c.uuid == data.chatUuid);
    if (getChatIndex != -1) {
        // console.log(state.chats[getChatIndex])
        state.chats[getChatIndex].last_message = data?.chat?.last_message;
        state.chats[getChatIndex].last_messaged_at =
            new Date().toISOString() || data?.chat?.last_messaged_at;
    }
};

const filterParticipants = computed(() => {
    let members = state.projectMembers;
    if (selectdProject.value) {
        members = selectdProject.value?.members ?? [];
    }
    members = members.filter((member) => member.uuid != currentAuth?.authUuid);
    return members;
});

onMounted(() => {
    listinChatChannel();

    /*selectdProject.value = isProjectChat.value ? props.project :  (filteredProjects.value?.[0] ?? {});

    if (filteredChats.value.length > 0) {
        selectdChat.value = filteredChats.value[0] || {};
        state.selectedParticipant = isProjectChat.value
            ? selectdChat.value?.project_chat_members
            : selectdProject?.value?.teams;
    }*/
});

const filteredSelectedMembers = computed(() => {
    const members = selectdChat.value?.project_chat_members ?? [];
    return members?.filter((m) => m.uuid != currentAuth.authUuid);
});

const HandelCurrentSelectedThreat = (projectUuid) => {
    selectdProject.value = filteredProjects.value.find(
        (p) => p.uuid == projectUuid
    );
    const getChat =
        filteredChats.value.find((c) => c?.project?.uuid == projectUuid) ||
        filteredChats.value.find((c) => c?.uuid == projectUuid);

    if (getChat) {
        selectdChat.value = getChat;
        // get selected Participant
        state.selectedParticipant = selectdChat.value.project_chat_members;
    }
};

const HandelCurrentSelectedChat = (pyload) => {
    const { chatUuid, projectUuid } = pyload || {};

    if (isProjectChat.value) {
        selectdProject.value =
            filteredProjects.value.find((p) => p.uuid == projectUuid) ??
            filteredProjects.value[0];
    }

    const getChat = filteredChats.value.find((c) => c.uuid == chatUuid);
    if (getChat) {
        // update unseen_messages_count in state chats
        const getChatIndex = state.chats.findIndex((c) => c.uuid == chatUuid);
        const filteredChatSeenCount = getChat.unseen_messages_count;

        state.chats[getChatIndex].unseen_messages_count = 0;

        selectdChat.value = getChat;
        // get selected Participant
        state.selectedParticipant = selectdChat.value.project_chat_members;

        if (filteredChatSeenCount > 0) {
            markSeenMessages(getChatIndex, chatUuid, filteredChatSeenCount);
        }
    }
};

const markUnSeenMessages = async (chatUuid) => {
    return await axios.get("/chats/mark-unseen-messages/" + chatUuid);
};

const markSeenMessages = async (getChatIndex, chatUuid, seenCount = 0) => {
    const res = markUnSeenMessages(chatUuid)
        .then((res) => {
            console.info("chat seen: " + res.data.message);
        })
        .catch((err) => {
            // state.chats[getChatIndex].unseen_messages_count = seenCount;
            console.error("chat seen: " + err);
        })
        .finally(() => {
            state.chats[getChatIndex].unseen_messages_count = 0;
        });
    return await res;
};

const HandleSelectedProjectClose = (value, id) => {
    state.selectedParticipant = [];

    if (!isProjectChat.value) {
        state.selectedParticipant = selectdProject?.value?.teams ?? [];
        state.projectMembers = selectdProject.value?.members ?? [];
    }

    // console.log(selectdProject.value.members);
    // state.chats = [];
};
const HandleParticipantNew = (
    selectedParticipantsData,
    selectedProject,
    selectedChat
) => {
    if (!isProjectChat.value) {
        state.selectedParticipant = selectedParticipantsData;
        selectdProject.value = selectedProject;
        selectdChat.value = selectedChat;
        if (selectdProject.value == null) {
            if (state.selectedParticipant.length < 0) return;

            const selectedParticipantUuid = state.selectedParticipant.map(
                (x) => x.uuid
            );
            // Create the direct chat
            createDirectChat(selectedParticipantUuid)
                .then((res) => {
                    const result = res.data;
                    // Construct the chat object using API response
                    const chatObj = {
                        id: result.chat.id || 0,
                        uuid: result.chat.uuid || chatUuid, // Use API response UUID or fallback
                        project_id: 0, // Indicates no specific project
                        title: result.chat.title || "New Chat",
                        last_message: result.chat.last_message || "",
                        last_messaged_at: result.chat.last_messaged_at || "",
                        project_chat_members_count:
                            result.chat.project_chat_members_count || 0,
                        unseen_messages_count:
                            result.chat.unseen_messages_count || 0,
                        project_chat_members:
                            result.chat.project_chat_members || [],
                        chat_messages: result.chat.chat_messages || [],
                    };

                    // Construct the project object
                    const projectObj = {
                        id: 0,
                        uuid: result.chat.uuid,
                        threatuuid: result.chat.uuid || chatUuid, // Optional: Associate threatuuid
                        name: "Direct Chat",
                        chats: [chatObj], // Associate the chat with the project
                        members: result.chat.project_chat_members || [],
                    };

                    // Update state
                    selectdChat.value = chatObj;
                    selectdProject.value = projectObj;
                    state.chats.unshift(chatObj); // Add chat to the chats list
                    state.projects.unshift(projectObj);
                })
                .catch((err) => {
                    console.error(err);
                    // Reset selected chat if there's an error
                    selectdChat.value = {};
                });
        } else {
            HandleParticipantClose();
        }
    }
};
const HandleParticipantClose = (value, id) => {
    if (state.selectedParticipant.length < 0) return;

    const selectedParticipantUuid = state.selectedParticipant.map(
        (x) => x.uuid
    );
    // Check if a chat already exists with the same participants
    const existingChat = state.chats.find((chat) => {
        const chatParticipantsUuid = chat?.project_chat_members
            ? chat.project_chat_members.map((m) => m.uuid)
            : []; // Ensure we don't try to map on undefined

        // Ensure chat.project exists and matches the selected project
        if (!isProjectChat.value && selectdProject.value) {
            return (
                chat?.project?.uuid === selectdProject?.value.uuid &&
                selectedParticipantUuid.length ===
                    chatParticipantsUuid.length - 1 &&
                selectedParticipantUuid.every((uuid) =>
                    chatParticipantsUuid.includes(uuid)
                )
            );
        } else {
            return (
                selectedParticipantUuid.length ===
                    chatParticipantsUuid.length - 1 &&
                selectedParticipantUuid.every((uuid) =>
                    chatParticipantsUuid.includes(uuid)
                )
            );
        }
    });

    // If the chat exists, select it
    if (existingChat) {
        selectdChat.value = existingChat;

        // Find the index of the chat titled "New Chat" and delete it
        const indexOfNewChat = state.chats.findIndex(
            (chat) => chat.title === "New Chat"
        );
        if (indexOfNewChat !== -1) {
            state.chats.splice(indexOfNewChat, 1); // Remove the chat titled "New Chat"
        }
    } else if (
        selectedParticipantUuid.length > 0 &&
        selectdProject.value?.uuid &&
        selectdChat.value?.uuid
    ) {
        // If no chat exists, create a new group chat
        createGroupChat(selectedParticipantUuid)
            .then((res) => {
                console.log(res);
                const result = res.data;
                selectdChat.value = result.chat;

                // Add new chat to the chats list
                const replaceAbleThreatIndex = state.chats.findIndex(
                    (c) => c.uuid == result.threatIndex
                );
                if (replaceAbleThreatIndex != -1) {
                    state.chats[replaceAbleThreatIndex] = result.chat;
                } else {
                    state.chats.unshift(result.chat);
                }

                if (!isProjectChat.value) {
                    const findProjectIndex = filteredProjects.value.findIndex(
                        (p) => p?.uuid == result?.chat?.project?.uuid
                    );
                    if (findProjectIndex != -1) {
                        state.projects[findProjectIndex]?.chats.unshift(
                            result.chat
                        );
                    } else {
                        const findProjectThreat = props.projects.find(
                            (p) => p?.uuid == selectdChat.value?.project.uuid
                        );
                        if (findProjectThreat) {
                            findProjectThreat.chats.unshift(result?.chat);
                            state.projects.unshift(findProjectThreat);
                        }
                    }

                    // Remove "NA" projects after creating threat
                    const getValueableProjects = filteredProjects.value.filter(
                        (p) => p.name != "NA" && !p?.id
                    );
                    if (getValueableProjects.length > 0) {
                        state.projects = getValueableProjects;
                    }
                }
            })
            .catch((err) => {
                console.log(err);
                selectdChat.value = {};
            })
            .finally(() => {
                console.log("Create a group chat");
                state.selectedParticipant = [];
            });
    }
    // else {
    //     // No project selected, create a direct chat
    //     // const newDirectChat = {
    //     //     id: 0,
    //     //     uuid: uuidv4(),
    //     //     project_id: null,
    //     //     title: "Direct Chat",
    //     //     last_message: "",
    //     //     last_messaged_at: "",
    //     //     project_chat_members_count: selectedParticipantUuid.length,
    //     //     unseen_messages_count: 0,
    //     //     project_chat_members: state.selectedParticipant,
    //     //     chat_messages: [],
    //     // };
    //     // If no chat exists, create a new group chat
    //     createDirectChat(selectedParticipantUuid)
    //         .then((res) => {
    //             const result = res.data;
    //             selectdChat.value = result.chat;

    //             // Add the new chat to the chats list
    //             state.chats.unshift(result.chat);
    //         })
    //         .catch((err) => {
    //             console.error(err);
    //             selectdChat.value = {};
    //         });
    // }
};

const HandleStartNewChat = () => {
    if (
        isProjectChat.value &&
        (!filterParticipants.value || filterParticipants.value.length <= 0)
    ) {
        toast.error("Add Members to Start a New Chat", {
            timeout: 3000,
            position: "top-right",
        });
        return; // Exit the function if no members are selected
    }
    try {
        const _uuidv4 = uuidv4();
        const chatObj = {
            id: 0,
            uuid: _uuidv4,
            project_id: selectdProject.value?.id ?? 0,
            title: props.project?.title ?? "New Chat",
            last_message: "",
            last_messaged_at: "",
            project_chat_members_count: 0,
            unseen_messages_count: 0,
            project_chat_members: [],
            chat_messages: [],
            // "project": {}
        };
        selectdChat.value = chatObj;
        state.chats.unshift(selectdChat.value);

        if (!isProjectChat.value) {
            const projectObj = {
                id: 0,
                uuid: selectedProjectUuid.value,
                threatuuid: _uuidv4,
                name: "Start New Chat",
                chats: [chatObj],
                members: [],
            };
            selectdProject.value = projectObj;
            state.projects.unshift(projectObj);
        } else {
            selectdProject.value = props.project;
        }
    } catch (error) {
        console.log(error);
        selectdChat.value = {};
    }
    if (!isProjectChat.value) {
        selectdProject.value = {};
    }
    state.selectedParticipant = [];
};

const selectedProjectUuid = computed(() => {
    return selectdProject.value?.uuid ?? uuidv4();
});

const filteredProjectMembers = computed(() => {
    const project_uuid = selectdProject.value?.uuid;
    const filteredProjectsArr = filteredProjects.value.filter(
        (project) => project.uuid === project_uuid
    );

    return filteredProjectsArr.length ? filteredProjectsArr[0].members : [];
});

const startProjectChat = async () => {
    const projectUuid =
        (props?.project?.uuid ?? selectdProject.value?.uuid) || null;
    return axios.post("/chats/" + projectUuid + "/start-group-chat");
};

const addParticipantsHandle = async (payload) => {
    console.log(payload);
    const { chatUuid, participents } = payload;

    if (participents.length > 0 && chatUuid == selectdChat.value?.uuid) {
        const selectedParticipantUuid = participents.map((x) => x.uuid);

        addParticipants(chatUuid, selectedParticipantUuid)
            .then((res) => {
                console.log(res);
                selectdChat.value = res.data?.chat;

                let chatIndex = props.chats.findIndex(
                    (chat) => chat.uuid === selectdChat.value?.uuid
                );

                if (chatIndex !== -1) {
                    // update chat in chats list
                    state.chats[chatIndex] = selectdChat.value;
                }
                state.selectedParticipant =
                    selectdChat.value?.project_chat_members;
            })
            .catch((err) => {
                console.log(err);
            });
    }
};

const addParticipants = async (chatUuid, selectedParticipantUuid) => {
    return axios.post("/chats/" + chatUuid + "/add-participants", {
        participant_uuid: selectedParticipantUuid,
    });
};

const createGroupChat = async (selectedParticipantUuid) => {
    const projectUuid =
        (props?.project?.uuid ?? selectdProject.value?.uuid) || null;
    return axios.post("/chats/" + projectUuid + "/create-group-chat", {
        participant_uuid: selectedParticipantUuid,
        threatIndex: selectdChat.value?.uuid,
    });
};
const createDirectChat = async (selectedParticipantUuid) => {
    return axios.post("/chats/create-direct-chat", {
        participant_uuid: selectedParticipantUuid,
    });
};

const checkThreatMessageLength = computed(() => {
    if (!isProjectChat.value) return false;
    return state.chats.filter((chat) => chat.title === "New Chat").length > 0;
});

const listinChatChannel = () => {
    window.Echo.private("project-chat-channel." + currentAuth.authUuid)
        .listen(".project-private-chat-message", (e) => {
            // console.log("Private Message:", e);
            console.log("Private Message:", e.message);

            const newChat = e.newChat;
            const chatUuid = newChat?.uuid;

            var currentThreat = document.querySelector("#threat-" + chatUuid);
            if (currentThreat) {
                const getChatIndex = state.chats.findIndex(
                    (c) => c.uuid == chatUuid
                );

                if (currentThreat.classList.contains("chat-tab-active")) {
                    // Append message to the selected chat thread
                    appendMessage(e, getChatIndex);

                    scrollToBottom();

                    if (e.senderId != currentAuth.authUuid) {
                        markSeenMessages(getChatIndex, chatUuid);
                        const newMessageText =
                            $(currentThreat).find(".new-message-text"); // Use jQuery to find the element
                        if (newMessageText.length > 0) {
                            newMessageText.show(); // Show the element using jQuery
                            setTimeout(() => {
                                newMessageText.hide(); // Hide the element after 3 seconds
                            }, 3000);
                        }
                    }
                } else {
                    // Increment unread badge if the user is not on the selected chat
                    state.chats[getChatIndex].unseen_messages_count += 1;

                    // update Chat Threat
                    state.chats[getChatIndex].last_message = e.message.message;
                    state.chats[getChatIndex].last_messaged_at =
                        new Date().toISOString();
                }
            } else {
                if (isProjectChat.value) {
                    state.chats.unshift(newChat);
                    selectdChat.value = newChat;
                }
            }

            $(document).on("click", ".chat-tabs", function (e) {
                e.preventDefault();
                $(".temp-div").remove();
            });
        })
        .on("pusher:subscription_succeeded", () => {
            console.log(
                "Subscription to private-channel." +
                    currentAuth?.authUuid +
                    " succeeded"
            );
        })
        .on("pusher:subscription_error", (status) => {
            console.log("Subscription error:", status);
        })
        .on("leave", (s) => {
            console.log("Leave subscription", s);
        });
};

const appendMessage = (e, getChatIndex) => {
    if (getChatIndex != -1) {
        const currentSelectdMessages = state.chats[getChatIndex].chat_messages;
        currentSelectdMessages.push(e.message);
        state.chats[getChatIndex].chat_messages = currentSelectdMessages;
    }
};

const scrollToBottom = () => {
    const messagesContainer = document.querySelector(".messages-container");
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight + 100;
    }
};
const directChats = computed(() => {
    return props.direct.filter(
        (directChat) => directChat.title === "Direct Chat"
    ); // Filter chats where project_id is 0
});
</script>

<style scoped>
.chat-tabs {
    padding: 20px 16px;
}

.project-selector {
    margin-top: -15px;
    /* width: 75%; */
}

.multiselect__single {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    margin: 0;
    padding-left: 15px;
}

.h-32 {
    height: 32px;
}
.add-participant-select .list {
    width: 100%;
    height: 160px;
    overflow-y: auto;
}
#add-participant .modal-body {
    height: 258px;
}
</style>
