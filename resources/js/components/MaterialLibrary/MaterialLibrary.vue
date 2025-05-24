<template>
    <div class="main-content-inner">
        <div
            class="dashboard-heading-container pb--40 d-flex justify-content-between"
        >
            <h1 class="dashboard-main-heading mb-0">Material Library</h1>
            <div class="d-flex gap-3">
                <a
                    class="filter-btn import-creat-project bg-yellow"
                    @click="handleAddMaterialClick"
                >
                    Add Material
                </a>
            </div>
        </div>
        <!-- Main inner content -->
        <div class="sales-report-area">
            <div class="pb--20 d-flex flex-wrap justify-content-between">
                <div class="d-flex table-filter-dp flex-wrap">
                    <div class="dropdown">
                        <a
                            class="d-flex align-items-center filter-btn border-0 dropdown-toggle gap-3"
                            href="#"
                            role="button"
                            id="categoryFilter"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <img src="assets/images/filter-icon.svg" />
                            <span>Filter by Category</span>
                        </a>
                        <ul
                            class="dropdown-menu border-0"
                            aria-labelledby="categoryFilter"
                        >
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="#"
                                    @click="selectedCategory = null"
                                >
                                    None
                                </a>
                            </li>
                            <li
                                v-for="category in categories"
                                :key="category.id"
                            >
                                <a
                                    class="dropdown-item"
                                    href="#"
                                    @click="selectedCategory = category.id"
                                    >{{ category.name }}</a
                                >
                            </li>
                        </ul>
                    </div>

                    <!-- Dropdown for Color -->
                    <div class="dropdown">
                        <a
                            class="d-flex align-items-center filter-btn border-0 dropdown-toggle gap-3"
                            href="#"
                            role="button"
                            id="colorFilter"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <img src="assets/images/filter-icon.svg" />
                            <span>Filter by Color</span>
                        </a>
                        <ul
                            class="dropdown-menu border-0"
                            aria-labelledby="colorFilter"
                        >
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="#"
                                    @click="selectedColor = null"
                                >
                                    None
                                </a>
                            </li>
                            <li v-for="color in uniqueColors" :key="color">
                                <a
                                    class="dropdown-item"
                                    href="#"
                                    @click="selectedColor = color"
                                >
                                    {{ color }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Dropdown for Brand -->
                    <div class="dropdown">
                        <a
                            class="d-flex align-items-center filter-btn border-0 dropdown-toggle gap-3"
                            href="#"
                            role="button"
                            id="brandFilter"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <img src="assets/images/filter-icon.svg" />
                            <span>Filter by Brand</span>
                        </a>
                        <ul
                            class="dropdown-menu border-0"
                            aria-labelledby="brandFilter"
                        >
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="#"
                                    @click="selectedBrand = null"
                                >
                                    None
                                </a>
                            </li>
                            <li v-for="brand in uniqueBrands" :key="brand">
                                <a
                                    class="dropdown-item"
                                    href="#"
                                    @click="selectedBrand = brand"
                                >
                                    {{ brand }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="">
                    <input
                        style="
                            background-image: url('assets/images/search-icon.svg');
                        "
                        class="nav-input nav-content-mobile-hide me-0"
                        placeholder="Search"
                        v-model="searchInputText"
                    />
                </div>
            </div>
            <div class="mb-3 d-flex flex-wrap gap-3">
                <material-card
                    v-for="material in filteredMaterials"
                    :key="material.uuid"
                    :material="material"
                    :projects="projects"
                    @handleToBeDeletedMaterialId="
                        (uuid) => (toBeDeletedMaterialId = uuid)
                    "
                />
            </div>
        </div>
        <!-- main content area end -->
    </div>
    <add-custom-schedule-popup
        :suppliers="suppliers"
        :categories="categories"
    />
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
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body pt-0 text-uppercase">
                    <p class="delete-warning">
                        Are you sure you want to delete this Material ?
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0 flex-nowrap">
                    <button
                        style="width: 50%; background: #fff; color: #000"
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
                        class="apply-leave-btn"
                        @click="handleRemoveMaterialClick"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        Yes
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onBeforeMount } from "vue";
import axios from "axios";
import MaterialCard from "./MaterialCard.vue";
import AddCustomSchedulePopup from "../Schedule/AddCustomSchedulePopup.vue";
import { useScheduleStore } from "../../stores/scheduleStore";

const props = defineProps({
    suppliers: {
        type: Array,
        default: [],
    },
    materials: {
        type: Array,
        default: [],
    },
    currencies: {
        type: Array,
        default: [],
    },
    projects: {
        type: Array,
        default: [],
    },
    categories: {
        type: Array,
        default: [],
    },
});

const scheduleStore = useScheduleStore();
const searchInputText = ref("");
const isLoading = ref(false);
const toBeDeletedMaterialId = ref("");
const selectedCategory = ref(null);
const selectedColor = ref(null);
const selectedBrand = ref(null);

const uniqueColors = computed(() => [
    ...new Set(props.materials.map((material) => material.color)),
]);
const uniqueBrands = computed(() => {
    return [
        ...new Set(props.materials.map((material) => material.brand_name)),
    ].filter(Boolean);
});

const filteredMaterials = computed(() => {
    return props.materials.filter((material) => {
        const matchesSearch = material.item_name
            .toLowerCase()
            .includes(searchInputText.value.toLowerCase());
        const matchesCategory = selectedCategory.value
            ? material.category_id === selectedCategory.value
            : true;
        const matchesColor = selectedColor.value
            ? material.color === selectedColor.value
            : true;
        const matchesBrand = selectedBrand.value
            ? material.brand_name === selectedBrand.value
            : true;

        return matchesSearch && matchesCategory && matchesColor && matchesBrand;
    });
});

const handleRemoveMaterialClick = async () => {
    try {
        isLoading.value = true;
        const response = await axios.delete(
            `/material-library/${toBeDeletedMaterialId.value}`
        );
        if (response.data) {
            window.location.reload();
        }
    } catch (error) {
        isLoading.value = false;
        console.error("Error creating project:", error);
    } finally {
        isLoading.value = false;
        toBeDeletedMaterialId.value = null;
    }
};

onBeforeMount(() => {
    scheduleStore.setModule("material-library");
});

const handleAddMaterialClick = () => {
    scheduleStore.setModule("material-library");
    scheduleStore.setMode("create");
    scheduleStore.resetSchdedule();
    const offCanvas = new bootstrap.Offcanvas(
        document.getElementById("addSceduleCanvas")
    );
    offCanvas.show();
};
</script>
