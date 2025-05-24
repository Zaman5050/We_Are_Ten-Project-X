<template>
    <div
        class="dashboard-heading-container pb--40 d-flex justify-content-between"
    >
        <h1 class="dashboard-main-heading mb-0">Supplier Library</h1>
        <div class="d-flex gap-3" >
            <a
                class="filter-btn import-creat-project bg-yellow"
                data-bs-toggle="modal"
                @click="handleDetailClick"
                data-bs-target="#create-new-supplier"
                >Add Supplier</a
            >
        </div>
    </div>
    <div>
        <div class="custom-tabs-container mb-4" v-if="props.suppliers.length">
            <input
                class="nav-input p-task-input"
                name="search"
                placeholder="Search"
                @keydown.enter.prevent
                v-model="searchQuery"
                style="background-image: url('/assets/images/search-icon.svg')"
            />
            <button
                @click="toggleViewMode(false)"
                :class="['tab-img ', { 'active-sub-tab': !isBoardView }]"
            >
                <img :src="'/assets/images/table-view.svg'" />
            </button>

            <button
                @click="toggleViewMode(true)"
                :class="['tab-img ', { 'active-sub-tab': isBoardView }]"
            >
                <img :src="'/assets/images/board-view.svg'" />
            </button>
        </div>
        <div v-if="props.suppliers.length <= 0" class="empty-view">
            <div class="create-project-container ">
                <div class="text-center">
                    <img style="width:217px ;height:214px ; object-fit: cover;"
                        src="assets/images/add-material-default.png">
                    <h2 style="margin-bottom: 12px;" class="signup-headings">
                        No Suppliers Found
                    </h2>
                    <span class="create-to-start d-flex justify-content-center gap-1">
                        <button
                            class="border-dark border-0 bg-transparent border-bottom border- "
                            @click="handleDetailClick"
                            data-bs-toggle="modal"
                            data-bs-target="#create-new-supplier"
                        >
                            Start by adding a supplier
                        </button>
                        to your library to manage them
                        efficiently.
                    </span>
                </div>
            </div>
        </div>
        <div v-else>
            <!-- Conditional rendering based on board or table view -->
            <div v-if="isBoardView">
                <supplier-board-view
                    :suppliers="filteredSuppliers"
                ></supplier-board-view>
            </div>
            <div v-else>
                <supplier-table-view
                    :suppliers="filteredSuppliers"
                ></supplier-table-view>
            </div>
        </div>
    </div>
    <AddSupplierModal
        :timezones="timezones"
        :current-company="currentCompany"
        :currencies="currencies"
        :categories="categories"
    ></AddSupplierModal>
</template>

<script setup>
import { computed, ref, defineProps, onMounted, watch } from "vue";
import SupplierBoardView from "./SupplierBoardView.vue";
import SupplierTableView from "./SupplierTableView.vue";
import AddSupplierModal from "./AddSupplierModal.vue";
import { useRoute, useRouter } from "vue-router";
import { useSupplierStore } from "../../stores/supplierStore";
const supplierStore = useSupplierStore();

const props = defineProps({
    suppliers: {
        type: Object,
        default: () => {},
    },
    timezones: {
        type: Object,
        required: true,
        default: [],
    },
    currencies: {
        type: Object,
        required: true,
        default: [],
    },
    categories: {
        type: Object,
        required: true,
        default: [],
    },
    currentCompany: {
        type: Object,
        required: true,
        default: {},
    },
});
const isBoardView = ref(false);
const route = useRoute();
const router = useRouter();

// Search Query (Bound to the input field)
const searchQuery = ref("");

// Computed property to filter suppliers based on the search query
const filteredSuppliers = computed(() => {
    // Check if search query exists
    if (!searchQuery.value) {
        // Return all suppliers if no search query
        return props.suppliers;
    }

    // Return filtered suppliers based on search query
    return props.suppliers.filter((supplier) => {
        // Convert both the search query and supplier details to lowercase to make the search case-insensitive
        const searchLower = searchQuery.value.toLowerCase();
        return (
            supplier.name.toLowerCase().includes(searchLower) ||
            supplier.company_name.toLowerCase().includes(searchLower) ||
            supplier.email.toLowerCase().includes(searchLower)
        );
    });
});
const toggleViewMode = (status) => {
    isBoardView.value = status;

    if (status) {
        // Add 'board-view=true' to the URL
        router.push({
            query: { ...router.currentRoute.value.query, 'board-view': 'true' }
        });
    } else {
        // Remove 'board-view' query from the URL
        const newQuery = { ...router.currentRoute.value.query };
        delete newQuery['board-view']; // Remove 'board-view' query parameter
        router.push({ query: newQuery });
    }
};
const handleDetailClick = () => {
    supplierStore.resetSupplier();
    supplierStore.setMode("create");
};

// Watch for changes in the query parameter
watch(
    () => route.query["board-view"],
    (newValue) => {
        if (newValue === "true") {
            isBoardView.value = true;
        } else {
            isBoardView.value = false;
        }
    },
    { immediate: true } // Run the watch immediately when the component loads
);
</script>

<style scoped></style>
