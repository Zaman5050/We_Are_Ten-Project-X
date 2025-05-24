<template>
    <form @submit="handleSubmit">
        <div class="row">
            <div class="col-md-6">
                <div class="sign-up-input-container">
                    <label class="signup-labels">Project Name</label>
                    <input
                        class="signup-inputs mw-100"
                        placeholder="Enter Name"
                        type="text"
                        v-model="state.name"
                    />
                    <input-validation-error-message :v="v$?.name" />
                </div>
                <div class="sign-up-input-container">
                    <label for="projectAddress" class="signup-labels"
                        >Project Address</label
                    >
                    <input
                        class="signup-inputs mw-100"
                        placeholder="Enter Address"
                        type="text"
                        v-model="state.address"
                    />
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="">
                            <label class="signup-labels"
                                >Project Currency</label
                            >
                            <div class="create-new-project-select-container">
                                <input
                                    type="text"
                                    class="signup-inputs mw-100"
                                    value="GBP (£)"
                                    disabled
                                />
                                <input
                                    type="hidden"
                                    v-model="state.currency_id"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="">
                            <label class="signup-labels">Measurements</label>
                            <div class="create-new-project-select-container">
                                <select
                                    class="select"
                                    ref="measurementRef"
                                    required
                                    v-model="state.measurement_unit"
                                >
                                    <option value="Sq meter">Sq meter</option>
                                    <option value="Sq. Feet">Sq. Feet</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="sign-up-input-container">
                    <label class="signup-labels">Project Type</label>
                    <div
                        class="create-new-project-select-container select-full"
                    >
                        <select
                            class="select"
                            v-model="state.type"
                            name="type"
                            ref="projectTypeRef"
                        >
                            <option value="Residential">Residential</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Hospitality">Hospitality</option>
                            <option value="Retail">Retail</option>
                            <option value="Healthcare">Healthcare</option>
                            <option value="Institutional">Institutional</option>
                            <option value="Government">Government</option>
                            <option value="Community Space">
                                Community Space
                            </option>
                        </select>
                    </div>
                </div>
                <div class="sign-up-input-container h-auto mb-3">
                    <label class="signup-labels">Project Description</label>
                    <textarea
                        style="height: 156px; resize: none"
                        class="signup-inputs py-2 mw-100"
                        placeholder="Enter Description"
                        v-model="state.description"
                    ></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <p style="font-size: 16px; margin-bottom: 20px">
                    Set the currency and measurement system for this project.
                    These will be used for quoting, invoicing, and appear on
                    your schedule.
                </p>
                <div class="sign-up-input-container mb-4 h-auto">
                    <label class="signup-labels darker-grotesque-bold"
                        >Cover Image</label
                    >
                    <SingleImageUploader
                        @imagesUploaded="updateProjectImage"
                        :attachments="projectAttachment"
                        :sub-path="'projects/attachments'"
                    >
                        <label
                            class="darker-grotesque-bold fs-14"
                            for="addAttachment"
                        >
                            <img src="/assets/images/add-img-icon.svg" /> &nbsp;
                            <u>Add Image</u>
                        </label>
                    </SingleImageUploader>
                </div>

                <div
                    class="sign-up-input-container mb-3 h-auto"
                    v-if="project.members.length !== 0"
                >
                    <label class="signup-labels">TEAM MEMBERS</label>
                    <div
                        class="teams-members-container"
                        v-for="member in project.members"
                        :key="member.id"
                    >
                        <img
                            v-if="member.profile_picture"
                            class="nav-right-img m-0"
                            :src="member.profile_picture"
                        />

                        <Avatar v-else :name="member.short_name" />

                        <p style="font-size: 14px">{{ member.full_name }}</p>
                    </div>
                    <a
                        class="create-one d-block"
                        @click="
                            () => (isAddMemberEnabled = !isAddMemberEnabled)
                        "
                        >{{
                            isAddMemberEnabled ? "Cancel" : "Add Another Member"
                        }}
                    </a>
                </div>
                <div
                    class="sign-up-input-container mb-5 h-auto"
                    v-if="isAddMemberEnabled || project.members.length == 0"
                >
                    <label class="signup-labels">Team Members</label>
                    <div class="create-new-project-select-container">
                        <multiselect
                            v-model="state.member_ids"
                            :options="designers"
                            placeholder="Select one"
                            label="full_name"
                            track-by="full_name"
                            :multiple="true"
                            open-direction="bottom"
                        >
                        </multiselect>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div
                    class="sign-up-input-container"
                    style="height: auto; padding-bottom: 20px"
                >
                    <label class="signup-labels">Tags</label>
                    <div class="d-flex flex-column gap-3">
                        <multiselect
                            v-model="state.tags"
                            tag-placeholder="add as new tag"
                            placeholder="Enter tag"
                            label="tag_name"
                            track-by="id"
                            :multiple="true"
                            :options="state.tags"
                            :taggable="true"
                            :close-on-select="false"
                            :hide-selected="true"
                            @tag="handleAddTag"
                            :open-direction="bottom"
                        >
                        </multiselect>
                        <div>
                            <span
                                class="tags me-2"
                                v-for="(tag, index) in state.tags"
                                :key="index"
                                >{{ tag.tag_name }}</span
                            >
                        </div>
                    </div>
                </div>
                <div class="sign-up-input-container mb-3 h-auto">
                    <label class="signup-labels">Stages</label>
                    <div class="d-flex flex-column gap-3">
                        <multiselect
                            v-model="state.stages"
                            tag-placeholder="add as new label"
                            placeholder="Enter Stages"
                            label="stage_name"
                            track-by="id"
                            :multiple="true"
                            :options="state.stages"
                            :taggable="true"
                            :option-height="104"
                            :close-on-select="false"
                            :hide-selected="true"
                            @tag="handleAddStage"
                            open-direction="bottom"
                        >
                        </multiselect>
                        <div>
                            <span
                                class="tags me-2"
                                v-for="(stage, index) in state.stages"
                                :key="index"
                                >{{ stage.stage_name }}</span
                            >
                        </div>
                    </div>
                    <input-validation-error-message :v="v$?.stages" />
                </div>
                <div class="sign-up-input-container">
                    <label class="signup-labels">Start Date</label>
                    <flat-pickr
                        v-model="state.start_date"
                        :config="StartDateConfig"
                        placeholder="DD/MM/YYYY"
                        :class="['signup-inputs flatpickr-input mw-100 ']"
                    />
                    <input-validation-error-message :v="v$?.start_date" />
                </div>
                <div class="sign-up-input-container">
                    <label class="signup-labels">Due Date</label>
                    <flat-pickr
                        v-model="state.due_date"
                        :config="DueDateConfig"
                        placeholder="DD/MM/YYYY"
                        :class="['signup-inputs flatpickr-input mw-100 ']"
                    />
                    <input-validation-error-message :v="v$?.due_date" />
                </div>
            </div>
            <div class="sign-up-input-container h-auto" v-if="checkAuthUser">
                <label
                    class="check-container signup-labels"
                    style="font-size: 16px; display: block !important"
                >
                    Include a procurement budget in this project.
                    <input
                        type="checkbox"
                        v-model="state.is_procurement_enable"
                    />
                    <span class="checkmark"></span>
                </label>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <button class="log-in mw-100" type="submit">Save</button>
            </div>
        </div>
    </form>
</template>

<script setup>
import { reactive, onMounted, ref, computed } from "vue";
import Multiselect from "vue-multiselect";
import Datepicker from "vue3-datepicker";
// import VueGoogleAutocomplete from "vue-google-autocomplete";
import axios from "axios";
import SingleImageUploader from "../Common/SingleImageUploader.vue";
import Avatar from "@/components/Avatar.vue";
import flatPickr from "vue-flatpickr-component";
import { computeTimezoneDate } from "@/helpers.js";
import { useUserStore } from "../../stores/userStore";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers } from "@vuelidate/validators";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { useToast } from "vue-toastification";

const userStore = useUserStore();
const timezone = computed(() => userStore.authUser?.timezone || "UTC");
const toast = useToast();

const props = defineProps({
    designers: {
        type: Array,
        default: {},
    },
    currencies: {
        type: Array,
        default: {},
    },
    project: {
        type: Object,
        default: {},
    },
    checkAuthUser: {
        type: Boolean,
        default: false,
    },
});

const isAddMemberEnabled = ref(false);

const state = reactive({
    name: props.project.name,
    address: props.project.address,
    currency_id: props.project.currency_id,
    measurement_unit: props.project.measurement_unit,
    member_ids: props.project.members,
    tags: props.project.tags,
    type: props.project.type,
    description: props.project.description,
    stages: props.project.stages,
    is_procurement_enable: props.project.is_procurement_enable ? true : false,
    start_date: props.project.start_date
        ? computeTimezoneDate(props.project.start_date, timezone.value)
        : "",
    due_date: props.project.due_date
        ? computeTimezoneDate(props.project.due_date, timezone.value)
        : "",
    attachment: props.project.attachment ?? null,
});

const populotedAddress = ref("");

const getAddressData = (addressData, placeResultData) => {
    const { country, latitude, longitude } = addressData;
    // console.log([country ?? '',latitude,longitude]);
    state.address = placeResultData?.formatted_address ?? "";
    state.longitude = longitude ?? "";
    state.latitude = latitude ?? "";
};

const handleAddressInputChange = (data) => {
    populotedAddress.value = data?.newVal ?? "";
};

const projectAttachment = computed(() => {
    return state.attachment ? [state.attachment] : [];
});
const updateProjectImage = (attachment) => {
    state.attachment = attachment;
};
const handleSubmit = async (event) => {
    event.preventDefault();
    v$.value.$touch();
    if (v$.value.$pending) return;
    if (v$.value.$invalid) {
        return false;
    }
    const data = { ...state };

    if (state.attachment) {
        data.attachments = [data.attachment?.uuid] ?? [];
        data.attachment = null;
    }

    try {
        const response = await axios.put(
            `/projects/${props.project.uuid}`,
            data
        );
        if (response.data) {
            toast.success(response?.data?.message || "Success!", {
                timeout: 2000,
                position: "top-right",
            });
        }
        setTimeout(function () {
            window.location.reload();
        }, 2000);
    } catch (error) {
        console.error("Error creating project:", error);
    }
};

const handleAddStage = (stage_name) => {
    state.stages.push({ stage_name });
};

const handleAddTag = (tag_name) => {
    state.tags.push({ tag_name });
};

const projectTypeRef = ref(null);
const measurementRef = ref(null);

const initializeNiceSelect = () => {
    // Initialize NiceSelect
    $(projectTypeRef.value).niceSelect("destroy");
    $(projectTypeRef.value).niceSelect();
    $(projectTypeRef.value).on("change", function () {
        state.type = $(this).val();
    });
    $(measurementRef.value).niceSelect("destroy");
    $(measurementRef.value).niceSelect();
    $(measurementRef.value).on("change", function () {
        state.measurement_unit = $(this).val();
    });
};

const setInitialState = () => {
    projectTypeRef.value = props.project.type;
    $(projectTypeRef).val(projectTypeRef.value);

    measurementRef.value = props.project.measurement_unit;
    $(measurementRef).val(measurementRef.value);
};

onMounted(() => {
    initializeNiceSelect();
    setInitialState();
    populotedAddress.value = state.address ?? "";
});
const StartDateConfig = computed(() => {
    return {
        dateFormat: "d/m/Y",
    };
});

const DueDateConfig = computed(() => {
    return {
        dateFormat: "d/m/Y",
    };
});
const rules = computed(() => {
    const baseRules = {
        name: {
            required: helpers.withMessage(
                "Project name is required.",
                required
            ),
        },
        start_date: {
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
        due_date: {
            afterStartDate: state.due_date
                ? helpers.withMessage(
                      "Due Date cannot be before the Start Date.",
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
    };

    return baseRules;
});
const v$ = useVuelidate(rules, state);
</script>

<!-- Add Multiselect CSS. Can be added as a static asset or inside a component. -->
<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style scoped>
textarea {
    height: 72px;
    resize: none;
}
button {
    max-width: 417.5px;
}
</style>

<style>
.multiselect__tag {
    background: #252c32 !important;
}
.tags {
    /* background-color: #94aef2de; */
    text-transform: uppercase;
}
</style>
