<template>
    <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="addSceduleCanvas"
        aria-labelledby="offcanvasRightLabel"
    >
        <div class="offcanvas-body add-material-ofcanvas-body">
            <div class="">
                <vue-loader v-if="isLoading" />
                <div class="row">
                    <div class="col-12">
                        <file-uploader
                            @handleAttachmentUpload="handleImageUpload"
                            :attachments="state.cover_image"
                            :sub-path="`projects/${route.params.project}/schedules`"
                            :upload_url="'/file/upload'"
                            :delete_url="'/file/delete'"
                            :acceptedFileTypes="'image/png, image/jpeg, image/gif'"
                            :disabled="mode === 'add-to'"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <label class="signup-labels">item name</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.item_name"
                                :disabled="mode === 'add-to'"
                            />
                            <input-validation-error-message
                                :v="v$?.item_name"
                            />
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="">
                            <label class="signup-labels"
                                >item description</label
                            >
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.description"
                                :disabled="mode === 'add-to'"
                            />
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="">
                            <label class="signup-labels">brand name</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.brand_name"
                                :disabled="mode === 'add-to'"
                            />
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="">
                            <label class="signup-labels">sku</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.sku"
                                :disabled="mode === 'add-to'"
                            />
                            <input-validation-error-message :v="v$?.sku" />
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="">
                            <label class="signup-labels">product url</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.product_url"
                                :disabled="mode === 'add-to'"
                            />
                        </div>
                    </div>

                    <div
                        class="col-6"
                        v-if="suppliers.length && !isNewSupplierEnable"
                    >
                        <div class="mb-2">
                            <label class="signup-labels">supplier</label>
                            <div
                                class="create-new-project-select-container supplier-select"
                            >
                                <select
                                    class="select"
                                    v-model="state.supplier_library_id"
                                    ref="supplierRef"
                                    :disabled="mode === 'add-to'"
                                >
                                    <option
                                        v-for="supplier in suppliers"
                                        :key="supplier.uuid"
                                        :value="supplier.id"
                                    >
                                        {{ supplier.name }}
                                    </option>
                                </select>
                                <input-validation-error-message
                                    :v="v$?.supplier_library_id"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="col-6" v-if="!isNewSupplierEnable">
                        <label class="signup-labels">category</label>
                        <div
                            v-if="isSupplierCategoriesSelectEnable"
                            class="create-new-project-select-container"
                        >
                            <select
                                class="select"
                                v-model="state.category_id"
                                ref="categoryRef"
                                :disabled="mode === 'add-to'"
                            >
                                <option
                                    v-for="category in categories"
                                    :key="category.uuid"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <input-validation-error-message
                                :v="v$?.category_id"
                            />
                        </div>
                    </div>
                    <div
                        class="col-6 d-flex justify-content-end m-0"
                        v-if="!isNewSupplierEnable"
                    >
                        <span
                            class="create-one d-block"
                            style="font-family: inter-Regular; font-size: 14px"
                            @click="handleAddNewSuppplierClick(true)"
                            :disabled="mode === 'add-to'"
                        >
                            Add Custom Supplier
                        </span>
                    </div>
                </div>
                <div
                    class="row"
                    v-if="!suppliers.length || isNewSupplierEnable"
                >
                    <div class="col-6 pb-3">
                        <h6 class="offcanvas-heading">Supplier Information</h6>
                    </div>
                    <div
                        class="col-6 d-flex align-items-center justify-content-end pb-3"
                    >
                        <div class="collaps-container" v-if="suppliers.length">
                            <button
                                class="collaps-expand-btn"
                                @click="handleAddNewSuppplierClick(false)"
                            >
                                Cancel
                            </button>
                            <img
                                class="collaps-icon"
                                src="{{ asset('assets/images/arrow-icon.svg') }}"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">Company Name</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.supplier.company_name"
                                :disabled="mode === 'add-to'"
                                @blur="isNewSupplierEnable && v$.$touch()"
                            />
                            <input-validation-error-message
                                v-if="isNewSupplierEnable"
                                :v="v$.supplier.company_name"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">Contact Name</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.supplier.name"
                                :disabled="mode === 'add-to'"
                                @blur="isNewSupplierEnable && v$.$touch()"
                            />
                            <input-validation-error-message
                                v-if="isNewSupplierEnable"
                                :v="v$.supplier?.name"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">Email Address</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.supplier.email"
                                :disabled="mode === 'add-to'"
                                @blur="isNewSupplierEnable && v$.$touch()"
                            />
                            <input-validation-error-message
                                v-if="isNewSupplierEnable"
                                :v="v$?.supplier?.email"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">Phone Number</label>
                            <vue-tel-input
                                @on-input="(phoneString, obj) => (telObj = obj)"
                                :validCharactersOnly="true"
                                class="signup-inputs mw-100"
                                id="phone_number"
                                name="phone_number"
                                v-model="state.supplier.phone_number"
                                @keydown.enter.prevent
                                :disabled="mode === 'add-to'"
                                @blur="isNewSupplierEnable && v$.$touch()"
                            />
                            <input-validation-error-message
                                v-if="isNewSupplierEnable"
                                :v="v$?.supplier?.phone_number"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">Address</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.supplier.address"
                                :disabled="mode === 'add-to'"
                                @blur="isNewSupplierEnable && v$.$touch()"
                            />
                            <input-validation-error-message
                                v-if="isNewSupplierEnable"
                                :v="v$?.supplier?.address"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="create-new-project-select-container">
                            <label class="signup-labels">category</label>

                            <select
                                class="select"
                                v-model="state.supplier.category"
                                ref="supplierCategoryRef"
                                :disabled="mode === 'add-to'"
                            >
                                <option
                                    v-for="category in categories"
                                    :key="category.uuid"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <input-validation-error-message
                                v-if="
                                    isNewSupplierEnable &&
                                    v$.supplier?.category.$invalid &&
                                    v$.supplier?.category.$dirty
                                "
                                :v="v$?.supplier?.category"
                            />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h5 class="offcanvas-heading">Product Specification</h5>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">height</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.height"
                                :disabled="mode === 'add-to'"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">depth</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.depth"
                                :disabled="mode === 'add-to'"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">width</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.width"
                                :disabled="mode === 'add-to'"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">length</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.length"
                                :disabled="mode === 'add-to'"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">color</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.color"
                                :disabled="mode === 'add-to'"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">finish</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.finish"
                                :disabled="mode === 'add-to'"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="">
                            <label class="signup-labels">Price</label>
                            <input
                                class="signup-inputs mw-100"
                                placeholder=""
                                v-model="state.price"
                                :disabled="mode === 'add-to'"
                            />
                        </div>
                    </div>
                </div>
                <div
                    class="row"
                    v-if="module === 'schedule' || mode === 'add-to'"
                >
                    <div class="col-12">
                        <h5 class="offcanvas-heading">Area</h5>
                    </div>
                    <div
                        class="row"
                        v-if="computedProjectAreas.length > 0"
                        v-for="(projectArea, index) in state.project_areas"
                        :key="projectArea.uuid"
                    >
                        <div class="col-4">
                            <div
                                class="create-new-project-select-container area-select"
                            >
                                <label class="signup-labels">area</label>
                                <select
                                    class="select project-area-select"
                                    v-model="projectArea.project_area_id"
                                    :data-project_area_uuid="projectArea.uuid"
                                    :data-index="index"
                                >
                                    <option
                                        v-for="area in getAvailableProjectAreas(
                                            index
                                        )"
                                        :key="area.uuid"
                                        :value="area.id"
                                    >
                                        {{ area.area_name }}
                                    </option>
                                </select>
                            </div>
                            <input-validation-error-message
                                :v="v$.project_areas[index]?.project_area_id"
                            />
                        </div>
                        <div class="col-7">
                            <div class="">
                                <label class="signup-labels">quantity</label>
                                <input
                                    min="1"
                                    type="number"
                                    class="signup-inputs mw-100"
                                    placeholder="Enter Quantity"
                                    v-model="projectArea.quantity"
                                />
                            </div>
                            <input-validation-error-message
                                :v="v$.project_areas[index]?.quantity"
                            />
                        </div>
                        <div class="col-1 d-flex align-items-center">
                            <button
                                class="border-0 bg-white mt-4"
                                type="button"
                                @click="
                                    handleRemoveProjectAreaClick(
                                        projectArea.uuid
                                    )
                                "
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="#ACACAC"
                                    class="bi bi-trash text-white"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"
                                    />
                                    <path
                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div
                        class="col-12"
                        v-if="
                            computedProjectAreas.length !==
                            state.project_areas.length
                        "
                    >
                        <a
                            class="create-one d-block"
                            @click="handleAddProjectArea"
                        >
                            Add Another Area
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h5 class="offcanvas-heading">Custom Specification</h5>
                    </div>
                    <div
                        class="row d-flex align-items-center mt-3"
                        v-for="customSpecification in state.custom_specifications"
                        :key="customSpecification.uuid"
                    >
                        <div class="col-4">
                            <div class="">
                                <label class="signup-labels">label</label>
                                <input
                                    class="signup-inputs mw-100"
                                    placeholder=""
                                    v-model="customSpecification.label"
                                    :disabled="mode === 'add-to'"
                                />
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="">
                                <label class="signup-labels">description</label>
                                <input
                                    class="signup-inputs mw-100"
                                    placeholder=""
                                    v-model="
                                        customSpecification.custom_description
                                    "
                                    :disabled="mode === 'add-to'"
                                />
                            </div>
                        </div>
                        <div class="col-1">
                            <button
                                class="border-0 bg-white mt-4"
                                @click="
                                    handleRemoveCustomSpecificationClick(
                                        customSpecification.uuid
                                    )
                                "
                                type="button"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="#ACACAC"
                                    class="bi bi-trash text-white"
                                    style="filter: invert(1)"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"
                                    />
                                    <path
                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="col-12">
                        <a
                            class="create-one d-block"
                            @click="handleAddCustomSpecification"
                            :disabled="mode === 'add-to'"
                        >
                            Add New Specification
                        </a>
                    </div>
                    <div class="col-12"></div>
                    <div class="col-12">
                        <file-uploader
                            @handleAttachmentUpload="handleAttachmentUpload"
                            :attachments="state.attachments"
                            :sub-path="`projects/${route.params.project}/documents`"
                            :upload_url="'/file/upload'"
                            :delete_url="'/file/delete'"
                            :acceptedFileTypes="'.dwg, .pdf, .doc, .docx, .csv, .txt, .zip, .xml, image/png, image/jpeg, image/gif'"
                            multiple
                            :disabled="mode === 'add-to'"
                        />
                    </div>
                    <div class="col-12">
                        <button
                            style="max-width: 400px; margin: 0 auto"
                            class="log-in"
                            type="button"
                            @click="handleSubmit"
                            :disabled="isLoading"
                        >
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, onMounted, ref, computed, watch, nextTick } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import { v4 as uuidv4 } from "uuid";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers, maxLength, email } from "@vuelidate/validators";
import FileUploader from "../Common/FileUploader.vue";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { useScheduleStore } from "../../stores/scheduleStore";
import { storeToRefs } from "pinia";
const ScheduleStore = useScheduleStore();
const { schedule, mode, module } = storeToRefs(ScheduleStore);
import "vue-tel-input/vue-tel-input.css";
import VueLoader from "../Common/Loader.vue";
import { useToast } from "vue-toastification";

const route = useRoute();
const toast = useToast();

$("select").niceSelect();
const props = defineProps({
    suppliers: {
        type: Array,
        default: [],
    },
    project_areas: {
        type: Array,
        default: [],
    },
    categories: {
        type: Array,
        default: [],
    },
});

const state = reactive({
    cover_image: [],
    category_id: "",
    supplier_library_id: "",
    item_name: "",
    description: "",
    sku: "",
    brand_name: "",
    product_url: "",
    height: "",
    depth: "",
    width: "",
    length: "",
    color: "",
    finish: "",
    price: "",
    custom_specifications: [],
    supplier: {
        company_name: "",
        name: "",
        email: "",
        phone_number: "",
        address: "",
        category: "",
    },

    attachments: [],
    project_areas: [],
});
const isNewSupplierEnable = ref(false);
const isLoading = ref(false);
const rules = computed(() => ({
    item_name: {
        required: helpers.withMessage("Item name is required.", required),
    },
    supplier: {
        company_name: isNewSupplierEnable.value
            ? {
                  required: helpers.withMessage(
                      "Company name is required.",
                      required
                  ),
                  maxLength: maxLength(255),
              }
            : {},
        name: isNewSupplierEnable.value
            ? {
                  required: helpers.withMessage(
                      "Contact name is required.",
                      required
                  ),
                  maxLength: maxLength(255),
              }
            : {},
        email: isNewSupplierEnable.value
            ? {
                  required: helpers.withMessage("Email is required.", required),
                  email: helpers.withMessage("Invalid email format.", email),
                  maxLength: maxLength(255),
              }
            : {},
        phone_number: isNewSupplierEnable.value
            ? {
                  required: helpers.withMessage(
                      "Phone number is required.",
                      required
                  ),
                  maxLength: maxLength(15),
                  handlePhoneNumberValidation: helpers.withMessage(
                      "Invalid phone number format.",
                      (value) => {
                          return telObj.value?.valid || !value;
                      }
                  ),
              }
            : {},
        address: isNewSupplierEnable.value
            ? {
                  required: helpers.withMessage(
                      "Address is required.",
                      required
                  ),
                  maxLength: maxLength(255),
              }
            : {},
        category: isNewSupplierEnable.value
            ? {
                  required: helpers.withMessage(
                      "Category is required.",
                      required
                  ),
              }
            : {},
    },

    category_id: isNewSupplierEnable.value
        ? {}
        : {
              required: helpers.withMessage("Category is required.", required),
          },
    supplier_library_id: isNewSupplierEnable.value
        ? {}
        : {
              required: helpers.withMessage("Supplier is required.", required),
          },
    sku: {
        required: helpers.withMessage("Sku is required.", required),
    },
    project_areas:
        module.value === "schedule" ||
        mode.value === "add-to" ||
        (module.value !== "material-library" && mode.value === "edit")
            ? state.project_areas.map((area, index) => ({
                  project_area_id: {
                      required: helpers.withMessage(
                          "Area name is required.",
                          required
                      ),
                      custom: helpers.withMessage(
                          "This area has already been selected or is invalid.",
                          (value) => {
                              const numericValue = Number(value);
                              if (isNaN(numericValue)) {
                                  return false;
                              }
                              return !state.project_areas.some(
                                  (selectedArea, selectedIndex) =>
                                      selectedIndex !== index &&
                                      selectedArea.project_area_id ===
                                          numericValue
                              );
                          }
                      ),
                  },
                  quantity: {
                      required: helpers.withMessage(
                          "Quantity is required.",
                          required
                      ),
                      minValue: helpers.withMessage(
                          "Quantity must be greater than 0.",
                          (value) => value > 0
                      ),
                  },
              }))
            : [],
}));

const telObj = ref(null);
const v$ = useVuelidate(rules, state);

const customSpecificationObject = ref({
    label: "",
    custom_description: "",
});

const projectAreaObject = ref({
    project_area_id: "",
    quantity: "",
});

const isSupplierCategoriesSelectEnable = ref(true);
const supplierRef = ref(null);
const supplierCategoryRef = ref(null);
const categoryRef = ref(null);

onMounted(() => {
    if (!props.suppliers.length) {
        isNewSupplierEnable.value = true;
    } else {
        isNewSupplierEnable.value = false;
    }
    nextTick(() => initializeNiceSelect());

    handleAddCustomSpecification();
});

const initializeNiceSelect = () => {
    $(supplierRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
    $(supplierRef.value).niceSelect();

    $(supplierCategoryRef.value).niceSelect("destroy");
    $(supplierCategoryRef.value).niceSelect();

    $(categoryRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
    $(categoryRef.value).niceSelect();
    // Initialize NiceSelect
    $(supplierRef.value).on("change", function () {
        state.supplier_library_id = parseInt($(this).val());
    });
    $(categoryRef.value).on("change", function () {
        state.category_id = parseInt($(this).val());
    });
    if (state.supplier_library_id) {
        $(supplierRef.value)
            .val(state.supplier_library_id)
            .niceSelect("update");
    }
    if (state.category_id) {
        $(categoryRef.value).val(state.category_id).niceSelect("update");
    }
    $(supplierCategoryRef.value).on("change", function () {
        state.supplier.category = parseInt($(this).val());
    });

    $(".select").niceSelect("destroy");
    $(".select").niceSelect();

    $(".project-area-select").niceSelect("destroy");
    $(".project-area-select").niceSelect();
    $(".project-area-select")
        .off("change")
        .on("change", function () {
            const index = $(this).data("index");
            const value = $(this).val();

            // Update Vue's reactive state
            state.project_areas[index].project_area_id = parseInt(value, 10);
        });
};

const handleAddCustomSpecification = () => {
    if (mode.value === "add-to") return;
    state.custom_specifications = [
        ...state.custom_specifications,
        Object.assign(
            {},
            { ...customSpecificationObject.value, uuid: uuidv4() }
        ),
    ];
};

const handleRemoveCustomSpecificationClick = (uuid) => {
    if (mode.value === "add-to") return;
    state.custom_specifications = state.custom_specifications.filter(
        (customSpecification) => customSpecification.uuid !== uuid
    );
};
const canAddAnotherArea = computed(() => {
    // Ensure every project_area_id is non-empty
    return state.project_areas.every(
        (projectArea) =>
            projectArea.project_area_id && projectArea.project_area_id !== ""
    );
});

const handleAddProjectArea = async () => {
    if (!canAddAnotherArea.value) {
        toast.warning(
            "Please select an area for all existing rows before adding a new one.",
            {
                timeout: 3000,
                position: "top-right",
            }
        );
        return;
    }
    state.project_areas = [
        ...state.project_areas,
        Object.assign({}, { ...projectAreaObject.value, uuid: uuidv4() }),
    ];
    await nextTick();
    initializeNiceSelect();
};

const handleRemoveProjectAreaClick = (uuid) => {
    state.project_areas = state.project_areas.filter(
        (projectArea) => projectArea.uuid !== uuid
    );
};

const handleAddNewSuppplierClick = (value) => {
    if (mode.value === "add-to") return;
    isNewSupplierEnable.value = value;
    isSupplierCategoriesSelectEnable.value = !value;
    state.supplier_library_id = "";
    state.category_id = "";
    state.supplier = {
        company_name: "",
        name: "",
        email: "",
        phone_number: "",
        address: "",
        category: "",
    };
    nextTick(() => initializeNiceSelect());
};

const handleSubmit = async () => {
    try {
        v$.value.$touch(); // Mark fields as touched to show errors
        if (v$.value.$pending) return; // Wait for any async validation

        if (v$.value.$invalid) {
            return false;
        }
        isLoading.value = true;
        let response;
        if (module.value === "schedule") {
            if (mode.value === "create") {
                response = await axios.post(
                    `/projects/${route.params.project}/schedules`,
                    { ...state }
                );
            } else if (mode.value === "edit") {
                response = await axios.put(
                    `/projects/${route.params.project}/schedules/${[
                        schedule.value.uuid,
                    ]}`,
                    { ...state, material_uuid: schedule?.value?.material?.uuid }
                );
            }
        } else if (module.value === "material-library") {
            if (mode.value === "create") {
                response = await axios.post(`/material-library`, {
                    ...state,
                });
            } else if (mode.value === "edit") {
                response = await axios.put(
                    `/material-library/${schedule?.value?.uuid}`,
                    {
                        ...state,
                    }
                );
            } else if (mode.value === "add-to") {
                const payload = {
                    material_library_id: schedule.value?.id,
                    project_areas: state.project_areas.map((area) => ({
                        project_area_id: area.project_area_id,
                        quantity: area.quantity,
                    })),
                };
                response = await axios.post(
                    `/projects/${schedule?.value?.project_uuid}/add-schedules-from-library`,
                    payload
                );
            }
        }
        if (response.data) {
            isLoading.value = false;
            window.location.reload();
        }
    } catch (error) {
        isLoading.value = false;
        console.error("Error creating project:", error);
    }
};

watch(
    () => schedule.value,
    (newVal) => {
        if (!props.suppliers.length) {
            isNewSupplierEnable.value = true;
        } else {
            isNewSupplierEnable.value = false;
        }
        nextTick(() => initializeNiceSelect());
        if (mode.value == "create") {
            resetState();
            if (!state.project_areas.length && module.value === "schedule") {
                state.project_areas.push({
                    ...projectAreaObject.value,
                    uuid: uuidv4(),
                });
            }
        } else {
            resetState();
            v$.value.$reset();
            const material =
                module.value === "schedule" ? newVal?.material : newVal;
            state.cover_image = material?.cover_image;
            state.item_name = material?.item_name;
            state.description = material?.description;
            state.sku = material?.sku;
            state.brand_name = material?.brand_name;
            state.product_url = material?.product_url;
            state.height = material?.height;
            state.depth = material?.depth;
            state.width = material?.width;
            state.length = material?.length;
            state.color = material?.color;
            state.finish = material?.finish;
            state.price = material?.price;
            state.supplier_library_id = parseInt(material?.supplier?.id);
            state.category_id = parseInt(material?.category_id);
            state.custom_specifications = material?.specifications;
            state.attachments = material?.attachments;
            if (module.value === "schedule") {
                state.quantity = newVal?.quantity;
                state.project_areas =
                    newVal?.areas?.map((area) => ({
                        project_area_id: area?.pivot?.project_area_id,
                        quantity: area?.pivot?.quantity,
                    })) || [];
            }
            if (mode.value === "add-to") {
                handleAddProjectArea();
            }
            nextTick(() => initializeNiceSelect());
        }
    },
    { deep: true }
);

const resetState = () => {
    state.cover_image = [];
    state.category_id = "";
    state.supplier_library_id = "";
    state.project_areas = [];
    state.item_name = "";
    state.description = "";
    state.sku = "";
    state.item_name = "";
    state.product_url = "";
    state.height = "";
    state.depth = "";
    state.width = "";
    state.length = "";
    state.color = "";
    state.finish = "";
    state.price = "";
    state.custom_specifications = [];
    state.supplier = Object.assign(
        {},
        {
            company_name: "",
            name: "",
            email: "",
            phone_number: "",
            address: "",
            category: "",
        }
    );
    state.attachments = [];
    v$.value.$reset();
};

const handleAttachmentUpload = (ids) => {
    state.attachments = [...new Set(ids.filter((x) => !!x))];
};

const handleImageUpload = (ids) => {
    state.cover_image = ids;
};
const getAvailableProjectAreas = (currentIndex) => {
    // Get all selected category IDs except for the current item
    const selectedProjectedArea = state.project_areas
        .filter((_, index) => index !== currentIndex)
        .map((area) => area.project_area_id);

    // Return categories that are not selected
    return computedProjectAreas.value.filter(
        (area) => !selectedProjectedArea.includes(area.id)
    );
};
const computedProjectAreas = computed(() =>
    module.value === "material-library" && mode.value === "add-to"
        ? schedule?.value?.areas
        : props.project_areas
);
</script>
<style scoped lang="scss">
[class^="col-"] {
    margin: 8px 0;
}
</style>
