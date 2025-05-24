<template>
    <vue-loader v-if="isLoading" />
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="task-stage-heading">Procurement Budget</h6>
        <button
            class="btn btn-default border bg-yellow darker-grotesque-bold hover-bg"
            type="button"
        >
            Total Profit:
            £ {{ totalProcurementProfit }}
        </button>
    </div>
    <div v-if="Object.keys(state.procurementBudgetData).length > 0">
        <div
            v-for="(procurements, category, index) in state.procurementBudgetData"
            :key="category"
        >
            <div
                class="d-flex align-items-center justify-content-between"
                :id="`shadow-container-procurement-${index}`"
            >
                <label class="signup-labels table-date-heading mb-0"
                    >{{ category }} ({{ procurements.length }})</label
                >
                <div class="position-relative">
                    <button
                        class="collaps-expand-btn"
                        :data-target="`#procurement-table-${index}`"
                        :data-Shadow="`#shadow-container-procurement-${index}`"
                    >
                        Collapse
                    </button>
                    <img
                        class="collaps-icon"
                        src="/assets/images/arrow-icon.svg"
                    />
                </div>
            </div>
            <div class="task-table-container">
                <table class="table" :id="`procurement-table-${index}`">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Item Name</th>
                            <th>Trade Price</th>
                            <th>Markup</th>
                            <th>Actual Price</th>
                            <th>Quantity</th>
                            <th>Total Exc. VAT</th>
                            <th>Vat</th>
                            <th>Total Inc. VAT</th>
                            <th>Profit</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            class="table-body"
                            v-for="(procurement, index) in procurements"
                            :key="procurement.uuid"
                        >
                            <td class="d-none"></td>
                            <td>{{ procurement?.product?.sku }}</td>
                            <td>{{ procurement?.product?.product_name }}</td>
                            <td>
                                {{ procurement?.base_currency?.symbol
                                }} {{ procurement?.trade_price }}
                            </td>
                            <td
                                @dblclick="
                                    handleMarkupDoubleClick(procurement.uuid)
                                "
                                :id="`markup_td_${procurement.uuid}`"
                            >
                                {{ procurement?.markup }}%
                            </td>
                            <td
                                class="d-none"
                                :id="`markup_input_${procurement.uuid}`"
                            >
                                <div class="d-flex mb-3 position-relative">
                                    <input
                                         min="1"
                                        type="number"
                                        step=".25"
                                        style="
                                            border-radius: 6px 0 0 6px;
                                            padding: 0 9px;
                                        "
                                        class="signup-inputs mw-100"
                                        placeholder="0"
                                        v-model="
                                            state.procurementBudgetData[
                                                category
                                            ][index].markup
                                        "
                                        @change="
                                            handleMarkupChange(category, index)
                                        "
                                        @keyup.enter="
                                            toggleMarkupInputs(),
                                                handleMarkupSubmit(
                                                    procurement.uuid
                                                )
                                        "
                                    />
                                    <div class="input-group-append">
                                        <span class="input-group-text">% </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ procurement?.base_currency?.symbol
                                }} {{ formatNumber(procurement?.actual_price) }}
                            </td>
                            <td>{{ formatNumber(procurement?.quantity) }}</td>
                            <td>
                                {{ procurement?.base_currency?.symbol
                                }} {{
                                    formatNumber(
                                        procurement?.total_exclusive_tax
                                    )
                                }}
                            </td>
                            <td>
                                {{ procurement?.base_currency?.symbol
                                }} {{
                                    formatNumber(
                                        procurement?.total_inclusive_tax -
                                            procurement?.total_exclusive_tax
                                    )
                                }}
                            </td>
                            <td>
                                {{ procurement?.base_currency?.symbol
                                }}  {{
                                    formatNumber(
                                        procurement?.total_inclusive_tax
                                    )
                                }}
                            </td>
                            <td>{{ procurement?.base_currency?.symbol
                                }} {{ formatNumber(procurement?.profit) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div v-else>
        <slot></slot>
    </div>
</template>
<script setup>
import axios from "axios";
import { computed, onMounted, reactive, ref } from "vue";
import { useRoute } from "vue-router";
import VueLoader from "../Common/Loader.vue";

const props = defineProps({
    procurement_financial_data: {
        type: Object,
        default: {},
    },
});
const route = useRoute();
const isLoading = ref(false);
const state = reactive({
    procurementBudgetData:
        props?.procurement_financial_data?.procurementBudgetData,
});
const totalProcurementProfit = computed(() => {
    return Object.values(state.procurementBudgetData).reduce(
        (outerAcc, collection) => {
            return (
                outerAcc +
                collection.reduce((innerAcc, item) => {
                    return innerAcc + parseFloat(item.profit);
                }, 0)
            );
        },
        0
    );
});

const handleMarkupDoubleClick = (procurementUuid) => {
    $(`#markup_td_${procurementUuid}`).addClass("d-none");
    $(`#markup_input_${procurementUuid}`).removeClass("d-none");
};

onMounted(() => {
    $(document).on("click", function (event) {
        if (!$(event.target).closest('[id^="markup_input_"]').length) {
            const editableInputId = $(
                'td[id^="markup_input_"]:not(.d-none):first'
            ).attr("id");
            toggleMarkupInputs();
            if (editableInputId) {
                const procurementUuid = editableInputId.split("_").pop();
                handleMarkupSubmit(procurementUuid);
            }
        }
    });
});

const handleMarkupChange = (category, procurementIndex) => {
    let procurement = Object.assign(
        {},
        state.procurementBudgetData[category][procurementIndex]
    );
    const actual_price =
        parseFloat(procurement.trade_price) +
        (procurement.markup === ""
            ? 0
            : procurement.trade_price * (parseFloat(procurement.markup) / 100));
    const total_exclusive_tax =
        actual_price * parseFloat(procurement.quantity) || 0;
    const total_inclusive_tax =
        total_exclusive_tax + total_exclusive_tax * (5 / 100) || 0;
    const profit =
        (actual_price - parseFloat(procurement.trade_price)) *
        parseFloat(procurement.quantity);
    procurement.actual_price = actual_price.toFixed(2);
    procurement.total_exclusive_tax = total_exclusive_tax.toFixed(2);
    procurement.total_inclusive_tax = total_inclusive_tax.toFixed(2);
    procurement.profit = profit.toFixed(2);
    Object.keys(state.procurementBudgetData).forEach((category) => {
        state.procurementBudgetData[category] = state.procurementBudgetData[
            category
        ].map((item) => {
            if (item.uuid === procurement.uuid) return { ...procurement };
            return item;
        });
    });
};
const formatNumber = (number) => {
    return parseFloat(number).toFixed(2);
};
const toggleMarkupInputs = () => {
    $('[id^="markup_input_"]').addClass("d-none");
    $('[id^="markup_td_"]').removeClass("d-none");
};
const handleMarkupSubmit = async (procurementUuid) => {
    try {
        isLoading.value = true;
        // Flatten all arrays within the dataset
        const allItems = Object.values(state.procurementBudgetData).flat();
        // Find and return the item with the matching uuid
        const payload = allItems.find((item) => item.uuid === procurementUuid);
        const response = await axios.post(
            `/procurements/${procurementUuid}/update-procurement-markup`,
            payload
        );
        if (response.data) {
            isLoading.value = false;
        }
    } catch (error) {
        isLoading.value = false;
        console.error("Error updating procurement:", error);
    }
};
</script>
<style scoped lang="scss">
.signup-inputs {
    width: 75px;
    height: 35px;
}
.input-group-text {
    width: 10px;
    height: 35px;
    text-align: center;
}
</style>
