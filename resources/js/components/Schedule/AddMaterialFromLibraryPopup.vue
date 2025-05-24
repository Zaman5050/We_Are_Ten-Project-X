<template>
    <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="addSceduleFromMaterialLibrary"
        aria-labelledby="addSceduleFromMaterialLibraryLabel"
    >
        <div class="offcanvas-body">
            <div
                :class="[
                    'dashboard-heading-container pb--20 d-flex align-items-center gap-3 flex-wrap',
                    state.categories.length > 0
                        ? 'justify-content-between'
                        : 'justify-content-end',
                ]"
            >
                <div
                    class="dashboard-button-container"
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
                <div class="ms-auto" v-show="state.activeCategoryItems.length > 0">
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
                            No Material Added Yet.
                        </h2>
                        <!-- <span class="create-to-start d-flex justify-content-center gap-1">Start a conversation to see your messages here</span> -->
                    </div>
                </div>
            </div>
            <div
                v-else
                class="mb-4 d-flex align-items-center justify-content-between gap-3"
                style="border-radius: 6px"
                :class="{ 'bg-grey': item.isSelected }"
                v-for="(item, index) in state.activeCategoryItems"
                :key="item.uuid"
            >
                <div class="d-flex align-items-center">
                    <label
                        class="check-container display-block offcanvas-checbox"
                    >
                        <input
                            type="checkbox"
                            :checked="item.isSelected"
                            @click="handleItemSelection(item.uuid)"
                        />
                        <span class="checkmark"></span>
                    </label>
                    <div class="d-flex align-items-center gap-3">
                        <img
                            class="material-library-entity-img"
                            :src="
                                item.cover_image.length > 0
                                    ? item?.cover_image[0]?.url
                                    : '/assets/images/project-list-default.svg'
                            "
                            alt="off-canvas-image"
                        />
                        <span>
                            <h6 class="offcanvas-img-heading">
                                {{ item?.item_name }}
                            </h6>
                            <p>{{ item.supplier?.name }}</p>
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
import { useToast } from "vue-toastification";
import { useScheduleStore } from "../../stores/scheduleStore";
import { useRoute } from "vue-router";

const ScheduleStore = useScheduleStore();
const route = useRoute();

const props = defineProps({
    categories_with_materials: {
        type: Object,
        default: [],
    },
    project_areas: {
        type: Array,
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
onMounted(() => {
    if (Object.keys(props.categories_with_materials).length === 0) {
        state.categories = [];
    } else {
        state.categories = Object.keys(props.categories_with_materials).map(
            (category, index) => {
                const categoryData =
                    props.categories_with_materials[category][0]?.category;
                return {
                    uuid: categoryData ? categoryData.uuid : "", // Safely access 'uuid' if category exists
                    name: category,
                    isSelected: index === 0,
                };
            }
        );
    }
});

watch(
    () => state.categories,
    (newVal) => {
        const computedActiveCategory =
            state.categories.filter((category) => category.isSelected)[0] ?? [];
        const propEntries = Object.entries(props.categories_with_materials);

        let materials = propEntries.filter(([key, value]) => {
            // Ensure value is defined and has a 'category' property
            return (
                value.length > 0 &&
                value[0]["category"] &&
                value[0]["category"]["uuid"] === computedActiveCategory["uuid"]
            );
        });
        // Convert filtered entries back to an object or extract the materials as needed
        materials = Object.fromEntries(materials);
        materials =
            Object.keys(materials).map((category) => materials[category])[0] ??
            [];
        state.activeCategoryItems = materials.map((material) =>
            Object.assign(
                {
                    isSelected: false,
                    quantity: "",
                    project_area_ids: [],
                },
                material
            )
        );
    },
    { deep: true }
);

const handleItemSelection = (itemUuid) => {
    state.activeCategoryItems = state.activeCategoryItems.map(
        (categoryItem) => {
            // Set isSelected to true for the clicked item, and false for others
            categoryItem.isSelected = categoryItem.uuid === itemUuid;
            return categoryItem;
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
    const selectedProduct = state.activeCategoryItems.find(
        (item) => item.isSelected
    );

    if (!selectedProduct) {
        useToast().error("Please select a material.", {
            timeout: 3000,
            position: "top-right",
        });
        return;
    }

    const schedule = {
        ...selectedProduct,
        project_uuid: route?.params?.project,
        areas: props.project_areas,
    };

    // Set the schedule in the store
    ScheduleStore.setModule("material-library");
    ScheduleStore.setMode("add-to");
    ScheduleStore.setSchdedule(schedule);

    const bsOffcanvas = bootstrap.Offcanvas.getInstance(
        document.getElementById("addSceduleFromMaterialLibrary")
    );
    if (bsOffcanvas) {
        bsOffcanvas.hide();
    }

    // Open the new off-canvas
    const offCanvas = new bootstrap.Offcanvas(
        document.getElementById("addSceduleCanvas")
    );
    offCanvas.show();
};
</script>

<style scoped lang="scss">
.dashboard-anchors {
    &.active {
        background: #ffdc5f;
    }
}
</style>
