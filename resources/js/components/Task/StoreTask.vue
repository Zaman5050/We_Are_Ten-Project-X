<template>
    <div class="container">
        <div class="modal-content">
            <div class="modal-header border-0 create-new-task-header pb-0">
                <h5 style="font-size: 14px" class="modal-title" id="task_code">
                    {{ state.task_code }}
                </h5>
                <button
                    @click="closeEvent()"
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body create-project-body">
                <div class="container">
                    <form
                        :method="!isEditAble ? 'POST' : 'PUT'"
                        @submit.prevent="handleSubmit()"
                        :action="props.action"
                        novalidate
                    >
                        <input
                            type="text"
                            id="task_title"
                            v-model="state.title"
                            :class="[
                                'create-project-title',
                                { 'is-invalid': checkValidation('title') },
                            ]"
                            placeholder="Type Task Title"
                            @keydown.enter.prevent
                        />
                        <ValidationError :errors="errors" field="title" />

                        <input
                            v-if="state.task_uuid != 'null'"
                            type="hidden"
                            id="task_uuid"
                            name="task_uuid"
                            v-model="state.task_uuid"
                        />
                        <input
                            type="hidden"
                            id="project_uuid"
                            name="project_uuid"
                            v-model="props.project.uuid"
                        />

                        <div class="row mt-4">
                            <div class="col-12 mb-4">
                                <div
                                    :class="[
                                        'sign-up-input-container h-auto mb-3 ',
                                        {
                                            'pb-4 position-relative':
                                                isEditAble,
                                        },
                                    ]"
                                >
                                    <label
                                        for="description"
                                        class="signup-labels"
                                        >TASK DESCRIPTION</label
                                    >
                                    <textarea
                                        v-model="state.description"
                                        style="height: 168px; resize: none"
                                        :class="[
                                            'signup-inputs py-2 mw-100',
                                            {
                                                'is-invalid':
                                                    checkValidation(
                                                        'description'
                                                    ),
                                            },
                                        ]"
                                        placeholder="Task Description"
                                        name="description"
                                        id="description"
                                    ></textarea>
                                    <ValidationError
                                        :errors="errors"
                                        field="description"
                                    />

                                    <div
                                        v-if="isEditAble"
                                        class="d-flex justify-content-between gap-3 small text-nowrap py-2 position-absolute timer-bar"
                                    >
                                        <span
                                            class="timer-btn position-relative"
                                            role="button"
                                        >
                                            <span
                                                @click="handelEstimateToggle"
                                                class="d-flex align-items-center"
                                            >
                                                <img
                                                    src="/assets/images/estimated-time.svg"
                                                    alt="estimate time"
                                                />
                                                &nbsp;Estimated Time
                                            </span>

                                            <span
                                                v-show="estimateToggle"
                                                class="position-absolute estimated-field"
                                            >
                                                <input
                                                    type="text"
                                                    id="estimate_time"
                                                    style="width: 78%"
                                                    :class="[
                                                        'p-1 border-1 rounded-1 border',
                                                        {
                                                            'is-invalid':
                                                                checkValidation(
                                                                    'estimate_time'
                                                                ),
                                                        },
                                                    ]"
                                                    v-model="
                                                        state.estimate_time
                                                    "
                                                    @blur="
                                                        handelEstimateValidation
                                                    "
                                                    placeholder="e.g: 1h 30m"
                                                    @keydown.enter.prevent
                                                />
                                                <ValidationError
                                                    :errors="errors"
                                                    field="estimate_time"
                                                    klass="position-absolute text-wrap"
                                                />
                                            </span>
                                        </span>

                                        <span
                                            class="timer-btn d-flex align-items-center position-relative"
                                            role="button"
                                            @click="handelTimeLoggedToggle"
                                        >
                                            <img
                                                class="invert-img"
                                                src="/assets/images/log-timer.svg"
                                                alt="log time"
                                            />
                                            &nbsp;Log Time

                                            <span
                                                v-show="tileLoggedToggle"
                                                class="position-absolute estimated-field"
                                            >
                                                <input
                                                    type="text"
                                                    id="total_time"
                                                    style="width: 78%"
                                                    class="p-1 border-1 rounded-1 border"
                                                    :value="
                                                        state.total_time_formated
                                                    "
                                                    disabled="disabled"
                                                    @keydown.enter.prevent
                                                />
                                            </span>
                                        </span>
                                        <span
                                            class="timer-btn d-flex align-items-center"
                                            role="button"
                                            @click="handelTimeLogged"
                                        >
                                            <img
                                                class="invert-img"
                                                :src="
                                                    timerToggled
                                                        ? '/assets/images/pause-timer.svg'
                                                        : '/assets/images/resume-timer.svg'
                                                "
                                                alt="toggle timer"
                                            />
                                            &nbsp;{{
                                                timerToggledStatus + " Time"
                                            }}
                                        </span>
                                    </div>
                                </div>
                                <div
                                    class="sign-up-input-container mb-4"
                                    style="height: auto"
                                >
                                    <file-uploader
                                        @handleAttachmentUpload="updateImageIds"
                                        :attachments="state.attachments"
                                        :sub-path="`projects/${props.project.uuid}/tasks`"
                                        :upload_url="'/task/upload'"
                                        :delete_url="'/task/attachment'"
                                        :acceptedFileTypes="'.dwg, .pdf, .doc, .docx, .csv, .txt, .zip, .xml, image/png, image/jpg, image/jpeg, image/gif'"
                                        :disabled="false"
                                        :title_view="true"
                                        :multiple="true"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label class="signup-labels">LABEL</label>
                                    <div
                                        class="create-new-project-select-container"
                                    >
                                        <multiselect
                                            v-model="state.stage"
                                            :options="props.stages"
                                            :searchable="false"
                                            :close-on-select="true"
                                            :show-labels="true"
                                            placeholder="select status"
                                            label="stage_name"
                                            track-by="uuid"
                                            :multiple="false"
                                            open-direction="bottom"
                                            :class="[
                                                {
                                                    'is-invalid':
                                                        checkValidation(
                                                            'stage_uuid'
                                                        ),
                                                },
                                            ]"
                                        ></multiselect>

                                        <ValidationError
                                            :errors="errors"
                                            field="stage_uuid"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="sign-up-input-container teamMemberWrap h-auto mb-2"
                                >
                                    <label class="signup-labels"
                                        >TEAM MEMBERS</label
                                    >
                                    <div
                                        class="create-new-project-select-container"
                                    >
                                        <multiselect
                                            v-model="state.member"
                                            :options="props.members"
                                            placeholder="Select one"
                                            label="full_name"
                                            track-by="full_name"
                                            :multiple="false"
                                            open-direction="bottom"
                                            @select="handleMemberSelected"
                                            @remove="handleMemberRemoved"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                                <!-- <a class="create-one mb-4 d-block" style="font-family:'inter-Regular';font-size: 14px;">Add
            Another Member</a> -->

                                <!-- <ul id="assignMemberList">
                                    <li
                                        v-for="(member, key) in state.member"
                                        :key="key"
                                    >
                                        <Avatar
                                            :name="member.short_name"
                                            :path="member.profile_picture"
                                        />
                                        {{ member.full_name }}
                                    </li>
                                </ul> -->
                            </div>
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label class="signup-labels"
                                        >START DATE</label
                                    >
                                    <flat-pickr
                                        v-model="state.start_date"
                                        :config="DateConfig"
                                        placeholder="DD/MM/YYYY"
                                        :class="[
                                            'signup-inputs flatpickr-input mw-100',
                                            {
                                                'is-invalid':
                                                    checkValidation(
                                                        'start_date'
                                                    ),
                                            },
                                        ]"
                                    />
                                    <ValidationError
                                        :errors="errors"
                                        field="start_date"
                                    />
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="sign-up-input-container">
                                    <label class="signup-labels"
                                        >DUE DATE</label
                                    >
                                    <flat-pickr
                                        v-model="state.due_date"
                                        :config="DateConfig"
                                        placeholder="DD/MM/YYYY"
                                        :class="[
                                            'signup-inputs flatpickr-input mw-100',
                                            {
                                                'is-invalid':
                                                    checkValidation(
                                                        'start_date'
                                                    ),
                                            },
                                        ]"
                                    />
                                    <ValidationError
                                        :errors="errors"
                                        field="due_date"
                                    />
                                </div>
                                <div class="sign-up-input-container">
                                    <label for="status" class="signup-labels"
                                        >STATUS</label
                                    >
                                    <div
                                        class="create-new-project-select-container"
                                    >
                                        <multiselect
                                            v-model="state.status"
                                            :options="props.taskStatuses"
                                            :searchable="false"
                                            :close-on-select="true"
                                            :show-labels="true"
                                            placeholder="select status"
                                            label="title"
                                            track-by="uuid"
                                            :multiple="false"
                                            open-direction="bottom"
                                            :class="[
                                                {
                                                    'is-invalid':
                                                        checkValidation(
                                                            'status_uuid'
                                                        ),
                                                },
                                            ]"
                                        ></multiselect>

                                        <ValidationError
                                            :errors="errors"
                                            field="status_uuid"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <button
                                    :disabled="loading"
                                    class="log-in submit-btn"
                                    style="max-width: 100%"
                                    type="submit"
                                >
                                    <IconSpinner
                                        v-show="loading"
                                    />&nbsp;&nbsp;Submit
                                </button>
                            </div>
                            <div class="col-md-6 mb-4">
                                <p
                                    v-show="errors.errorMessage"
                                    class="col-12 my-3 res-error invalid-feedback d-inline"
                                >
                                    {{ errors.errorMessage }}
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import eventBus from "../../eventBus";
import {
    reactive,
    ref,
    defineProps,
    onMounted,
    onUnmounted,
    watch,
    computed,
    defineEmits,
} from "vue";
import Multiselect from "vue-multiselect";
import axios from "axios";
import ValidationError from "../ValidationError.vue";
import IconSpinner from "../IconSpinner.vue";
import Avatar from "../Avatar.vue";
import FileUploader from "../Common/FileUploader.vue";
import { useTaskStore } from "../../stores/taskStore";
import { storeToRefs } from "pinia";
import Datepicker from "vue3-datepicker";
import flatPickr from "vue-flatpickr-component";
import { computeTimezoneDate } from "@/helpers.js";
import { useToast } from "vue-toastification";
import moment from "moment-timezone";
import { useUserStore } from "../../stores/userStore";
const userStore = useUserStore();

const toast = useToast();
const emit = defineEmits(["updateTask"]);
const taskStore = useTaskStore();
const { task, mode } = storeToRefs(taskStore);

const props = defineProps({
    action: "",
    project: {
        type: Object,
        default: {},
    },
    stages: {
        type: Array,
        required: true,
        default: [],
    },
    members: {
        type: Array,
        required: true,
        default: [],
    },

    taskStatuses: {
        type: Array,
        required: true,
        default: [],
    },
});

const estimateToggle = ref(false);
const tileLoggedToggle = ref(false);
const StageRef = ref(null);
const statusRef = ref(null);
const membersRef = ref(null);
const previousMember = ref(null);

const initializeNiceSelect = () => {
    $(StageRef.value).on("change", function () {
        state.stage_uuid = $(this).val();
    });

    $(statusRef.value).on("change", function () {
        state.status_uuid = $(this).val();
    });

    $(membersRef.value).on("change", function () {
        state.member = $(this).val();
        updateStateData();
    });
};

const handelEstimateToggle = () => {
    estimateToggle.value = !estimateToggle.value;
};

const handelTimeLoggedToggle = () => {
    tileLoggedToggle.value = !tileLoggedToggle.value;
};

const isTimeFormatValid = computed(() => {
    const formatPattern = /^(\d+w\s*)?(\d+d\s*)?(\d+h\s*)?(\d+m\s*)?$/;

    // Check if input matches the format
    if (!formatPattern.test(state.estimate_time.trim())) {
        return {
            isValid: false,
            message: 'Invalid format. Use "2w 3d 4h 30m".',
        };
    }

    // Define maximum values
    const maxValues = { w: 52, d: 30, h: 24, m: 60 };

    // Extract values using regex
    const matches = state.estimate_time.match(/(\d+)([wdhm])/g) || [];

    // Convert the matches into a key-value pair for easy validation
    const values = {};
    matches.forEach((match) => {
        const value = parseInt(match);
        const unit = match.replace(/\d+/, "");
        values[unit] = value;
    });

    // Validate the extracted values against the maximum limits
    for (const unit in maxValues) {
        if (values[unit] !== undefined && values[unit] > maxValues[unit]) {
            return {
                isValid: false,
                message: `Value for ${unit} exceeds maximum (${maxValues[unit]}) allowed.`,
            };
        }
    }

    // If all checks pass
    return { isValid: true, message: "Valid input." };
});

const handelEstimateValidation = async () => {
    if (!errors || !errors.value) {
        Object.assign(errors, { value: {} });
    }

    if (!isTimeFormatValid.value.isValid) {
        if (!errors.value["estimate_time"]) {
            errors.value["estimate_time"] = [];
        }
        errors.value["estimate_time"] = [];

        // Add the error message to the field's array
        errors.value["estimate_time"].push(isTimeFormatValid.value.message);
    } else {
        if (errors?.value["estimate_time"]) {
            delete errors?.value["estimate_time"];
        }
        await updateStateData();
    }
};
const timezone = computed(() => userStore.authUser?.timezone || "UTC");

const updateStateData = async () => {
    if (isEditAble.value) {
        try {
            const endTime = moment().tz(timezone.value);
            const response = await axios
                .post(`/task/update-task-data/${task.value?.task_uuid}`, {
                    estimate_time: state.estimate_time || null,
                    member: state.member || "",
                    previousMemberUuid: previousMember.value[0]?.uuid || "",
                    end_time: endTime,
                })
                .then((response) => {
                    if (response.data && !response.data.error) {
                        if (state.timer_mode === "active") {
                            state.timer_mode = "paused";
                        }
                        state.total_time = response.data.total_time;
                        state.total_time_formated =
                            convertMinutesToReadableTime(
                                state.total_time,
                                true
                            );
                        taskStore.updateTaskInList(
                            JSON.parse(JSON.stringify(state))
                        );
                        toast.success("Task updated successfully!", {
                            timeout: 3000,
                            position: "top-right",
                        });
                    }
                })
                .catch((error) => {
                    if (error.response && error.response.status === 403) {
                        // Handle forbidden case
                        toast.error(error.response.data.message, {
                            timeout: 3000,
                            position: " top-right",
                        });

                        // Revert to the previous member
                        state.member = previousMember.value;
                    } else {
                        console.error("Failed to update task", error);
                        toast.error("Failed to update task", {
                            timeout: 3000,
                            position: "top-right",
                        });
                    }
                });
        } catch (error) {
            console.error("Failed to update task", error);
            toast.error("Failed to update task", {
                timeout: 3000,
                position: "top-right",
            });
        }
    }
};

const handelTimeLogged = () => {
    // if(!state.member_eligible){
    //   alert("Action not allowed");
    //   return false;
    // }
    const members = Array.isArray(state.member) ? state.member : [state.member];

    // Check if the member list is empty
    if (members.length === 0 || !members[0].uuid) {
        toast.error("Please select a team member before starting the timer.", {
            timeout: 3000,
            position: "top-right",
        });
        return;
    }
    // Ensure the authenticated user is a team member
    const isAuthUserTeamMember = members.some(
        (member) => member.uuid === currentAuth.authUuid
    );
    if (!isAuthUserTeamMember) {
        toast.error(
            "Only the logged-in user as a team member in the task can start the timer.",
            {
                timeout: 5000,
                position: "top-right",
            }
        );
        return;
    }
    if (state.timer_mode === "idle") {
        if (
            state.estimate_time === "" ||
            state.estimate_time === null ||
            state.estimate_time === 0 ||
            state.estimate_time === "0"
        ) {
            toast.error(
                "Please add the estimated time before starting the timer.",
                {
                    timeout: 3000,
                    position: "top-right",
                }
            );
        } else {
            state.timer_mode = "active";
        }
    } else if (state.timer_mode === "paused") {
        if (
            state.estimate_time === "" ||
            state.estimate_time === 0 ||
            state.estimate_time === null ||
            state.estimate_time === "0"
        ) {
            toast.error(
                "Please add the estimated time before starting the timer.",
                {
                    timeout: 3000,
                    position: "top-right",
                }
            );
        } else {
            state.timer_mode = "active";
        }
    } else if (state.timer_mode === "active") {
        state.timer_mode = "paused";
    }

    eventBus.emit("timeLogEvent", {
        taskUuid: state.task_uuid,
        timer_mode: state.timer_mode,
        timer: state?.total_time ?? 0,
    });
};

onMounted(() => {
    eventBus.on("timeLogEvent", handleTimeLogEvent);
    eventBus.on("captureTimerEvent", handleCaptureTimer);
});

onUnmounted(() => {
    eventBus.off("timeLogEvent", handleTimeLogEvent);
    eventBus.off("captureTimerEvent", handleCaptureTimer);
});

const handleTimeLogEvent = (data) => {
    if (state.task_uuid === data.taskUuid) {
        state.timer_mode = data.timer_mode;
        if (+data.timer > 0) {
            state.total_time = data.timer;
            state.total_time_formated = convertMinutesToReadableTime(
                state.total_time,
                true
            );
        }
        console.log(state.timer_mode);
    }
};

const handleCaptureTimer = (data) => {
    if (+data.timer > 0) {
        state.total_time = data.timer;
        state.total_time_formated = convertMinutesToReadableTime(
            state.total_time,
            true
        );
    }
};

const timerToggledStatus = computed(() => {
    if (state.timer_mode === "idle") return "Start";
    if (state.timer_mode === "paused") return "Resume";
    if (state.timer_mode === "active") return "Pause";
    return "Start"; // Fallback, although it shouldn't be reached
});

const timerToggled = computed(() => {
    return state.timer_mode === "active";
});

function convertMinutesToReadableTime(minutes, skipMinutes = false) {
    // Ensure the input is a valid number and check for zero or negative minutes
    if (!minutes || minutes <= 0) {
        return "0s"; // Return '0s' for zero, invalid, or negative input
    }

    // Convert the minutes to total seconds (if skipMinutes is false)
    const totalSeconds = !skipMinutes
        ? Math.floor(minutes * 60)
        : Math.floor(minutes);

    // Calculate hours, minutes, and remaining seconds
    const hours = Math.floor(totalSeconds / 3600);
    const remainingMinutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    // Construct the human-readable format
    let readableTime = "";

    if (hours > 0) {
        readableTime += `${hours}h `;
    }
    if (remainingMinutes > 0) {
        readableTime += `${remainingMinutes}m `;
    }
    // Only add seconds if it's greater than zero, or if there's no other time component (hours/minutes)
    if (seconds > 0 || readableTime === "") {
        readableTime += `${seconds}s`;
    }

    return readableTime.trim(); // Remove any trailing spaces
}

const isEditAble = computed(() => {
    return mode.value == "edit";
});

watch(
    () => task.value,
    (newVal) => {
        console.log(newVal);

        if (mode.value == "create") resetForm();
        else {
            state.task_uuid = newVal?.task_uuid;
            state.created_by = newVal?.created_by;
            state.stage = newVal?.stage ?? {};
            state.stage_uuid = newVal?.stage_id;
            state.task_code = newVal?.task_code;
            state.title = newVal?.title;
            state.description = newVal?.description;
            state.flaged_by = newVal?.flaged_by;
            state.status = newVal?.status ?? {};
            state.status_id = newVal?.status ? newVal?.status?.id : null;
            state.project_uuid = newVal?.project_uuid;
            state.project_uuid = props.projectId;
            state.start_date = newVal?.start_date;
            state.due_date = newVal?.due_date;
            state.estimate_time = newVal?.estimate_time;
            state.order_number = newVal?.order_number;
            state.is_timer_paused = newVal?.is_timer_paused;
            state.attachments = newVal?.attachments ?? [];
            state.member = newVal?.members
                ? newVal.members
                : newVal.member
                ? newVal.member
                : [];
            // state.member = newVal?.members ?? [];
            state.timer_mode = newVal?.timer_mode;
            state.total_time = newVal?.total_time;
            const timerState = getTimerStateForTask(state.task_uuid);
            if (timerState?.totalTime >= (state.total_time || 0)) {
                state.total_time_formated = convertMinutesToReadableTime(
                    timerState?.totalTime,
                    true
                );
            } else {
                state.total_time_formated = newVal?.total_time_formated;
            }
        }
    },
    { deep: true }
);

onMounted(() => {
    initializeNiceSelect();
});

const closeEvent = () => {
    resetForm();
};

const errors = reactive({});
const loading = ref(false);

function resetForm() {
    state.title = "";
    state.description = "";
    state.start_date = "";
    state.due_date = "";
    state.stage = {};
    state.status = {};
    state.member = "";
    errors.value = {};
    state.attachments = [];
    state.member_eligible = false;
}

const state = reactive({
    task_uuid: "",
    created_by: "",
    stage: {},
    stage_uuid: "",
    task_code: "",
    title: "",
    description: "",
    flaged_by: "",
    status: {},
    project_uuid: props.project.uuid,
    start_date: "",
    due_date: "",
    estimate_time: "",
    order_number: "",
    is_timer_paused: "",
    attachments: [],
    member: "",
    timer_mode: "idle",
    total_time: 0,
});

const checkValidation = (field) => {
    return errors.value?.[field]?.length;
};

const updateImageIds = (ids) => {
    state.attachments = [...new Set(ids.filter((x) => !!x))];
};
const handleSubmit = async (event) => {
    if (loading.value) return;

    loading.value = true;
    errors.value = {};

    const data = { ...state };

    data.status_uuid = state?.status?.uuid;
    data.status = null;
    data.stage_uuid = state?.stage?.uuid;

    data.attachments = data.attachments
        ? data.attachments.map((x) => x.uuid)
        : [];

    reqCallMethod(props.action, data, mode.value)
        .then((response) => {
            if (response.data && !response.data.error) {
                window.location.reload();
            }
        })
        .catch((error) => {
            console.log(error);
            if (error.response.status === 422) {
                errors.value = error.response.data.errors;
            }
        })
        .finally(() => {
            // resetForm()
            loading.value = false;
        });
};

const reqCallMethod = async (url, data, mode) => {
    if (mode === "create") {
        return await axios.post(url, data);
    }
    url = url.replace("/store", "/update/" + task.value?.task_uuid);
    return await axios.put(url, data);
};
const DateConfig = computed(() => {
    return {
        dateFormat: "d/m/Y",
        minDate: computeTimezoneDate(props.project.start_date),
        maxDate: computeTimezoneDate(props.project.due_date),
    };
});

const getTimerStateForTask = (taskUuid) => {
    const timerStates = JSON.parse(localStorage.getItem("timer-states")) || {};
    return timerStates[taskUuid] || null;
};
const handleMemberSelected = () => {
    updateStateData();
};
const handleMemberRemoved = () => {
    updateStateData();
};
watch(
    () => state.member,
    (oldValue) => {
        if (oldValue) {
            previousMember.value = oldValue; // Store the previous member before change
        }
    },
    { deep: true }
);
</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style scoped>
.create-project-title {
    width: 100%;
    border: none;
}
textarea {
    height: 72px;
    resize: none;
}
.multiselect__tag {
    background: #252c32 !important;
}
button {
    max-width: 417.5px;
}

.timer-bar {
    bottom: -10px;
    right: 0;
}
.invert-img {
    -webkit-filter: invert(1);
    filter: invert(1);
}

.estimated-field {
    bottom: -30px;
    width: 125px;
}
</style>
