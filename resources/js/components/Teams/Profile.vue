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
                        :value="props.member.email"
                        placeholder="Email"
                        class="signup-inputs"
                        disabled
                    />
                    <input-validation-error-message :v="v$?.email" />
                </div>

                <div
                    class="sign-up-input-container mb-4 h-auto"
                    v-if="!is_member_profile"
                >
                    <label for="addAttachment" class="signup-labels"
                        >Upload profile picture</label
                    >

                    <div class="upload__box-only-name">
                        <div
                            v-if="!imagePreview"
                            class="upload__btn-box-only-name"
                        >
                            <label role="button" for="addAttachment">
                                <img src="/assets/images/add-img-icon.svg" />
                                &nbsp;
                                <u>Add Image</u>
                            </label>

                            <input
                                id="addAttachment"
                                type="file"
                                @change="onFileChange"
                                accept=".dwg, image/png, image/gif, image/jpeg"
                                class="upload__inputfile-only-name"
                                hidden
                            />
                        </div>

                        <div
                            v-if="imagePreview"
                            class="upload__img-wrap-only-name"
                        >
                            <div class="img-container">
                                <!-- Display blob image -->
                                <img
                                    :src="imagePreview"
                                    alt="Selected Image"
                                    class="img-block"
                                />

                                <!-- Cross button to remove the image -->
                                <button
                                    type="button"
                                    @click.prevent="deleteImage"
                                    class="remove-image-button"
                                >
                                    &times;
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="sign-up-input-container"
                    v-if="props.role !== 'admin'"
                >
                    <label class="signup-labels" for="can_procure">Role</label>

                    <input
                        disabled
                        id="can_procure"
                        type="text"
                        :value="state.can_procure?.label"
                        class="signup-inputs"
                    />
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
                <div class="sign-up-input-container" v-if="is_member_profile">
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
                <div class="sign-up-input-container" v-if="is_member_profile">
                    <label for="leaving_date" class="signup-labels"
                        >leaving date</label
                    >
                    <flat-pickr
                        v-model="state.leaving_date"
                        id="leaving_date"
                        style="max-width: 400px !important"
                        :config="StartDateConfig"
                        placeholder="DD/MM/YYYY"
                        class="signup-inputs"
                    />
                </div>

                <div class="sign-up-input-container" v-if="is_member_profile">
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
                <div class="sign-up-input-container" v-if="is_member_profile">
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
                <div
                    class="sign-up-input-container"
                    v-if="props.role !== 'admin'"
                >
                    <label for="company_website" class="signup-labels"
                        >Company website</label
                    >
                    <input
                        id="company_website"
                        type="text"
                        placeholder="company@website.com"
                        class="signup-inputs"
                        :value="member?.company?.virtual_url"
                        disabled
                    />
                    <input-validation-error-message :v="v$?.company_website" />
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

                <div
                    class="border-container"
                    v-if="!is_member_profile && props.role !== 'admin'"
                >
                    <div class="row justify-content-between">
                        <div class="col-3">
                            <label class="signup-labels text-nowrap text-center"
                                >Sick leave</label
                            >
                            <div class="text-center fs-14 bg-yellow br-6">
                                {{
                                    props.role !== "admin"
                                        ? member?.sick_leaves ?? "---"
                                        : "---"
                                }}
                            </div>
                        </div>
                        <div class="col-3">
                            <label class="signup-labels text-nowrap text-center"
                                >Casual leave</label
                            >
                            <div class="text-center fs-14 bg-yellow br-6">
                                {{
                                    props.role !== "admin"
                                        ? member?.casual_leaves ?? "---"
                                        : "---"
                                }}
                            </div>
                        </div>
                        <div class="col-3">
                            <label class="signup-labels text-nowrap text-center"
                                >Annual leave</label
                            >
                            <div class="text-center fs-14 bg-yellow br-6">
                                {{
                                    props.role !== "admin"
                                        ? member?.annual_leaves ?? "---"
                                        : "---"
                                }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sign-up-input-container mw-400">
                    <div class="row" v-if="is_member_profile">
                        <div class="col-4">
                            <label class="signup-labels">Sick leave </label>
                            <input
                                class="signup-inputs"
                                type="number"
                                min="0"
                                placeholder="Enter Sick Leave"
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
        <vue-loader v-show="isLoading" />
    </form>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from "vue";
import Multiselect from "vue-multiselect";
import axios from "axios";
import "vue-tel-input/vue-tel-input.css";
import flatPickr from "vue-flatpickr-component";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers, email } from "@vuelidate/validators";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { useToast } from "vue-toastification";
import VueLoader from "../Common/Loader.vue";

const props = defineProps({
    member: {
        type: Object,
        default: () => {},
    },
    timezones: {
        type: Object,
        required: true,
        default: [],
    },
    role: {
        type: String,
        default: "designer",
    },
    is_member_profile: {
        type: Boolean,
        default: false,
    },
});

const apiErrorMessage = ref("");
const profileImage = ref(null);
const imagePreview = ref(null);
const toast = useToast();
const isLoading = ref(false);

const procurement = ref([
    { value: 1, label: "Designer With Procurement Rights" },
    { value: 0, label: "Designer Without Procurement Rights" },
]);

const selectedProcurement = computed(() => {
    return procurement.value.find(
        (p) => p.value === +props?.member?.can_procure ?? false
    );
});

const selectedTimezone = computed(() => {
    return props.timezones.find((p) => p.value === props?.member?.timezone);
});

const loading = ref(false);
const state = reactive({
    first_name: props.member.first_name,
    last_name: props.member.last_name,
    phone_number: props.member.phone_number,
    can_procure: selectedProcurement.value,
    timezone: selectedTimezone.value,
    location: props.member.location,
    profile_picture: props.member.profile_picture,
    hourly_rate: props.member.hourly_rate,
    leaving_date: props.member.leaving_date,
    joining_date: props.member.joining_date,
    sick_leaves: props.member.sick_leaves ?? 0,
    casual_leaves: props.member.casual_leaves ?? 0,
    annual_leaves: props.member.annual_leaves ?? 0,
});
const rules = computed(() => ({
    first_name: {
        required: helpers.withMessage("First name is required.", required),
    },
    last_name: {
        required: helpers.withMessage("Last name is required.", required),
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
    hourly_rate: props.is_member_profile
        ? {
              required: helpers.withMessage(
                  "Hourly rate is required.",
                  required
              ),
          }
        : {},
    joining_date: props.is_member_profile
        ? {
              required: helpers.withMessage(
                  "Joining date is required.",
                  required
              ),
          }
        : {},
    sick_leaves: props.is_member_profile
        ? {
              required: helpers.withMessage(
                  "Sick leave is required.",
                  required
              ),
              min: helpers.withMessage(
                  "Annual leave cannot be negative.",
                  (value) => value >= 0
              ),
          }
        : {},
    casual_leaves: props.is_member_profile
        ? {
              required: helpers.withMessage(
                  "Casual leave is required.",
                  required
              ),
              min: helpers.withMessage(
                  "Annual leave cannot be negative.",
                  (value) => value >= 0
              ),
          }
        : {},
    annual_leaves: props.is_member_profile
        ? {
              min: helpers.withMessage(
                  "Annual leave cannot be negative.",
                  (value) => value >= 0
              ),
          }
        : {},
}));

const v$ = useVuelidate(rules, state);
const telObj = ref(null);

// profile_file
const onFileChange = async (event) => {
    const fileInput = event.target;
    const file = event.target.files[0];
    const MAX_FILE_SIZE = 20 * 1024 * 1024; // 20MB

    if (file) {
        if (file.size > MAX_FILE_SIZE) {
            // Show a toast notification
            toast.error("You cannot upload an image larger than 20MB.", {
                timeout: 3000,
                position: "top-right",
            });
            fileInput.value = "";
            return;
        }
        isLoading.value = true;
        profileImage.value = file;
        imagePreview.value = URL.createObjectURL(file); // Create preview URL

        const formData = new FormData();
        formData.append("sub_path", `/users/${props.member?.uuid}`);
        formData.append("attachments[]", profileImage.value);

        try {
            const response = await axios.post(
                "/users/upload-profile-picture",
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                }
            );
            imagePreview.value = response?.data?.attachments[0]?.url;
            state.profile_picture = imagePreview.value;
            isLoading.value = false;
        } catch (error) {
            isLoading.value = false;
            console.error("Profile Image upload failed:", error);
        }
    }
};

const deleteImage = async () => {
    try {
        isLoading.value = true;
        await axios.post(
            "/users/delete-profile-picture",
            {
                attachment_url: imagePreview.value,
            },
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );
        profileImage.value = null;
        imagePreview.value = null;
        state.profile_picture = null;
        isLoading.value = false;
    } catch (error) {
        isLoading.value = false;
        console.error("Image removal failed:", error);
    }
};

onMounted(() => {
    imagePreview.value = props.member.profile_picture ?? null;
});

const handleSubmit = async () => {
    v$.value.$touch(); // Mark fields as touched to show errors
    if (v$.value.$pending) return; // Wait for any async validation

    if (v$.value.$invalid) {
        return false;
    }

    loading.value = true;

    const payload = {
        ...state,
        can_procure: state?.can_procure?.value,
        timezone: state?.timezone?.value,
    };

    try {
        const memberUuid = props.member.uuid ?? "";
        const response = await axios.post(`/profile/${memberUuid}`, payload);

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
                window.location.reload();
            }, 2000);
        }
    } catch (error) {
        if (error.response.status === 422) {
        }
        loading.value = false;
        console.error("Error:", error);
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
