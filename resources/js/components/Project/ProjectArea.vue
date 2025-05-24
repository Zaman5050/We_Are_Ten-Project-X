<template>
    <div>
        <form @submit="handleSubmit">
            <div
                class="row"
                v-for="(area, index) in state.areas"
                :key="area.uuid"
            >
                <div class="col-md-3 col-4">
                    <div class="sign-up-input-container">
                        <label class="signup-labels">AREA NAME </label>
                        <input
                            class="signup-inputs mw-100"
                            placeholder="Enter Area Name"
                            v-model="area.area_name"
                        />
                        <input-validation-error-message
                            :v="v$.areas[index].area_name"
                        />
                    </div>
                </div>
                <div class="col-md-3 col-4">
                    <div class="sign-up-input-container">
                        <label class="signup-labels">AREA DIMENTIONS</label>

                        <div class="input-group mb-3">
                            <input
                                type="text"
                                class="form-control signup-inputs mw-100"
                                placeholder="Enter DIMENTIONS"
                                aria-label="Recipient's username"
                                aria-describedby="basic-addon2"
                                v-model="area.area_dimention"
                            />
                            <div class="input-group-append">
                                <span
                                    class="input-group-text signup-inputs"
                                    id="basic-addon2"
                                    >{{ measurementUnit }}
                                </span>
                            </div>
                            <input-validation-error-message
                                :v="v$.areas[index].area_dimention"
                            />
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-4">
                    <div class="sign-up-input-container">
                        <label class="signup-labels">AREA CODE</label>
                        <input
                            class="signup-inputs mw-100"
                            placeholder="Enter Area Code"
                            v-model="area.area_code"
                            @input="v$.$clearExternalResults()"
                        />
                        <input-validation-error-message
                            :v="v$.areas[index].area_code"
                        />
                    </div>
                </div>
                <div
                    class="col-md-3 col-4 d-flex justify-content-start align-items-center"
                >
                    <button
                        v-if="state.areas.length > 1"
                        @click="setDeletedUuid(area.uuid)"
                        class="p-1 bg-white border-0"
                        data-bs-toggle="modal"
                        data-bs-target="#delete-area"
                        type="button"
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
            <div class="row">
                <div class="col-2">
                    <a
                        class="create-one d-block"
                        @click="handleAddProjectAreaClick"
                    >
                        Add Another Area
                    </a>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-6">
                    <button
                        class="log-in mw-100"
                        type="submit"
                        :disabled="isLoading"
                    >
                        Save
                    </button>
                </div>
            </div>
            <div
                class="modal fade"
                id="delete-area"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
                tabindex="-1"
                aria-labelledby="staticBackdropLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <!-- <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5> -->
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body pt-0">
                            <p class="delete-warning">
                                ARE YOU SURE YOU WANT TO DELETE THIS AREA ?
                            </p>
                        </div>
                        <div class="modal-footer border-0 pt-0 flex-nowrap">
                            <button
                                style="
                                    width: 50%;
                                    background: #fff;
                                    color: #000;
                                "
                                type="button"
                                class="apply-leave-btn"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            >
                                No
                            </button>
                            <button
                                style="width: 50%"
                                type="button"
                                @click="
                                    handleRemoveProjectAreaClick(deletedUuid)
                                "
                                class="apply-leave-btn"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            >
                                Yes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, onMounted, ref, computed } from "vue";
import axios from "axios";
import { v4 as uuidv4 } from "uuid";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers, maxLength, email } from "@vuelidate/validators";
import { useToast } from "vue-toastification";

const props = defineProps({
    projectAreas: {
        type: Array,
        default: [],
    },
    measurementUnit: {
        type: String,
        default: "sq/m",
    },
    projectUuid: {
        type: String,
        default: null,
    },
});

const state = reactive({
    areas: [],
    areaObject: {
        area_name: "",
        area_dimention: "",
        area_code: "",
    },
});

const toast = useToast();
const isLoading = ref(false);
const deletedUuid = ref(null);

const setDeletedUuid = (uuid) => {
    deletedUuid.value = uuid;
};
const serverSideErrors = reactive({});
const handleSubmit = async (event) => {
    event.preventDefault();
    v$.value.$clearExternalResults();
    v$.value.$touch();
    if (v$.value.$pending) return;

    if (v$.value.$invalid) {
        return false;
    }
    isLoading.value = true;

    try {
        const response = await axios.post(
            `/${props.projectUuid}/project-areas`,
            { areas: state.areas }
        );
        if (response.data) {
            isLoading.value = false;
            toast.success(response?.data?.message || "Success!", {
                timeout: 3000,
                position: "top-right",
            });
        }
    } catch (error) {
        isLoading.value = false;
        if (error.response.status === 500) {
            toast.error(error?.response?.data?.message || "Server Error!", {
                timeout: 3000,
                position: "top-right",
            });
        } else if (error.response.status === 422) {
            if (error.response?.data?.errors) {
                syncErrorsWithVuelidate(error.response?.data?.errors);
            } else {
                console.error("Unexpected error:", error);
            }
        }
    }
};

const handleAddProjectAreaClick = () => {
    addProjectArea();
};

const handleRemoveProjectAreaClick = async (areaUuid) => {
    if (state.areas[0].id === undefined && areaUuid) {
        state.areas = state.areas.filter((area) => area.uuid !== areaUuid);
        return false;
    }

    try {
        isLoading.value = true;
        const response = await axios.delete(
            `/${props.projectUuid}/project-areas/${areaUuid}`
        );
        if (response.data) {
            state.areas = state.areas.filter((area) => area.uuid !== areaUuid);
            if (state.areas.length === 0) {
                addProjectArea();
            }
        }
    } catch (error) {
        isLoading.value = false;
        console.error("Error creating project:", error);
    } finally {
        isLoading.value = false;
        deletedUuid.value = null;

        const areaDeleteModel = document.getElementById("delete-area");
        const bsModal = bootstrap.Modal.getInstance(areaDeleteModel); // Get modal instance

        if (bsModal) {
            bsModal.hide(); // Hide the modal
        }
    }
};

const addProjectArea = () => {
    state.areas = [
        ...state.areas,
        Object.assign({}, { ...state.areaObject, uuid: uuidv4() }),
    ];
};

onMounted(() => {
    state.areas = [...props?.projectAreas];
    if (state.areas.length === 0) {
        addProjectArea();
    }
});
const rules = computed(() => {
    return {
        areas: state.areas.map((area) => ({
            area_name: {
                required: helpers.withMessage(
                    "Area name is required.",
                    required
                ),
            },
            area_dimention: {
                required: helpers.withMessage(
                    "Area dimension is required.",
                    required
                ),
            },
            area_code: {
                required: helpers.withMessage(
                    "Area code is required.",
                    required
                ),
            },
        })),
    };
});

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
</script>
