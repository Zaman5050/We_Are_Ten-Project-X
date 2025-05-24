<template>
                    <vue-loader v-if="loading" />

    <div
        class="modal fade"
        id="add-time-entry-popup"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        tabindex="-1"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body pt-0">
                    <h5 class="modal-title">Add Time Entry</h5>
                    <form @submit.prevent="handleSubmit()" id="addEntryForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label
                                        for="start_date"
                                        class="signup-labels"
                                        >START DATE</label
                                    >
                                    <flat-pickr
                                        v-model="state.start_date"
                                        id="start_date"
                                        placeholder="DD/MM/YYYY"
                                        :config="DateConfig"
                                        :class="[
                                            'signup-inputs flatpickr-input mw-100',
                                        ]"
                                    />
                                    <input-validation-error-message
                                        :v="v$?.start_date"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label for="end_date" class="signup-labels"
                                        >END DATE</label
                                    >
                                    <flat-pickr
                                        v-model="state.end_date"
                                        id="end_date"
                                        placeholder="DD/MM/YYYY"
                                        :config="DateConfig"
                                        :class="[
                                            'signup-inputs flatpickr-input mw-100',
                                        ]"
                                    />
                                    <input-validation-error-message
                                        :v="v$?.end_date"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label class="signup-labels">PROJECT</label>
                                    <div
                                        class="create-new-project-select-container"
                                    >
                                        <multiselect
                                            v-model="state.project"
                                            :options="props.memberProjects"
                                            placeholder="Select one"
                                            label="name"
                                            track-by="uuid"
                                            :multiple="false"
                                            open-direction="bottom"
                                        >
                                        </multiselect>
                                        <input-validation-error-message
                                            :v="v$?.project"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label class="signup-labels">TASK</label>
                                    <div
                                        class="create-new-project-select-container"
                                    >
                                        <multiselect
                                            v-model="state.task"
                                            :options="filteredTasks"
                                            placeholder="Select one"
                                            label="title"
                                            track-by="title"
                                            :multiple="false"
                                            open-direction="bottom"
                                        >
                                        </multiselect>
                                        <input-validation-error-message
                                            :v="v$?.task"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div
                                    class="sign-up-input-container only-time-picker"
                                >
                                    <label
                                        for="start_time"
                                        class="signup-labels"
                                        >START TIME</label
                                    >
                                    <flat-pickr
                                        v-model="state.start_time"
                                        id="start_time"
                                        :config="timePickerConfig"
                                        placeholder="Enter Start Time"
                                        @change="calculateTimeDifference"
                                        :class="['signup-inputs mw-100 ']"
                                    />
                                    <input-validation-error-message
                                        :v="v$?.start_time"
                                    />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div
                                    class="sign-up-input-container only-time-picker"
                                >
                                    <label for="end_time" class="signup-labels"
                                        >END TIME</label
                                    >
                                    <flat-pickr
                                        v-model="state.end_time"
                                        id="end_time"
                                        :config="timePickerConfig"
                                        @change="calculateTimeDifference"
                                        placeholder="Enter End Time"
                                        :class="['signup-inputs mw-100']"
                                    />
                                    <input-validation-error-message
                                        :v="v$?.end_time"
                                    />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="sign-up-input-container">
                                    <label
                                        for="hours_spent"
                                        class="signup-labels"
                                        >HOURS SPENT</label
                                    >
                                    <input
                                        readonly
                                        disabled
                                        v-model="state.hours_spent"
                                        id="hours_spent"
                                        class="signup-inputs mw-100"
                                        placeholder="Enter Hours Spent"
                                    />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="sign-up-input-container">
                                    <label
                                        for="minutes_spent"
                                        class="signup-labels"
                                        >MINUTES SPENT</label
                                    >
                                    <input
                                        readonly
                                        disabled
                                        v-model="state.minutes_spent"
                                        id="minutes_spent"
                                        class="signup-inputs mw-100"
                                        placeholder="Enter Minutes Spent"
                                    />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div
                    class="modal-footer border-0 pt-2 pb-4 justify-content-start flex-column align-items-start"
                >
                    <button
                        :disabled="loading"
                        class="log-in"
                        type="submit"
                        form="addEntryForm"
                    >
                        <IconSpinner v-show="loading" />&nbsp;&nbsp;Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, defineProps, watch, onMounted, reactive } from "vue";
import Multiselect from "vue-multiselect";
import flatPickr from "vue-flatpickr-component";
import IconSpinner from "../IconSpinner.vue";
import axios from "axios";
import ValidationError from "../ValidationError.vue";
import moment from "moment-timezone";
import { useUserStore } from "../../stores/userStore";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers } from "@vuelidate/validators";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import VueLoader from "../Common/Loader.vue";

const userStore = useUserStore();

const timezone = computed(() => userStore.authUser?.timezone || "UTC");

const props = defineProps({
    memberProjects: {
        type: Object,
        default: [],
    },
});

const loading = ref(false);

const timeDifference = ref(null);

const state = reactive({
    start_date: moment().tz(timezone.value).toDate(),
    end_date: moment().tz(timezone.value).toDate(),
    start_time: "09:00 AM",
    end_time: "",
    project: null,
    project_uuid: null,
    task: null,
    task_uuid: null,
});

function resetForm() {
    state.start_date = moment().tz(timezone.value).toDate();
    state.end_date = "";
    state.start_time = "";
    state.end_time = "";
    state.project = {};
    state.task_uuid = null;
}

const rules = computed(() => {
    const baseRules = {
        start_date: {
            required: helpers.withMessage("Start Date is required.", required),
            beforeDueDate: state.due_date
                ? helpers.withMessage(
                      "Start Date cannot be after the Due Date.",
                      (value) => {
                          const due_date = moment(state.due_date, "DD/MM/YYYY");
                          return (
                              !due_date.isValid() ||
                              moment(value, "DD/MM/YYYY").isSameOrBefore(
                                  due_date,
                                  "day"
                              )
                          );
                      }
                  )
                : {},
        },
        end_date: {
            required: helpers.withMessage("End Date is required.", required),
            afterStartDate: state.due_date
                ? helpers.withMessage(
                      "End Date cannot be before the Start Date.",
                      (value) => {
                          const start_date = moment(
                              state.start_date,
                              "DD/MM/YYYY"
                          );
                          return (
                              !start_date.isValid() ||
                              moment(value, "DD/MM/YYYY").isSameOrAfter(
                                  start_date,
                                  "day"
                              )
                          );
                      }
                  )
                : {},
        },
        project: {
            required: helpers.withMessage("Project is required.", required),
        },
        task: {
            required: helpers.withMessage("Task is required.", required),
        },
        start_time: {
            required: helpers.withMessage("tart Time is required.", required),
        },
        end_time: {
            required: helpers.withMessage("End Time is required.", required),
            validTimeSelection: helpers.withMessage(
                "Start Date and End Date are required when selecting Start Time and End Time.",
                () => {
                    if ((state.start_time || state.end_time) && (!state.start_date || !state.end_date)) {
                        return false;
                    }
                    return true;
                }
            ),
        },
    };

    return baseRules;
});
const v$ = useVuelidate(rules, state);

const handleSubmit = async () => {
    if (loading.value) return;
    v$.value.$touch();
    if (v$.value.$pending) return;
    if (v$.value.$invalid) {
        return false;
    }
    loading.value = true;

    const data = { ...state };

    data.start_date = moment(state.start_date, "DD/MM/YYYY").tz(timezone.value).format();
    data.end_date = moment(state.end_date, "DD/MM/YYYY").tz(timezone.value).format();

    data.project_uuid = state?.project?.uuid;
    data.project = null;

    data.task_uuid = state?.task?.uuid;
    data.task = null;

    await axios
        .post("/timesheet/add-time-entry", data)
        .then((response) => {
            console.log(response);

            if (response.data && !response.data.error) {
                window.location.reload();
            }
        })
        .catch((error) => {
            console.log(error);
            
            // if (error.response.status === 422) {
            //     errors.value = error.response.data.errors;
            // }
        })
        .finally(() => {
            // resetForm();
            loading.value = false;
        });
};

const DateConfig = {
    dateFormat: "d/m/Y",
};

// const timePickerConfig = {
//     enableTime: true,
//     noCalendar: true,
//     dateFormat: "H:i",
// };
const timePickerConfig = {
    enableTime: true,
    noCalendar: true,
    dateFormat: "h:i K", // 12-hour format with AM/PM
    time_24hr: false,
    minuteIncrement: 1, // Adjust increment for smoother selection
    defaultHour: 12, // Default time
    defaultMinute: 0,
    onReady(selectedDates, dateStr, instance) {
        // Remove the default AM/PM span element
        const amPmSpan =
            instance.calendarContainer.querySelector(".flatpickr-am-pm");
        if (amPmSpan) {
            amPmSpan.style.display = "none"; // Hides the default AM/PM span
        }

        addAmPmDropdown(instance);
        syncAmPmDropdown(instance);
    },
    onValueUpdate(selectedDates, dateStr, instance) {
        syncAmPmDropdown(instance);
    },
};

// Add an AM/PM dropdown to the Flatpickr instance
function addAmPmDropdown(instance) {
    const container = instance.calendarContainer;
    const timeContainer = container.querySelector(".flatpickr-time");
    if (!timeContainer) return;

    // Check if the dropdown already exists to avoid duplicates
    if (timeContainer.querySelector(".flatpickr-am-pm-dropdown")) return;

    // Create an AM/PM dropdown
    const amPmSelect = document.createElement("select");
    amPmSelect.classList.add("flatpickr-am-pm-dropdown");
    amPmSelect.innerHTML = `
        <option value="AM">AM</option>
        <option value="PM">PM</option>
    `;

    // Add a change event to sync with Flatpickr time
    amPmSelect.addEventListener("change", () => {
        const currentTime = instance.selectedDates[0] || new Date();
        const hour = currentTime.getHours();
        const newHour =
            amPmSelect.value === "AM" ? hour % 12 : (hour % 12) + 12;
        instance.setDate(new Date(currentTime.setHours(newHour)));
    });

    // Append the dropdown to the time container
    timeContainer.appendChild(amPmSelect);
}

// Sync the AM/PM dropdown with the selected time in Flatpickr
function syncAmPmDropdown(instance) {
    const amPmDropdown = instance.calendarContainer.querySelector(
        ".flatpickr-am-pm-dropdown"
    );

    if (amPmDropdown) {
        // Get the current hour and determine AM/PM
        const selectedDate = instance.selectedDates[0] || new Date();
        const hours = selectedDate.getHours();
        amPmDropdown.value = hours < 12 ? "AM" : "PM";
    }
}
const calculateTimeDifference = () => {
    if (state.start_time && state.end_time) {

        const start = moment(state.start_time, "hh:mm A");
        const end = moment(state.end_time, "hh:mm A");

        if (end.isBefore(start)) {
            end.add(1, "day");
        }

        const duration = moment.duration(end.diff(start));
        const hours = Math.floor(duration.asHours());
        const minutes = duration.minutes();

        timeDifference.value = { hours, minutes };
        state.hours_spent = `${hours} hours`;
        state.minutes_spent = `${minutes} minutes`;
    } else {
        timeDifference.value = null;
        state.hours_spent = "";
        state.minutes_spent = "";
    }
};
const filteredTasks = computed(() => {
    const project_uuid = state.project?.uuid;
    const filteredProjects = props.memberProjects.filter(
        (project) => project.uuid === project_uuid
    );

    return filteredProjects.length ? filteredProjects[0].tasks : [];
});

watch(state, () => calculateTimeDifference(), { deep: true });
const formatTime = (time) => {
    return moment(time, "HH:mm").format("hh:mm A"); // Convert to 12-hour format with AM/PM
};

watch(
    () => state.start_time,
    (newVal) => {
        if (newVal) {
            state.start_time = moment(newVal, "hh:mm A").format("hh:mm A");
        }
    }
);

watch(
    () => state.end_time,
    (newVal) => {
        if (newVal) {
            state.end_time = moment(newVal, "hh:mm A").format("hh:mm A");
        }
    }
);
</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style scoped>
.flatpickr-am-pm-dropdown {
    margin-left: 5px;
    padding: 5px;
    background-color: #fff;
    font-size: 0.9rem;
}

.multiselect__tag {
    background: #252c32 !important;
}
.flatpickr-am-pm {
    display: none !important;
}
</style>
