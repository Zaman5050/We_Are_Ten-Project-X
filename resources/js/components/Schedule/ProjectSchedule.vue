<template>
    <AddMaterialFromLibraryPopup
        :categories_with_materials="props.categories_with_materials"
        :project_areas="props.project_areas"
    />
    <AddCustomSchedulePopup
        :suppliers="props.suppliers"
        :project_areas="props.project_areas"
        :categories="props.categories"
    />
</template>

<script setup>
import { defineProps } from "vue";
import AddCustomSchedulePopup from "./AddCustomSchedulePopup.vue";
import AddMaterialFromLibraryPopup from "./AddMaterialFromLibraryPopup.vue";

const props = defineProps({
    suppliers: {
        type: Array,
        default: [],
    },
    categories_with_materials: {
        type: Object,
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
</script>
