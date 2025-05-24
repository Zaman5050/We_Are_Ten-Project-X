<template>
    <AddProductFromLibraryPopup
        :categories_with_products="categories_with_products"
        :project="project"
    />
    <AddCustomProcurementPopup
        :suppliers="suppliers"
        :currencies="currencies"
        :project_areas="project_areas"
        :categories="categories"
        :project="project"
    />
</template>

<script setup>
import AddCustomProcurementPopup from "./AddCustomProcurementPopup.vue";
import AddProductFromLibraryPopup from "./AddProductFromLibraryPopup.vue";

const props = defineProps({
    suppliers: {
        type: Array,
        default: [],
    },
    areas_with_products: {
        type: Object,
        default: [],
    },
    categories_with_products: {
        type: Object,
        default: {},
    },
    project_areas: {
        type: Array,
        default: [],
    },
    currencies: {
        type: Array,
        default: [],
    },
    categories: {
        type: Array,
        default: [],
    },
    project: {
        type: Object,
        default: {},
    },
});
</script>
