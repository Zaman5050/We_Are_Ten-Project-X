<template>
    <div class="d-flex align-items-center justify-content-between">
        <h2 class="signup-headings">Exchange Rates</h2>
        <button
            class="log-in"
            v-if="!isCreateNewExchangeRateEnable"
            @click="handleCreateNewExchangeRate(true), resetForm()"
        >
            Add New Exchange Rate
        </button>
    </div>

    <div v-if="isCreateNewExchangeRateEnable">
        <form @submit.prevent="handleSubmit()">
            <div class="row">
                <div class="col-md-3">
                    <div class="d-flex flex-column">
                        <label class="signup-labels">From</label>
                        <select
                            class="nice-select select no-arrow"
                            v-model="state.base_currency_id"
                            ref="baseCurrencyIdRef"
                            :disabled="true"
                        >
                            <option value="" disabled selected>
                                Select base currency
                            </option>
                            <option
                                v-for="currency in currencies"
                                :key="currency.id"
                                :value="currency.id"
                                :selected="currency.symbol === '£'"
                            >
                                {{ `(${currency.symbol}) ${currency.code}` }}
                            </option>
                        </select>
                    </div>
                    <input-validation-error-message :v="v$?.base_currency_id" />
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-column">
                        <label class="signup-labels">To</label>
                        <select
                            class="select"
                            v-model="state.quote_currency_id"
                            ref="quoteCurrencyIdRef"
                        >
                            <option value="" disabled selected>
                                Select quote currency
                            </option>
                            <option
                                v-for="currency in filteredCurrencies"
                                :key="currency.id"
                                :value="currency.id"
                            >
                                {{ `(${currency.symbol}) ${currency.code}` }}
                            </option>
                        </select>
                    </div>
                    <input-validation-error-message
                        :v="v$?.quote_currency_id"
                    />
                </div>
                <div class="col-md-3">
                    <label for="exchange_rate" class="signup-labels"
                        >Exchange Rate</label
                    >
                    <input
                        class="signup-inputs mw-100"
                        id="exchange_rate"
                        v-model="state.exchange_rate"
                        type="text"
                        autocomplete="off"
                        @input="v$.$clearExternalResults()"
                    />
                    <input-validation-error-message :v="v$?.exchange_rate" />
                </div>
            </div>
            <div class="d-flex my-5 justify-content-center align-items-center">
                <button
                    class="btn btn-default"
                    @click="handleCreateNewExchangeRate(false), resetForm()"
                >
                    Cancel
                </button>
                <button class="log-in" type="submit" :disabled="isLoading">
                    Save
                </button>
            </div>
        </form>
    </div>

    <div class="task-table-container table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Base Currency</th>
                    <th>Quote Currency</th>
                    <th>Exchange Rate</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="exchangeRate in exchangeRates"
                    :key="exchangeRate.uuid"
                >
                    <td>
                        {{
                            `(${exchangeRate?.base_currency?.symbol}) ${exchangeRate?.base_currency?.code} `
                        }}
                    </td>
                    <td>
                        {{
                            `(${exchangeRate?.quote_currency?.symbol}) ${exchangeRate?.quote_currency?.code} `
                        }}
                    </td>
                    <td>{{ exchangeRate?.exchange_rate }}</td>
                    <td>{{ computeTimezoneDate(exchangeRate?.created_at) }}</td>
                    <td>{{ computeTimezoneDate(exchangeRate?.updated_at) }}</td>
                </tr>
                <tr class="table-body" v-if="exchangeRates.length == 0">
                    <td class="d-none"></td>
                    <td colspan="2">No record found!</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script setup>
import { reactive, ref, computed, nextTick, onMounted } from "vue";
import axios from "axios";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers, numeric } from "@vuelidate/validators";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { computeTimezoneDate } from "../../helpers";
import { useToast } from "vue-toastification";

const props = defineProps({
    currencies: {
        type: Array,
        default: [],
    },
    exchange_rates: {
        type: Array,
        default: [],
    },
    exchange_rate_logs: {
        type: Array,
        default: [],
    },
});

const state = reactive({
    base_currency_id: 47,
    quote_currency_id: "",
    exchange_rate: "",
});

const exchangeRates = ref([]);

const rules = computed(() => ({
    base_currency_id: {
        required: helpers.withMessage("Base Currency is required.", required),
    },
    quote_currency_id: {
        required: helpers.withMessage("Quote Currency is required.", required),
    },
    exchange_rate: {
        required: helpers.withMessage("Exchange Rate is required.", required),
        numeric: helpers.withMessage("Exchange Rate must be number.", numeric),
    },
}));

const toast = useToast();
const serverSideErrors = reactive({});
const v$ = useVuelidate(rules, state, {
    $externalResults: serverSideErrors,
});

const baseCurrencyIdRef = ref(null);
const quoteCurrencyIdRef = ref(null);
const isCreateNewExchangeRateEnable = ref(false);
const editedRateUuid = ref("");
const isLoading = ref(false);

const initializeNiceSelect = () => {
    $(baseCurrencyIdRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates

    $(quoteCurrencyIdRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
    $(quoteCurrencyIdRef.value).niceSelect();

    $(baseCurrencyIdRef.value).on("change", function () {
        state.base_currency_id = parseInt($(this).val());
        validateBaseAndExchangeCurrency();
    });

    $(quoteCurrencyIdRef.value).on("change", function () {
        state.quote_currency_id = parseInt($(this).val());
        validateBaseAndExchangeCurrency();
    });
};

const handleSubmit = async () => {
    try {
        v$.value.$clearExternalResults();
        v$.value.$touch(); // Mark fields as touched to show errors
        if (v$.value.$pending || v$.value.$invalid) return; // Wait for any async validation

        isLoading.value = true;
        let response;
        if (editedRateUuid.value === "") {
            response = await axios.post(`/settings/exchange-rate`, {
                ...state,
            });
        } else {
            response = await axios.put(
                `/settings/exchange-rate/${editedRateUuid.value}`,
                {
                    ...state,
                }
            );
        }
        if (response.data) {
            isLoading.value = false;
            if (editedRateUuid.value === "") {
                exchangeRates.value.unshift(response.data.exchange_rate);
            } else {
                exchangeRates.value = exchangeRates.value.map((rate) => {
                    if (rate.uuid === response.data?.exchange_rate?.uuid)
                        return { ...response.data?.exchange_rate };
                    return rate;
                });
            }
            toast.success(response?.data?.message || "Success!", {
                timeout: 3000,
                position: "top-right",
            });
            handleCreateNewExchangeRate(false);
            resetForm();
        }
    } catch (error) {
        isLoading.value = false;
        if (error.response?.status === 500) {
            toast.error(error?.response?.data?.message || "Server Error!", {
                timeout: 3000,
                position: "top-right",
            });
        } else if (error?.response?.status === 422) {
            if (error.response?.data?.errors) {
                Object.assign(serverSideErrors, {
                    ...error.response?.data?.errors,
                });
            } else {
                console.error("Unexpected error:", error);
            }
        }
    }
};
const handleCreateNewExchangeRate = async (value) => {
    isCreateNewExchangeRateEnable.value = value;
    if (value) {
        await nextTick();
        initializeNiceSelect(); // Re-initialize niceSelect after DOM update
    }
};
const resetForm = () => {
    editedRateUuid.value = "";
    v$.value.$reset();
    state.base_currency_id = 47;
    state.quote_currency_id = "";
    state.exchange_rate = "";
};
onMounted(() => {
    exchangeRates.value = props.exchange_rate_logs;
});
const validateBaseAndExchangeCurrency = () => {
    v$.value.$clearExternalResults();
    if (state.base_currency_id === state.quote_currency_id) {
        state.exchange_rate = 1;
        $("#exchange_rate").attr("disabled", "disabled");
    } else {
        $("#exchange_rate").removeAttr("disabled");
    }
};
const filteredCurrencies = computed(() => {
    return props.currencies.filter(
        (currency) => !(currency.symbol === "£" && currency.code === "GBP")
    );
});
</script>
<style scoped>
.select.no-arrow:disabled {
    appearance: none; /* Removes the default dropdown arrow */
    -moz-appearance: none; /* Firefox */
    -webkit-appearance: none; /* Safari/Chrome */
    background: #f0f0f0; /* Optional: Set a gray background to indicate it's disabled */
    cursor: not-allowed; /* Optional: Set a 'not-allowed' cursor */
    border: 1px solid #ccc; /* Optional: Adjust border for a cleaner look */
}

.select.no-arrow:disabled::after {
    display: none; /* Prevent any pseudo-elements for dropdown arrows */
}
</style>
