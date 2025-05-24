<template>
    <form @submit.prevent="handleSubmit">
        <div class="">
            <label class="signup-labels table-date-heading"
                >Stages Budget ({{ project.stages.length }})</label
            >
            <div class="row">
                <div class="col-md-8">
                    <div
                        class="col-md-12"
                        v-for="(stage, index) in state.stages"
                        :key="stage.id"
                    >
                        <div class="row">
                            <div class="col-2">
                                <p
                                    lang="de"
                                    class="signup-labels mb-0 mt-33 darker-grotesque-bold fs-18 text-capitalize project-type-budget break-word"
                                >
                                    {{ stage.stage_name }}
                                </p>
                            </div>
                            <div class="col-10">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div
                                                    class="sign-up-input-container"
                                                >
                                                    <label
                                                        class="signup-labels darker-grotesque-bold fs-18 text-capitalize"
                                                        >Amount</label
                                                    >
                                                    <div class="input-group">
                                                        <div
                                                            class="currency-select w-60"
                                                        >
                                                            <div
                                                                class="signup-inputs d-flex align-items-center justify-content-center"
                                                            >
                                                                {{
                                                                    state.currency
                                                                }}
                                                            </div>
                                                        </div>
                                                        <input
                                                            type="number"
                                                            v-model="
                                                                stage.amount
                                                            "
                                                            class="form-control signup-inputs mw-100"
                                                            placeholder="0"
                                                            aria-label="Trade discount0"
                                                            aria-describedby="basic-addon2"
                                                            step="any"
                                                        />
                                                    </div>
                                                    <input-validation-error-message
                                                        :v="
                                                            v$.stages[index]
                                                                .amount
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div
                                                    class="sign-up-input-container"
                                                >
                                                    <label
                                                        class="signup-labels darker-grotesque-bold fs-18 text-capitalize"
                                                        >Start Date</label
                                                    >
                                                    <flat-pickr
                                                        v-model="
                                                            stage.start_date
                                                        "
                                                        :config="DateConfig"
                                                        placeholder="DD/MM/YYYY"
                                                        :class="[
                                                            'signup-inputs flatpickr-input mw-100',
                                                        ]"
                                                    />
                                                    <input-validation-error-message
                                                        :v="
                                                            v$.stages[index]
                                                                .start_date
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="">
                                                    <label
                                                        class="signup-labels darker-grotesque-bold fs-18 text-capitalize"
                                                        >Due Date</label
                                                    >
                                                    <flat-pickr
                                                        v-model="stage.due_date"
                                                        :config="DateConfig"
                                                        placeholder="DD/MM/YYYY"
                                                        :class="[
                                                            'signup-inputs flatpickr-input mw-100',
                                                        ]"
                                                    />
                                                    <input-validation-error-message
                                                        :v="
                                                            v$.stages[index]
                                                                .due_date
                                                        "
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-2">
                            <p
                                class="signup-labels mb-0 mt-33 darker-grotesque-bold fs-18 text-capitalize"
                            >
                                total
                            </p>
                        </div>
                        <div class="col-10">
                            <div class="sign-up-input-container">
                                <label
                                    class="signup-labels darker-grotesque-bold fs-18"
                                    >Amount</label
                                >
                                <div class="input-group">
                                    <div class="currency-select w-60">
                                        <div
                                            class="signup-inputs d-flex align-items-center justify-content-center"
                                        >
                                            {{ state.currency }}
                                        </div>
                                    </div>
                                    <input
                                        readonly
                                        type="text"
                                        :value="totalAmount"
                                        class="form-control signup-inputs mw-100"
                                        aria-label="Trade discount0"
                                        aria-describedby="basic-addon2"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="" v-if="props.project.is_procurement_enable">
            <label class="signup-labels table-date-heading"
                >Procurement Budget ({{
                    state.procurementBudget.length
                }})</label
            >
            <div class="row mb-4">
                <div class="col-md-8">
                    <div
                        class="row"
                        v-for="(item, index) in state.procurementBudget"
                        :key="index"
                    >
                        <div class="col-4">
                            <label
                                class="signup-labels darker-grotesque-bold fs-18"
                                >Category</label
                            >
                            <div
                                class="create-new-project-select-container select-full"
                            >
                                <select
                                    class="select categorySel"
                                    v-model="item.category_id"
                                    :data-index="index"
                                >
                                    <option
                                        v-for="category in getAvailableCategories(
                                            index
                                        )"
                                        :key="category.uuid"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <input-validation-error-message
                                :v="v$.procurementBudget[index].category_id"
                            />
                        </div>
                        <div class="col-2">
                            <label
                                class="signup-labels darker-grotesque-bold fs-18"
                                >Quantity</label
                            >
                            <input
                                type="number"
                                v-model="item.quantity"
                                class="form-control signup-inputs mw-100"
                                placeholder="Enter Quantity"
                            />
                            <input-validation-error-message
                                :v="v$.procurementBudget[index].quantity"
                            />
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="sign-up-input-container">
                                        <label
                                            class="signup-labels darker-grotesque-bold fs-18"
                                            >Amount</label
                                        >
                                        <div class="input-group">
                                            <div class="currency-select w-60">
                                                <div
                                                    class="signup-inputs d-flex align-items-center justify-content-center"
                                                >
                                                    {{ state.currency }}
                                                </div>
                                            </div>
                                            <input
                                                type="number"
                                                class="form-control signup-inputs mw-100"
                                                v-model="item.amount"
                                                placeholder="Enter Amount"
                                                aria-label="Trade discount0"
                                                aria-describedby="basic-addon2"
                                                step="any"
                                            />
                                        </div>
                                        <input-validation-error-message
                                            :v="
                                                v$.procurementBudget[index]
                                                    .amount
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <a
                                class="create-one d-block mt-4"
                                @click="addProcurementBudget"
                            >
                                Add Another Category
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-2">
                            <p
                                class="signup-labels mb-0 mt-33 darker-grotesque-bold fs-18 text-capitalize"
                            >
                                total
                            </p>
                        </div>
                        <div class="col-10">
                            <div class="sign-up-input-container">
                                <label
                                    class="signup-labels darker-grotesque-bold fs-18"
                                    >Amount</label
                                >
                                <div class="input-group">
                                    <div class="currency-select w-60">
                                        <div
                                            class="signup-inputs d-flex align-items-center justify-content-center"
                                        >
                                            {{ state.currency }}
                                        </div>
                                    </div>
                                    <input
                                        readonly
                                        type="text"
                                        :value="totalProcurementAmount"
                                        class="form-control signup-inputs mw-100"
                                        aria-label="Trade discount0"
                                        aria-describedby="basic-addon2"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="log-in" type="submit">Save</button>
    </form>
</template>

<script setup>
import { reactive, onMounted, ref, computed, nextTick, watch } from "vue";
import axios from "axios";
import flatPickr from "vue-flatpickr-component";
import { computeTimezoneDate } from "@/helpers.js";
import { v4 as uuidv4 } from "uuid";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers } from "@vuelidate/validators";
import { useToast } from "vue-toastification";

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: [],
    },
});
const categoryRef = ref(null);
const toast = useToast();

const state = reactive({
    stages: [],
    procurementBudget: [],
    currency: props.project.currency?.symbol || "$",
});

onMounted(() => {
    state.stages = props.project.stages.map((stage) => ({
        stage_name: stage.stage_name || null,
        amount: stage?.amount || null,
        start_date: stage.start_date
            ? computeTimezoneDate(stage?.start_date)
            : null,
        due_date: stage.due_date ? computeTimezoneDate(stage?.due_date) : null,
    }));
    if (props.project && props.project.procurement_budget) {
        state.procurementBudget = props.project.procurement_budget.map(
            (budget) => ({
                category_id: budget?.category_id || null,
                quantity: budget?.quantity || 0,
                amount: budget?.amount || null,
            })
        );
    }
    initializeNiceSelect();
});

const DateConfig = computed(() => {
    return {
        dateFormat: "d/m/Y",
        minDate: computeTimezoneDate(props.project.start_date),
        maxDate: computeTimezoneDate(props.project.due_date),
    };
});

const initializeNiceSelect = () => {
    nextTick(() => {
        $(".categorySel").niceSelect("destroy");
        $(".categorySel").niceSelect();

        // Bind the change event
        $(".categorySel")
            .off("change")
            .on("change", function () {
                const index = $(this).data("index");
                const value = $(this).val();

                // Update Vue's reactive state
                state.procurementBudget[index].category_id = parseInt(
                    value,
                    10
                );
            });
    });
};

const totalAmount = computed(() => {
    return state.stages.reduce(
        (sum, stage) => sum + (parseFloat(stage.amount) || 0),
        0
    );
});

const addProcurementBudget = async () => {
    if (!props.categories.length) {
        toast.error("Add Category first from Settings > Category.", {
            timeout: 3000,
            position: "top-right",
        });
        return;
    }
    if (areCategoriesExhausted.value) {
        toast.error("All available categories have been used.");
        return;
    }

    const newBudgetItem = {
        category_id: null,
        quantity: 1,
        amount: null,
        currency: props.project.currency?.symbol || "$",
    };

    state.procurementBudget.push(newBudgetItem);

    await nextTick();

    initializeNiceSelect();
};

const totalProcurementAmount = computed(() => {
    return state.procurementBudget.reduce(
        (sum, item) => sum + (parseFloat(item.amount) || 0),
        0
    );
});

const rules = computed(() => {
    return {
        stages: state.stages.map((stage) => ({
            amount: {
                required: helpers.withMessage("Amount is required.", required),
                numeric: helpers.withMessage(
                    "Amount must be a number.",
                    (value) => !isNaN(value)
                ),
                min: helpers.withMessage(
                    "Quantity cannot be negative.",
                    (value) => value >= 0
                ),
            },
            start_date: {
                required: helpers.withMessage(
                    "Start Date is required.",
                    required
                ),
                beforeDueDate: helpers.withMessage(
                    "Start Date cannot be after the Due Date.",
                    (value) => {
                        const inputDate = moment(value, "DD/MM/YYYY", true);
                        const stageDueDate = moment(
                            stage.due_date,
                            "DD/MM/YYYY",
                            true
                        );
                        return (
                            inputDate.isValid() &&
                            (!stageDueDate.isValid() ||
                                inputDate.isSameOrBefore(stageDueDate, "day"))
                        );
                    }
                ),
            },

            due_date: {
                required: helpers.withMessage(
                    "Due Date is required.",
                    required
                ),
                afterStartDate: helpers.withMessage(
                    "Due Date cannot be before the Start Date.",
                    (value) => {
                        const inputDate = moment(value, "DD/MM/YYYY", true); // Parse due date
                        const stageStart = moment(
                            stage.start_date,
                            "DD/MM/YYYY",
                            true
                        );
                        return (
                            inputDate.isValid() &&
                            stageStart.isValid() &&
                            inputDate.isSameOrAfter(stageStart, "day")
                        );
                    }
                ),
            },
        })),

        procurementBudget: state.procurementBudget.map(() => ({
            category_id: {
                required: helpers.withMessage(
                    "Category is required.",
                    required
                ),
            },
            quantity: {
                min: helpers.withMessage(
                    "Quantity should be greater than 0.",
                    (value) => value >= 1
                ),
                required: helpers.withMessage(
                    "Quantity is required.",
                    required
                ),
                numeric: helpers.withMessage(
                    "Quantity must be a number.",
                    (value) => !isNaN(value)
                ),
            },
            amount: {
                min: helpers.withMessage(
                    "Quantity cannot be negative.",
                    (value) => value >= 0
                ),
                required: helpers.withMessage("Amount is required.", required),
                numeric: helpers.withMessage(
                    "Amount must be a number.",
                    (value) => !isNaN(value)
                ),
            },
        })),
    };
});

const v$ = useVuelidate(rules, state);

const isLoading = ref(false);
const handleSubmit = async (event) => {
    v$.value.$touch();
    if (v$.value.$pending) return;

    if (v$.value.$invalid) {
        return false;
    }

    isLoading.value = true;

    event.preventDefault();

    const data = { ...state };

    try {
        const response = await axios.put(
            `/projects/${props.project.uuid}/financial`,
            data
        );
        if (response.data) {
            window.location.reload();
        }
    } catch (error) {
        console.error("Error creating project:", error);
    }
};
const getAvailableCategories = (currentIndex) => {
    // Get all selected category IDs except for the current item
    const selectedCategories = state.procurementBudget
        .filter((_, index) => index !== currentIndex)
        .map((item) => item.category_id);

    // Return categories that are not selected OR the one currently selected by the item
    return props.categories.filter(
        (category) =>
            !selectedCategories.includes(category.id) ||
            state.procurementBudget[currentIndex]?.category_id === category.id
    );
};
const areCategoriesExhausted = computed(() => {
    const selectedCategories = state.procurementBudget.map(
        (item) => item.category_id
    );
    return selectedCategories.length >= props.categories.length;
});

watch(
    () => state.procurementBudget.map((item) => item.category_id),
    () => {
        initializeNiceSelect();
    },
    { deep: true }
);
</script>
<style scoped>
.break-word {
    overflow-wrap: break-word; /* Breaks long words */
    hyphens: auto; /* Enables hyphenation */
}
</style>
