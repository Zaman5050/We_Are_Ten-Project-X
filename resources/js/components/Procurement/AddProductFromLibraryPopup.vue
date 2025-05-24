<template>
    <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="addProcurementFromProductLibraryCanvas"
        aria-labelledby="addProcurementFromProductLibraryCanvasLabel"
    >
        <div class="offcanvas-body">
            <div
                :class="[
                    'dashboard-heading-container pb--20 d-flex align-items-start gap-3 flex-wrap',
                    state.categories.length > 0
                        ? 'justify-content-between'
                        : 'justify-content-end',
                ]"
            >
                <div
                    class="dashboard-button-container mw-80"
                    v-if="state.categories.length > 0"
                >
                    <a
                        :class="[
                            'dashboard-anchors text-decoration-underline',
                            category.isSelected ? 'active' : '',
                        ]"
                        v-for="category in state.categories"
                        :key="category.uuid"
                        @click="handleCategorySelection(category.uuid)"
                    >
                        {{ category.name }}
                    </a>
                </div>
                <div v-show="state.activeCategoryItems.length > 0">
                    <button
                        class="addTo-btn text-decoration-underline bg-yellow"
                        @click="handleAddToClick"
                        :disabled="state.categories.length === 0"
                    >
                        Add To
                    </button>
                </div>
            </div>
            <div class="" v-if="state.activeCategoryItems.length === 0">
                <div class="create-project-container">
                    <div class="text-center">
                        <img
                            src="../../../../public/assets/images/add-material-default.png"
                            style="
                                width: 170px;
                                height: 214px;
                                object-fit: contain;
                            "
                        />
                        <h2
                            class="signup-headings"
                            style="margin-bottom: 12px; font-size: 30px"
                        >
                            No Product Added Yet.
                        </h2>
                        <!-- <span class="create-to-start d-flex justify-content-center gap-1">Start a conversation to see your messages here</span> -->
                    </div>
                </div>
            </div>
            <div
                v-else
                class="mb-4 d-flex align-items-center justify-content-between gap-3"
                style="border-radius: 6px"
                :class="{ 'bg-grey': product.isSelected }"
                v-for="product in state.activeCategoryItems"
                :key="product.uuid"
            >
                <div class="d-flex align-items-center">
                    <label
                        class="check-container display-block offcanvas-checbox"
                    >
                        <input
                            type="checkbox"
                            :checked="product.isSelected"
                            @click="handleItemSelection(product.uuid)"
                        />
                        <span class="checkmark"></span>
                    </label>
                    <div class="d-flex align-items-center gap-3">
                        <img
                            class="material-library-entity-img"
                            :src="
                                product.cover_image.length > 0
                                    ? product?.cover_image[0]?.url
                                    : '/assets/images/project-list-default.svg'
                            "
                            alt="off-canvas-image"
                        />
                        <span>
                            <h6 class="offcanvas-img-heading">
                                {{ product.product_name }}
                            </h6>
                            <p>{{ product?.supplier?.name }}</p>
                        </span>
                    </div>
                </div>
            </div>
            <x-schedule.add-material />
        </div>
    </div>
</template>

<script setup>
import { reactive, watch, onMounted } from "vue";
import { useProcurementStore } from "../../stores/procurmentStore";
import { useToast } from "vue-toastification";
const ProcurmentStore = useProcurementStore();

const props = defineProps({
    categories_with_products: {
        type: Object,
        default: [],
    },
    project: {
        type: Object,
        default: {},
    },
});

const state = reactive({
    categories: [],
    activeCategoryItems: [],
});
const toast = useToast();
onMounted(() => {
    if (props.project.procurement_budget.length === 0) {
        toast.error("Please add procurement Budget.", {
            timeout: 3000,
            position: "top-right",
        });
    }
    if (Object.keys(props.categories_with_products).length === 0)
        state.categories = [];
    else
        state.categories = Object.keys(props.categories_with_products).map(
            (category, index) => {
                return {
                    uuid:
                        props.categories_with_products[category][0]["category"][
                            "uuid"
                        ] ?? "",
                    name: category,
                    isSelected: index === 0,
                };
            }
        );
});

watch(
    () => state.categories,
    (newVal) => {
        const computedActiveCategory =
            state.categories.filter((category) => category.isSelected)[0] ?? [];
        const propEntries = Object.entries(props.categories_with_products);

        let products = propEntries.filter(([key, value]) => {
            // Ensure value is defined and has a 'category' property
            return (
                value.length > 0 &&
                value[0]["category"] &&
                value[0]["category"]["uuid"] === computedActiveCategory["uuid"]
            );
        });
        // Convert filtered entries back to an object or extract the products as needed
        products = Object.fromEntries(products);
        products =
            Object.keys(products).map((category) => products[category])[0] ??
            [];
        state.activeCategoryItems = products.map((product) =>
            Object.assign(
                {
                    isSelected: false,
                    quantity: "",
                    project_area_ids: [],
                },
                product
            )
        );
    },
    { deep: true }
);

const handleItemSelection = (itemUuid) => {
    state.activeCategoryItems = state.activeCategoryItems.map(
        (categoryItem) => {
            return {
                ...categoryItem,
                isSelected: categoryItem.uuid === itemUuid,
            };
        }
    );
};

const handleCategorySelection = (categoryUuid) => {
    state.categories = state.categories.map((category) => {
        // Set isSelected to true only for the clicked category, and false for others
        return {
            ...category,
            isSelected: category.uuid === categoryUuid ? true : false,
        };
    });
};
const handleAddToClick = async () => {
    const selectedProduct = state.activeCategoryItems.filter(
        (item) => item.isSelected
    );
    if (selectedProduct.length <= 0) {
        toast.error("Please select a product.", {
            timeout: 3000,
            position: "top-right",
        });
        return;
    }
    const product = selectedProduct[0];
    const project = props.project;
    ProcurmentStore.resetProcurement();
    ProcurmentStore.setModule("product-library");
    ProcurmentStore.setMode("add-to");
    const procurement = {
        ...product,
        project_uuid: project.uuid,
        areas: project.areas,
        procurement_budget: project.procurement_budget,
        project_start_date: project.start_date,
        project_due_date: project.due_date,
    };

    var bsOffcanvas = bootstrap.Offcanvas.getInstance(
        "#addProcurementFromProductLibraryCanvas"
    );
    bsOffcanvas.hide();
    ProcurmentStore.setProcurement(procurement);
    const offCanvas = new bootstrap.Offcanvas(
        document.getElementById("addProcurementCanvas")
    );

    offCanvas.show();
};
</script>

<style scoped lang="scss">
.dashboard-anchors {
    &.active {
        background: #ffdc5f;
        color: #000;
    }
}
</style>
