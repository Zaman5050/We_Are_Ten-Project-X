<template>
    <div>
        <vue-loader v-if="isLoading" />

        <div class="d-flex justify-content-between">
            <h5 class="modal-title" style="text-transform: capitalize">
                {{
                    [
                        "approved",
                        "declined",
                        "negotiated",
                        "re-negotiated",
                    ].includes(leaveStatus)
                        ? mapleaveStatus[leaveStatus]
                        : "Apply for Leaves"
                }}
            </h5>
            <div
                class="mb-2 float-end text-decoration-underline"
                style="margin-top: 22px; cursor: pointer"
                @click="showMember"
                v-if="isAuthUserAdmin"
            >
                {{ memberName }}
            </div>
        </div>

        <form @submit.prevent="handleSubmit">
            <h5 class="what-type-leave">
                {{
                    leaveStatus === "negotiated"
                        ? "Alternate Dates Suggested by your manager"
                        : leaveStatus === "re-negotiated"
                        ? "Suggest alternate Dates To your Manager"
                        : "  What type of leave are you applying for?"
                }}
            </h5>
            <div
                class="row mb-4"
                v-if="
                    leaveStatus !== 'negotiated' &&
                    leaveStatus !== 're-negotiated'
                "
            >
                <div class="col-md-3">
                    <div class="sign-up-input-container h-auto">
                        <label
                            style="font-size: 16px; display: block !important"
                            class="check-container signup-labels"
                        >
                            SICK LEAVE
                            <input
                                type="radio"
                                name="leave_type"
                                value="sick"
                                v-model="state.leave_type"
                                :disabled="isEditable"
                            />
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="sign-up-input-container h-auto">
                        <label
                            style="font-size: 16px; display: block !important"
                            class="check-container signup-labels"
                        >
                            CASUAL LEAVE
                            <input
                                type="radio"
                                name="leave_type"
                                value="casual"
                                v-model="state.leave_type"
                                :disabled="isEditable"
                            />
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="sign-up-input-container h-auto">
                        <label
                            style="font-size: 16px; display: block !important"
                            class="check-container signup-labels"
                        >
                            ANNUAL LEAVE
                            <input
                                type="radio"
                                name="leave_type"
                                value="annual"
                                v-model="state.leave_type"
                                :disabled="isEditable"
                            />
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                <input-validation-error-message :v="v$.leave_type" />
            </div>
            <h5
                class="what-type-leave"
                v-if="
                    leaveStatus !== 'negotiated' &&
                    leaveStatus !== 're-negotiated'
                "
            >
                Select the dates for your leave
            </h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="sign-up-input-container">
                        <label class="signup-labels">START DATE</label>
                        <flatpickr
                            v-model="state.start_date"
                            :config="StartDateConfig"
                            placeholder="DD/MM/YYYY"
                            :class="['signup-inputs flatpickr-input mw-100 ']"
                            :disabled="isEditable"
                        />
                        <input-validation-error-message :v="v$.start_date" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sign-up-input-container">
                        <label class="signup-labels">END DATE</label>
                        <flatpickr
                            v-model="state.end_date"
                            :config="DueDateConfig"
                            placeholder="DD/MM/YYYY"
                            :class="['signup-inputs flatpickr-input mw-100 ']"
                            :disabled="isEditable"
                        />
                        <input-validation-error-message :v="v$.end_date" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="sign-up-input-container">
                        <label class="signup-labels">NUMBER OF DAYS</label>
                        <input
                            id="number_of_days"
                            type="text"
                            :class="['signup-inputs ']"
                            disabled
                            :value="numberOfDays"
                        />
                       
                    </div>
                    <input-validation-error-message
                            :v="v$.number_of_days"
                            style="position: relative; z-index: 10"
                        />
                </div>
            </div>
            <div>
                <label class="signup-labels">NOTE</label>
                <textarea
                    name="notes"
                    style="height: 168px; resize: none"
                    :class="['signup-inputs py-2 mw-100 ']"
                    placeholder="Type Text Here"
                    v-model="state.notes"
                    :readonly="isEditable"
                ></textarea>
                <input-validation-error-message :v="v$.notes" />
            </div>

            <div
                class="modal-footer border-0 flex-nowrap pb-0"
                v-if="
                    leaveStatus === 'negotiated' ||
                    leaveStatus === 're-negotiated'
                "
            >
                <button
                    v-if="leaveStatus === 'negotiated'"
                    class="apply-leave-btn btn-close"
                    style="
                        width: 50%;
                        background: rgb(255, 255, 255);
                        color: rgb(0, 0, 0);
                    "
                    type="button"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                >
                    Cancel
                </button>
                <button
                    v-if="leaveStatus === 'negotiated'"
                    type="submit"
                    class="apply-leave-btn"
                    style="width: 50%"
                    :disabled="isChanged"
                >
                    Negotiate
                </button>
                <button
                    v-if="leaveStatus === 're-negotiated'"
                    type="submit"
                    class="apply-leave-btn"
                    style="
                        width: 50%;
                        background: rgb(255, 255, 255);
                        color: rgb(0, 0, 0);
                    "
                >
                    Renegotiate
                </button>
                <button
                    v-if="leaveStatus === 're-negotiated'"
                    type="button"
                    class="apply-leave-btn"
                    style="width: 50%"
                    :disabled="isChanged"
                    @click="handleAccept"
                >
                    Approve
                </button>
            </div>
            <div
                class="modal-footer border-0 pe-0 pt-0 flex-nowrap pb-0"
                v-if="
                    leaveStatus !== 'negotiated' &&
                    leaveStatus !== 're-negotiated' &&
                    state.status !== 'approved' &&
                    state.status !== 'declined'
                "
            >
                <button
                    style="width: 122px; text-transform: capitalize"
                    type="submit"
                    class="apply-leave-btn me-0 rounded-0"
                    :disabled="isLoading"
                >
                    {{
                        [
                            "approved",
                            "declined",
                            "negotiated",
                            "re-negotiated",
                        ].includes(leaveStatus)
                            ? mapleaveStatus[leaveStatus]
                            : "Apply"
                    }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import flatpickr from "vue-flatpickr-component";
import { reactive, ref, computed, watch, onMounted } from "vue";
import axios from "axios";
import { storeToRefs } from "pinia";
import { useLeavStore } from "../../stores/leaveStore";
import { useUserStore } from "../../stores/userStore";
import VueLoader from "../Common/Loader.vue";
import { useToast } from "vue-toastification";
import { v4 as uuidv4 } from "uuid";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers, maxLength, email } from "@vuelidate/validators";

const leaveStore = useLeavStore();
const userStore = useUserStore();
const { leave, leaveStatus, member } = storeToRefs(leaveStore);
const { authUser } = storeToRefs(userStore);
const isAuthUserAdmin = ref(false);

const toast = useToast();

const errors = ref(false);
const props = defineProps({
    authuserid: {
        type: Number,
        default: "",
    },
    authuserleaves: {
        type: Object,
        default: [],
    },
});

const state = reactive({
    leave_type: "",
    start_date: moment().format("DD/MM/YYYY"),
    end_date: "",
    notes: "",
    status: "",
});

const isLoading = ref(false);

const mapleaveStatus = {
    approved: "Approve",
    declined: "Deny",
    negotiated: "Negotiate",
    "re-negotiated": "Renegotiate",
};
import moment from "moment";

const numberOfDays = computed(() => {
    if (state.start_date && state.end_date) {
        // Parse the start and end dates using moment
        const startDate = moment(state.start_date, "DD/MM/YYYY");
        const endDate = moment(state.end_date, "DD/MM/YYYY");

        // Ensure the dates are valid
        if (startDate.isValid() && endDate.isValid()) {
            // Calculate the difference in days
            const daysDiff = endDate.diff(startDate, "days") + 1; // Add 1 to include the end date
            return daysDiff;
        }
    }
    return 0; // If no valid start_date and end_date, return 0
});
const remainingLeavesUser = computed(() => {
    return member.value?.remaining_leaves
        ? member.value.remaining_leaves
        : props.authuserleaves;
});

const isEditable = computed(() =>
    ["approved", "declined"].includes(leaveStatus.value)
);
const formateDate = (date) => {
    return date.split("/").reverse().join("/");
};
const serverSideErrors = reactive({});

const rules = computed(() => ({
    leave_type: {
        required: helpers.withMessage("Leave Type is required.", required),
    },
    start_date: {
        required: helpers.withMessage("Start Date is required.", required),
    },
    end_date: {
        required: helpers.withMessage("End Date is required.", required),
    },
    notes: {
        required: helpers.withMessage("Notes are required.", required),
    },
    number_of_days: {
        isWithinRemainingLeaves: helpers.withMessage(
            "The number of days exceeds your remaining leaves.",
            (value) => {
                if (!state.leave_type || !state.start_date || !state.end_date) {
                    return true;
                }
                value = numberOfDays.value;
                const selectedLeave =
                    remainingLeavesUser.value[state.leave_type];
                return selectedLeave !== undefined && value <= selectedLeave;
            }
        ),
    },
}));

const v$ = useVuelidate(rules, state, {
    $externalResults: serverSideErrors,
});
const syncErrorsWithVuelidate = (errors) => {
    // Map backend errors to the structure of Vuelidate's `$externalResults`
    let externalResults = {
        areas: state.areas.map(() => ({
            area_name: [],
            area_dimention: [],
            area_code: [],
        })),
    };
    Object.keys(errors).forEach((key) => {
        const [prefix, index, field] = key.split(".");
        if (prefix === "areas" && !isNaN(index)) {
            externalResults.areas[index][field] = errors[key];
        }
    });
    // Assign external errors to the Vuelidate instance
    Object.assign(serverSideErrors, externalResults);
};

const handleSubmit = async (event = null) => {
    event.preventDefault();
    const isNegotiated = ["negotiated", "re-negotiated"].includes(
        leaveStatus.value
    );
    if (!isEditable.value || isNegotiated) {
        v$.value.$clearExternalResults();
        v$.value.$touch();
        if (v$.value.$pending) return;

        if (v$.value.$invalid) {
            return false;
        }
    }

    try {
        isLoading.value = true;
        let payload = {
            leave_type: state.leave_type,
            notes: state.notes,
            user_id: props?.authuserid,
            start_date: state.start_date,
            end_date: state.end_date,
            number_of_days: numberOfDays.value,
        };
        payload = { ...payload, status: leaveStatus.value };
        const url = [
            "approved",
            "declined",
            "negotiated",
            "re-negotiated",
        ].includes(leaveStatus.value)
            ? `/leaves/${leave.value.uuid}/update-status`
            : `/apply-leave`;
        const response = await axios.post(url, payload);
        if (response.data) {
            errors.value = false;
            isLoading.value = false;
            window.location.reload();
        }
    } catch (error) {
        isLoading.value = false;
        console.error("Submission error:", error);

        if (error.response && error.response.data.errors) {
            syncErrorsWithVuelidate(error.response.data.errors);
        }
    }
};
const isChanged = computed(
    () =>
        state.leave_type !== leave.value.leave_type &&
        state.notes !== leave.value.notes &&
        state.status !== leave.value.status
);
const handleAccept = () => {
    const leaveStartDate = moment(leave.value.start_date, "DD/MM/YYYY", true);
    const stateStartDate = moment(state.start_date, "DD/MM/YYYY", true);
    const leaveEndDate = moment(leave.value.end_date, "DD/MM/YYYY", true);
    const stateEndDate = moment(state.end_date, "DD/MM/YYYY", true);
    if (
        leaveStartDate.isSame(stateStartDate) &&
        leaveEndDate.isSame(stateEndDate)
    ) {
        leaveStore.setLeaveStatus("approved");

        if (!isChanged.value) {
            handleSubmit();
        }
    } else {
        toast.warning(
            "Dates have been changed. Only renegotiation is possible.",
            {
                timeout: 3000,
                position: "top-right",
            }
        );
    }
};

const resetState = () => {
    state.leave_type = "";
    state.date = "";
    state.notes = "";
    state.status = "";
};
watch(
    () => leave.value,
    (newVal) => {
        if (Object.keys(newVal).length === 0) return resetState();
        state.leave_type = newVal.leave_type;
        state.start_date = moment(newVal.start_date, "DD/MM/YYYY").toDate();
        state.end_date = moment(newVal.end_date, "DD/MM/YYYY").toDate();
        state.number_of_days = newVal.number_of_days;
        state.notes = newVal.notes;
        state.status = newVal.status;
    },
    { deep: true }
);
const StartDateConfig = computed(() => {
    return {
        dateFormat: "d/m/Y",
        minDate:
            moment(state.start_date, "DD/MM/YYYY").toDate() ||
            moment().format("DD/MM/YYYY"),
    };
});

const DueDateConfig = computed(() => {
    return {
        dateFormat: "d/m/Y",
        minDate:
            moment(state.start_date, "DD/MM/YYYY").toDate() ||
            moment().format("DD/MM/YYYY"), // Ensure due date is not before start date
    };
});
const memberName = computed(() => {
    return member?.value.full_name
        ? member.value.full_name
        : leave.value.full_name;
});
const showMember = () => {
    const memberUuid = member?.value.uuid
        ? member.value.uuid
        : leave.value.user_uuid;
    const redirectUrl = `/teams/${memberUuid}`; // Adjust URL pattern as per your routing.
    window.location.href = redirectUrl;
};
onMounted(() => {
    isAuthUserAdmin.value =
        authUser.value.roles.findIndex((role) => role.name === "admin") !== -1;
});
</script>

<style scoped>
#div_top_hypers {
    background-color: #eeeeee;
    display: inline;
}
#ul_top_hypers li {
    display: inline;
}

.member-name {
    font-size: 14px; /* Adjust size as needed */
    font-weight: bold;
    text-transform: capitalize;
    float: right;
}
</style>
