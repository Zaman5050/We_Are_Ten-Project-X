<template>
    <form @submit.prevent="handleSubmit()" :action="props.action" method="POST">
        <div v-if="apiErrorMessage" class="alert alert-danger">
            {{ apiErrorMessage }}
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="sign-up-input-container">
                    <label for="first_name" class="signup-labels"
                        >First Name</label
                    >
                    <input
                        id="first_name"
                        type="text"
                        name="first_name"
                        v-model="state.first_name"
                        placeholder="First Name"
                        class="signup-inputs"
                    />
                    <input-validation-error-message :v="v$?.first_name" />
                </div>

                <div class="sign-up-input-container">
                    <label for="email" class="signup-labels">Email</label>
                    <input
                        id="email"
                        type="email"
                        v-model="state.email"
                        placeholder="Enter Email"
                        class="signup-inputs"
                    />
                    <input-validation-error-message :v="v$?.email" />
                </div>

                <div class="sign-up-input-container">
                    <label for="location" class="signup-labels">Location</label>
                    <input
                        id="location"
                        type="text"
                        name="location"
                        v-model="state.location"
                        placeholder="Location"
                        class="signup-inputs"
                    />
                    <input-validation-error-message :v="v$?.location" />
                </div>
                <div class="sign-up-input-container">
                    <label for="joining_date" class="signup-labels"
                        >joining date</label
                    >
                    <flat-pickr
                        v-model="state.joining_date"
                        id="joining_date"
                        style="max-width: 400px !important"
                        :config="StartDateConfig"
                        placeholder="DD/MM/YYYY"
                        class="signup-inputs"
                    />
                    <input-validation-error-message :v="v$?.joining_date" />
                </div>

                <div class="sign-up-input-container">
                    <label class="signup-labels" for="timezone">Timezone</label>
                    <div class="create-new-project-select-container">
                        <multiselect
                            v-model="state.timezone"
                            :options="props.timezones"
                            placeholder="Select one"
                            label="label"
                            track-by="value"
                            :multiple="false"
                            open-direction="bottom"
                        >
                        </multiselect>
                        <input-validation-error-message :v="v$?.timezone" />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="sign-up-input-container">
                    <label for="last_name" class="signup-labels"
                        >Last Name</label
                    >
                    <input
                        id="last_name"
                        type="text"
                        name="last_name"
                        v-model="state.last_name"
                        placeholder="Last Name"
                        class="signup-inputs"
                    />
                    <input-validation-error-message :v="v$?.last_name" />
                </div>
                <div class="sign-up-input-container">
                    <label for="phone_number" class="signup-labels"
                        >Phone number</label
                    >
                    <vue-tel-input
                        @on-input="(phoneString, obj) => (telObj = obj)"
                        :validCharactersOnly="true"
                        class="signup-inputs"
                        id="phone_number"
                        name="phone_number"
                        v-model="state.phone_number"
                        @blur="isNewSupplierEnable && v$.$touch()"
                    />
                    <input-validation-error-message :v="v$?.phone_number" />
                </div>
                <div class="sign-up-input-container">
                    <label class="signup-labels" for="can_procure"
                        >Designtation</label
                    >
                    <div class="create-new-project-select-container">
                        <multiselect
                            v-model="state.can_procure"
                            :options="procurement"
                            placeholder="Select one"
                            label="label"
                            track-by="value"
                            :multiple="false"
                            open-direction="bottom"
                        >
                        </multiselect>
                        <ValidationError :errors="errors" field="can_procure" />
                    </div>
                </div>
                <div class="sign-up-input-container">
                    <label for="company_website" class="signup-labels"
                        >Company website</label
                    >
                    <input
                        id="company_website"
                        type="text"
                        placeholder="Enter Website"
                        class="signup-inputs"
                        v-model="state.company_website"
                    />
                    <input-validation-error-message :v="v$?.company_website" />
                </div>
                <div class="sign-up-input-container">
                    <label class="signup-labels" for="hourly_rate"
                        >Hourly Rate</label
                    >
                    <div class="input-group">
                        <div class="input-group-append">
                            <span
                                class="input-group-text signup-inputs"
                                id="basic-addon2"
                                >£
                            </span>
                        </div>
                        <input
                            id="hourly_rate"
                            type="number"
                            min="0"
                            placeholder="Enter Hourly Rate"
                            style="max-width: 355px"
                            class="signup-inputs"
                            v-model="state.hourly_rate"
                        />
                        <input-validation-error-message :v="v$?.hourly_rate" />
                    </div>
                </div>

                <div class="sign-up-input-container mw-400">
                    <div class="row">
                        <div class="col-4">
                            <label class="signup-labels">Sick leave </label>
                            <input
                                class="signup-inputs"
                                type="number"
                                placeholder="Enter Sick Leave"
                                min="0"
                                v-model="state.sick_leaves"
                            />
                            <input-validation-error-message
                                :v="v$?.sick_leaves"
                            />
                        </div>
                        <div class="col-4">
                            <label class="signup-labels">Casual leave</label>
                            <input
                                class="signup-inputs"
                                type="number"
                                min="0"
                                placeholder="Enter Casual Leave"
                                v-model="state.casual_leaves"
                            />
                            <input-validation-error-message
                                :v="v$?.casual_leaves"
                            />
                        </div>
                        <div class="col-4">
                            <label class="signup-labels">Annual leave</label>
                            <input
                                class="signup-inputs"
                                type="number"
                                min="0"
                                placeholder="Enter Annual Leave"
                                v-model="state.annual_leaves"
                            />
                            <input-validation-error-message
                                :v="v$?.annual_leaves"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button :disabled="loading" class="log-in" type="submit">
                    Save
                </button>
            </div>
        </div>
    </form>
</template>

<script setup>
import { ref, defineProps, onMounted, reactive, computed } from "vue";
import Multiselect from "vue-multiselect";
import axios from "axios";
import ValidationError from "../ValidationError.vue";
import "vue-tel-input/vue-tel-input.css";
import flatPickr from "vue-flatpickr-component";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers, email, maxLength } from "@vuelidate/validators";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { useToast } from "vue-toastification";

const loading = ref(false);
const state = reactive({
    first_name: "",
    last_name: "",
    email: "",
    phone_number: "",
    can_procure: false,
    timezone: null,
    location: "",
    company_website: "",
    hourly_rate: "",
    sick_leaves: 0,
    casual_leaves: 0,
    annual_leaves: 0,
    joining_date: "",
});

const procurement = ref([
    { value: 1, label: "Designer With Procurement Rights" },
    { value: 0, label: "Designer Without Procurement Rights" },
]);

const props = defineProps({
    action: "",
    timezones: {
        type: Object,
        required: true,
        default: [],
    },
});

const apiErrorMessage = ref("");
const profileImage = ref(null);
const imagePreview = ref(null);
const toast = useToast();

const rules = computed(() => ({
    first_name: {
        required: helpers.withMessage("First name is required.", required),
    },
    last_name: {
        required: helpers.withMessage("Last name is required.", required),
    },
    email: {
        required: helpers.withMessage("Email is required.", required),
        email: helpers.withMessage("Invalid email format.", email),
        maxLength: maxLength(255),
    },
    phone_number: {
        required: helpers.withMessage("Phone number is required.", required),
        handlePhoneNumberValidation: helpers.withMessage(
            "Invalid phone number format.",
            (value) => {
                return telObj.value?.valid || !value;
            }
        ),
    },
    can_procure: {
        required: helpers.withMessage(
            "Procurement permission is required.",
            required
        ),
    },
    timezone: {
        required: helpers.withMessage("Timezone is required.", required),
    },
    location: {
        required: helpers.withMessage("Location is required.", required),
    },
    hourly_rate: {
        required: helpers.withMessage("Hourly rate is required.", required),
    },
    joining_date: {
        required: helpers.withMessage("Joining date is required.", required),
    },
    sick_leaves: {
        required: helpers.withMessage("Sick leave is required.", required),
        min: helpers.withMessage(
            "Sick leave cannot be negative.",
            (value) => value >= 0
        ),
    },
    casual_leaves: {
        required: helpers.withMessage("Casual leave is required.", required),
        min: helpers.withMessage(
            "Casual leave cannot be negative.",
            (value) => value >= 0
        ),
    },
    annual_leaves: {
        min: helpers.withMessage(
            "Annual leave cannot be negative.",
            (value) => value >= 0
        ),
    },
}));

const v$ = useVuelidate(rules, state);
const telObj = ref(null);

const handleSubmit = async () => {
    v$.value.$touch();
    if (v$.value.$pending) return;

    if (v$.value.$invalid) {
        return false;
    }

    loading.value = true;

    const data = { ...state };

    data.can_procure = state?.can_procure?.value;
    data.timezone = state?.timezone?.value;

    try {
        const response = await axios.post(props.action, data);
        loading.value = false;
        if (response.data?.error) {
            apiErrorMessage.value =
                response.data?.message ?? "Something went wrong";
            toast.success(apiErrorMessage.value, {
                timeout: 3000,
                position: "top-right",
            });
        } else {
            toast.success(response?.data?.message || "Success!", {
                timeout: 2000,
                position: "top-right",
            });
            setTimeout(() => {
                window.location.assign("/teams");
            }, 2000);
        }
    } catch (error) {
        loading.value = false;
    }
};
const StartDateConfig = computed(() => {
    return {
        dateFormat: "d/m/Y",
    };
});
</script>

<style scoped>
.create-new-project-select-container {
    max-width: 400px;
}

#phone_number {
    padding: 0;
    border: 0;
    border-radius: 6px;
}
</style>
