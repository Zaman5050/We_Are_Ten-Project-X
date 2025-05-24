<template>
    <div class="container">
        <form @submit.prevent="handleSubmit()">
            <div class="d-flex gap-3 mb-3">
                <div
                    class="create-project-tab"
                    :class="{ active: selectedTab === 1 }"
                    @click="selectedTab = 1"
                >
                    <div class="tab-index"><b>1</b></div>
                    <p>Details</p>
                </div>
                <div
                    v-if="checkAuthUser"
                    class="create-project-tab"
                    :class="{ active: selectedTab === 2 }"
                    @click="selectedTab = 2"
                >
                    <div class="tab-index"><b>2</b></div>
                    <p>Budget</p>
                </div>
            </div>
            <div v-if="selectedTab === 1" class="tab-content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="sign-up-input-container">
                            <label class="text-uppercase text-secondary mb-13"
                                >Project Name</label
                            >
                            <input
                                class="signup-inputs mw-100"
                                placeholder="Project Name"
                                v-model="state.name"
                            />
                            <input-validation-error-message :v="v$?.name" />
                        </div>

                        <div class="sign-up-input-container">
                            <label
                                for="projectAddress"
                                class="text-uppercase text-secondary mb-13"
                                >Project Address</label
                            >
                            <input
                                class="signup-inputs mw-100"
                                placeholder="Enter Address"
                                type="text"
                                v-model="state.address"
                            />
                            <!-- <vue-gocogle-autoomplete
                            id="projectAddress"
                            classname="signup-inputs mw-100"
                            placeholder="Project Address"
                            v-model="state.address"
                            @placechanged="getAddressData">
                        </vue-google-autocomplete> -->
                        </div>

                        <div class="sign-up-input-container">
                            <label class="text-uppercase text-secondary mb-13"
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

                        <div class="sign-up-input-container">
                            <label class="text-uppercase text-secondary mb-13"
                                >Measurements</label
                            >
                            <div class="create-new-project-select-container">
                                <select
                                    class="select"
                                    ref="measurementRef"
                                    required
                                >
                                    <option value="Sq meter">Sq meter</option>
                                    <option value="Sq. Feet">Sq. Feet</option>
                                </select>
                            </div>
                        </div>

                        <div class="sign-up-input-container h-auto mb-4">
                            <label class="text-uppercase text-secondary mb-13"
                                >Team Members</label
                            >
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

                        <div
                            class="sign-up-input-container"
                            style="height: auto; padding-bottom: 20px"
                        >
                            <label class="text-uppercase text-secondary mb-13"
                                >Tags</label
                            >
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
                                    open-direction="bottom"
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
                    </div>

                    <div class="col-md-6">
                        <div class="sign-up-input-container">
                            <label class="text-uppercase text-secondary mb-13"
                                >Project Type</label
                            >
                            <div class="create-new-project-select-container">
                                <select
                                    class="select"
                                    v-model="state.type"
                                    name="type"
                                    ref="projectTypeRef"
                                >
                                    <option value="Residential">
                                        Residential
                                    </option>
                                    <option value="Commercial">
                                        Commercial
                                    </option>
                                    <option value="Hospitality">
                                        Hospitality
                                    </option>
                                    <option value="Retail">Retail</option>
                                    <option value="Healthcare">
                                        Healthcare
                                    </option>
                                    <option value="Institutional">
                                        Institutional
                                    </option>
                                    <option value="Government">
                                        Government
                                    </option>
                                    <option value="Community Space">
                                        Community Space
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="sign-up-input-container">
                            <label class="text-uppercase text-secondary mb-13"
                                >Project Description</label
                            >
                            <textarea
                                class="signup-inputs py-2"
                                placeholder="Project Description"
                                v-model="state.description"
                            >
                            </textarea>
                        </div>

                        <div
                            class="sign-up-input-container my-4"
                            style="height: auto; padding-top: 20px"
                        >
                            <div
                                class="sign-up-input-container mb-4"
                                style="height: auto"
                            >
                                <SingleImageUploader
                                    @imagesUploaded="updateProjectImage"
                                    :attachments="projectAttachment"
                                    :sub-path="'projects/attachments'"
                                >
                                    <label for="addAttachment">
                                        <img
                                            src="/assets/images/add-img-icon.svg"
                                        />
                                        &nbsp;
                                        <u>Add Image</u>
                                    </label>
                                </SingleImageUploader>
                            </div>
                        </div>

                        <div class="sign-up-input-container mb-3 h-auto">
                            <label class="text-uppercase text-secondary mb-13"
                                >Stages</label
                            >
                            <div class="d-flex flex-column gap-3">
                                <multiselect
                                    v-model="state.stages"
                                    tag-placeholder="add as new label"
                                    placeholder="Enter Stage"
                                    label="stage_name"
                                    track-by="id"
                                    :multiple="true"
                                    :options="state.stages"
                                    :taggable="true"
                                    :option-height="104"
                                    :close-on-select="false"
                                    :hide-selected="true"
                                    @tag="handleAddStage"
                                    @remove="handleRemoveStage"
                                    open-direction="bottom"
                                >
                                </multiselect>
                                <!-- <input-validation-error-message
                                    v-if="
                                        v$?.stages?.$invalid &&
                                        state.stages.length === 0
                                    "
                                    :v="v$.stages"
                                /> -->
                                <div class="multi-select-tags-container">
                                    <span
                                        class="tags me-2"
                                        v-for="(stage, index) in state.stages"
                                        :key="index"
                                        >{{ stage.stage_name }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="sign-up-input-container">
                            <label class="text-uppercase text-secondary mb-13"
                                >Start Date</label
                            >
                            <flat-pickr
                                v-model="state.start_date"
                                :config="StartDateConfig"
                                placeholder="DD/MM/YYYY"
                                :class="[
                                    'signup-inputs flatpickr-input mw-100 ',
                                ]"
                            />
                            <input-validation-error-message
                                :v="v$?.start_date"
                            />
                        </div>

                        <div class="sign-up-input-container">
                            <label class="text-uppercase text-secondary mb-13"
                                >Due Date</label
                            >
                            <flat-pickr
                                v-model="state.due_date"
                                :config="DueDateConfig"
                                placeholder="DD/MM/YYYY"
                                :class="[
                                    'signup-inputs flatpickr-input mw-100 ',
                                ]"
                            />
                            <input-validation-error-message :v="v$?.due_date" />
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="selectedTab === 2" class="tab-content">
                <div class="mb-5">
                    <div
                        v-for="(stage, index) in state.stages"
                        :key="index"
                        class="d-flex"
                    >
                        <div style="width: 10%">
                            <p
                                class="signup-labels mb-0 mt-33 project-type-budget"
                            >
                                {{ stage.stage_name.toUpperCase() }}
                            </p>
                        </div>
                        <div style="width: 90%">
                            <div class="row">
                                <div class="col-4">
                                    <div class="sign-up-input-container">
                                        <label
                                            class="text-uppercase text-secondary mb-13"
                                            >Amount</label
                                        >
                                        <div class="input-group">
                                            <div class="currency-select w-60">
                                                <input
                                                    type="text"
                                                    class="form-control signup-inputs mw-100"
                                                    placeholder="£"
                                                    disabled
                                                />
                                            </div>
                                            <input
                                                type="text"
                                                class="form-control signup-inputs mw-100"
                                                placeholder="0"
                                                v-model="stage.amount"
                                            />
                                        </div>
                                        <input-validation-error-message
                                            v-if="v$.stages && v$.stages[index]"
                                            :v="v$.stages[index].amount"
                                        />
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="sign-up-input-container">
                                        <label
                                            class="text-uppercase text-secondary mb-13"
                                            >Start Date</label
                                        >
                                        <flat-pickr
                                            v-model="stage.start_date"
                                            :config="TabTwoDateConfig"
                                            placeholder="DD/MM/YYYY"
                                            :class="[
                                                'signup-inputs flatpickr-input mw-100',
                                            ]"
                                        />
                                        <input-validation-error-message
                                            v-if="v$.stages && v$.stages[index]"
                                            :v="v$.stages[index].start_date"
                                        />
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="">
                                        <label
                                            class="text-uppercase text-secondary mb-13"
                                            >Due Date</label
                                        >
                                        <flat-pickr
                                            v-model="stage.due_date"
                                            :config="TabTwoDateConfig"
                                            placeholder="DD/MM/YYYY"
                                            :class="[
                                                'signup-inputs flatpickr-input mw-100',
                                            ]"
                                        />
                                        <input-validation-error-message
                                            v-if="v$.stages && v$.stages[index]"
                                            :v="v$.stages[index].due_date"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sign-up-input-container h-auto">
                        <label
                            class="check-container text-uppercase text-secondary mb-13"
                            style="font-size: 16px; display: block !important"
                        >
                            Include a procurement budget in this project.
                            <input
                                type="checkbox"
                                v-model="state.is_procurement_enable"
                                :checked="state.is_procurement_enable"
                            />
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
            </div>
            <button
                class="log-in-draft"
                type="button"
                :disabled="isLoading"
                @click="handleSaveDraft"
            >
                Save as Draft
            </button>
            <button class="log-in" type="submit" :disabled="isLoading">
                Submit
            </button>
        </form>
    </div>
</template>

<script setup>
import { reactive, onMounted, ref, watch, nextTick } from "vue";
import Multiselect from "vue-multiselect";
import Datepicker from "vue3-datepicker";
//import VueGoogleAutocomplete from "vue-google-autocomplete";
import axios from "axios";
import SingleImageUploader from "../Common/SingleImageUploader.vue";
import { computed } from "vue";
import moment from "moment-timezone";
import { useUserStore } from "../../stores/userStore";
import flatPickr from "vue-flatpickr-component";
import { v4 as uuidv4 } from "uuid";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers } from "@vuelidate/validators";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { useProjectStore } from "../../stores/projectStore";
import { storeToRefs } from "pinia";
import { computeTimezoneDate } from "@/helpers.js";

const projectStore = useProjectStore();
const { project, mode } = storeToRefs(projectStore);

const userStore = useUserStore();

const timezone = computed(() => userStore.authUser?.timezone || "UTC");

const props = defineProps({
    designers: {
        type: Array,
        default: {},
    },
    currencies: {
        type: Array,
        default: {},
    },
    checkAuthUser: {
        type: Boolean,
        default: false,
    },
});

const state = reactive({
    name: "",
    address: "",
    currency_id: "",
    measurement_unit: "",
    member_ids: [],
    tags: [],
    type: "",
    description: "",
    stages: [],
    start_date: "",
    due_date: "",
    attachment: null,
    is_procurement_enable: false,
    status: "",
    projectUuid: "",
});
const handleSaveDraft = async () => {
    state.status = "draft";
    v$.value.$touch();
    if (v$.value.name.$invalid) {
        selectedTab.value = 1;
        return false;
    } else if (
        state.stages.length !== 0 &&
        props.checkAuthUser &&
        state.stages.some(
            (stage, index) =>
                v$.value.stages[index].amount?.$invalid ||
                v$.value.stages[index].start_date?.$invalid ||
                v$.value.stages[index].due_date?.$invalid
        )
    ) {
        selectedTab.value = 2;
        return false;
    }
    await saveProject();
};
const selectedTab = ref(1);

const getAddressData = (addressData, placeResultData) => {
    const { country, latitude, longitude } = addressData;
    // console.log([country ?? '',latitude,longitude]);
    state.address = placeResultData?.formatted_address ?? "";
    state.longitude = longitude ?? "";
    state.latitude = latitude ?? "";
};

const projectAttachment = computed(() => {
    return state.attachment ? [state.attachment] : [];
});

const updateProjectImage = (attachment) => {
    state.attachment = attachment;
};
const rules = computed(() => {
    // Default base rules
    const baseRules = {
        name: {
            required: helpers.withMessage(
                "Project name is required.",
                required
            ),
        },
    };
    // Handle stages validation if not in draft
    // if (state.stages.length === 0 && state.status !== 'draft') {
    //     baseRules.stages = {
    //         required: helpers.withMessage("Stages are required.", required),
    //     };
    // } else if
    if (props.checkAuthUser) {
        baseRules.stages = state.stages.map((stage) => ({
            amount: {
                required: helpers.withMessage("Amount is required.", required),
                numeric: helpers.withMessage(
                    "Amount must be a number.",
                    (value) => value !== null && !isNaN(value)
                ),
            },
            start_date: {
                beforeDueDate: stage.due_date
                    ? helpers.withMessage(
                          "Start Date cannot be after the Due Date.",
                          (value) => {
                              const inputDate = moment(
                                  value,
                                  "DD/MM/YYYY",
                                  true
                              );
                              const stageDueDate = moment(
                                  stage.due_date,
                                  "DD/MM/YYYY",
                                  true
                              );
                              return (
                                  inputDate.isValid() &&
                                  (!stageDueDate.isValid() ||
                                      inputDate.isSameOrBefore(
                                          stageDueDate,
                                          "day"
                                      ))
                              );
                          }
                      )
                    : {},
            },
            due_date: {
                withinRange:
                    state.start_date && state.due_date
                        ? helpers.withMessage(
                              "Due Date must be within the project's date range.",
                              (value) => {
                                  const inputDate = moment(
                                      value,
                                      "DD/MM/YYYY",
                                      true
                                  ); // Parse due date
                                  const projectStart = moment(
                                      state.start_date,
                                      "DD/MM/YYYY",
                                      true
                                  ); // Project start date
                                  const projectDue = moment(
                                      state.due_date,
                                      "DD/MM/YYYY",
                                      true
                                  );
                                  return (
                                      inputDate.isValid() &&
                                      projectStart.isValid() &&
                                      projectDue.isValid() &&
                                      inputDate.isBetween(
                                          projectStart,
                                          projectDue,
                                          "day",
                                          "[]"
                                      ) // Inclusive range
                                  );
                              }
                          )
                        : {},
                afterStartDate: stage.start_date
                    ? helpers.withMessage(
                          "Due Date cannot be before the Start Date.",
                          (value) => {
                              const start_date = moment(
                                  stage.start_date,
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
        }));
    }

    Object.assign(baseRules, {
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
    });

    return baseRules;
});

const v$ = useVuelidate(rules, state);
const isLoading = ref(false);
const handleSubmit = async (event) => {
    state.status = "active";
    v$.value.$touch();

    if (v$.value.$pending) return;
    if (v$.value.$invalid) {
        // Check fields for tab 1
        if (
            v$.value.name?.$invalid ||
            v$.value.description?.$invalid ||
            v$.value.start_date?.$invalid ||
            v$.value.due_date?.$invalid ||
            v$.value.measurement_unit?.$invalid ||
            v$.value.type?.$invalid
        ) {
            selectedTab.value = 1;
        } else if (
            state.stages.some(
                (stage, index) =>
                    v$.value.stages[index].amount?.$invalid ||
                    v$.value.stages[index].start_date?.$invalid ||
                    v$.value.stages[index].due_date?.$invalid
            )
        ) {
            selectedTab.value = 2;
        }
        return false;
    }
    await saveProject();
};
const saveProject = async () => {
    isLoading.value = true;

    const data = {
        ...state,
        stages: state.stages.map((stage) => ({
            stage_name: stage.stage_name,
            amount: stage.amount,
            start_date: stage.start_date,
            due_date: stage.due_date,
        })),
    };

    if (state.attachment) {
        data.attachments = [data.attachment?.uuid] ?? [];
        data.attachment = null;
    }

    try {
        let response;
        if (mode.value === "create") {
            response = await axios.post("/projects", data);
        } else if (mode.value === "edit") {
            response = await axios.put(`/projects/${state.projectUuid}`, data);
        }

        if (response.data) {
            window.location.reload();
        }
    } catch (error) {
        console.error("Error creating project:", error);
    } finally {
        isLoading.value = false;
    }
};

const handleAddStage = (stage_name) => {
    const newId = Date.now();
    state.stages.push({ id: newId, amount: 0, stage_name });
};

const handleAddTag = (tag_name) => {
    const newId = Date.now();
    state.tags.push({ id: newId, tag_name });
};

const handleRemoveStage = (item) => {
    console.log("Removing item:", item);
    // Ensure item has an id
    if (item && item.id) {
        state.stages = state.stages.filter((stage) => stage.id !== item.id);
    } else {
        console.error("Unexpected item format:", item);
    }
};

const projectTypeRef = ref(null);
const measurementRef = ref(null);
const currencyRef = ref(null);

const initializeNiceSelect = () => {
    // Initialize NiceSelect
    // state.type =
    //     state.type || $(projectTypeRef.value).find("option").first().val();
    // $(projectTypeRef.value).val(state.type).niceSelect("update");

    $(projectTypeRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
    $(projectTypeRef.value).niceSelect();
    $(projectTypeRef.value).on("change", function () {
        state.type = $(this).val();
    });
    $(measurementRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
    $(measurementRef.value).niceSelect();
    $(measurementRef.value).on("change", function () {
        state.measurement_unit = $(this).val();
    });
};

const selectValuesToSelect = () => {
    projectTypeRef.value = "Residential";
    $(projectTypeRef).val(projectTypeRef.value);
    state.type = projectTypeRef.value;

    measurementRef.value = "Sq meter";
    $(measurementRef).val(measurementRef.value);
    state.measurement_unit = measurementRef.value;
};

onMounted(() => {
    state.currency_id =
        props.currencies.find((currency) => currency.symbol === "£")?.id ||
        props.currencies[0]?.id;
    initializeNiceSelect();
    selectValuesToSelect();
    const createProjectModal = document.getElementById("create-new-project");
    createProjectModal.addEventListener("hide.bs.modal", () => {
        resetState();
    });
});

const TabTwoDateConfig = computed(() => {
    return {
        dateFormat: "d/m/Y",
        minDate: state.start_date,
        maxDate: state.due_date,
    };
});
const addStage = (stageName) => {
    if (stageName && !state.stages.some((stage) => stage.name === stageName)) {
        state.stages.push({
            name: stageName,
            currency: "$",
            amount: 0,
            start_date: "",
            due_date: "",
        });
    }
};

watch(
    () => state.name,
    (newName) => {
        const stagePattern = /(Stage\d+)/gi;
        const matches = newName.match(stagePattern);
        if (matches) {
            matches.forEach((stageName) => {
                addStage(stageName);
            });
        }
    }
);

const reinitializeNiceSelect = async () => {
    await nextTick();
    initializeNiceSelect();
};
watch(selectedTab, () => {
    reinitializeNiceSelect();
});

const resetState = () => {
    state.projectUuid = "";
    state.name = "";
    state.address = "";
    state.currency_id = "";
    state.measurement_unit = "";
    state.member_ids = [];
    state.tags = [];
    state.type = "";
    state.description = "";
    state.stages = [];
    state.start_date = "";
    state.due_date = "";
    state.attachment = null;
    state.is_procurement_enable = false;
    state.status = "";

    v$.value.$reset();
};

watch(
    () => project.value,
    (newVal) => {
        if (mode.value == "create") {
            resetState();
            state.currency_id =
                props.currencies.find((currency) => currency.symbol === "£")
                    ?.id || props.currencies[0]?.id;
            initializeNiceSelect();
            selectValuesToSelect();
        } else {
            state.projectUuid = newVal.uuid;
            state.name = newVal.name;
            state.address = newVal.address;
            state.currency_id = newVal.currency_id;
            state.measurement_unit = newVal.measurement_unit;
            state.member_ids = newVal.members ?? [];
            state.tags = newVal.tags;
            state.description = newVal.description;
            (state.stages = newVal.stages.map((stage) => ({
                stage_name: stage.stage_name,
                amount: stage.amount,
                start_date: computeTimezoneDate(stage.start_date),
                due_date: computeTimezoneDate(stage.due_date),
            }))),
                (state.start_date = computeTimezoneDate(newVal?.start_date));
            state.due_date = computeTimezoneDate(newVal?.due_date);
            state.attachment = newVal.attachment;
            state.is_procurement_enable = newVal.is_procurement_enable;
            state.status = "active";
            initializeNiceSelect();
            selectValuesToSelect();
        }
    },
    { deep: true }
);
const StartDateConfig = computed(() => {
    return {
        dateFormat: "d/m/Y",
    };
});

const DueDateConfig = computed(() => {
    return {
        dateFormat: "d/m/Y",
        minDate: state.start_date || moment().format("DD/MM/YYYY"), // Ensure due date is not before start date
    };
});
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
    /* background-color: #94AEF2DE; */
    text-transform: uppercase;
}
.log-in-draft {
    height: 44px;
    max-width: 400px;
    width: 100%;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1a1b1f;
    font-size: 18px;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: opacity 0.5s;
    font-size: 18px;
    font-family: "darker-grotesque-bold";
    margin-bottom: 10px;
}
</style>
