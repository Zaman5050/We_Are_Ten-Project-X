<template>
    <div class="library-card-container">
        <div class="library-card-img-container ">
            <div class="library-card-edit-dell">
                <button class="edit-dell-btn me-2" @click="handleEditClick">
                    <img
                        style="filter: invert(1)"
                        src="assets/images/edit-icon.svg"
                    />
                </button>
                <button
                    class="edit-dell-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#delete-area"
                    @click="$emit('handleToBeDeletedMaterialId', material.uuid)"
                    type="button"
                >
                    <img
                        style="filter: invert(1)"
                        src="assets/images/delete-icon.svg"
                    />
                </button>
            </div>
            <img
                class="library-card-img"
                :src="
                    material.cover_image.length > 0
                        ? material.cover_image[0]['url']
                        : 'assets/images/project-list-default.svg'
                "
            />
            
        </div>
        <p class="mb-3">
            {{ material?.category?.name }}
        </p>
        <h5 class="library-card-img-heading mb-3">
            {{ material.item_name }}
        </h5>
        <div class="d-flex align-items-center justify-content-between">
            <div class="dropdown-search-field">
                <div class="dropdown">
                    <button
                        class="add-to-btn"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        Add to
                    </button>
                    <ul
                        style="
                            width: 100%;
                            min-width: 213px;
                            padding: 0px 7px 15px 7px !important;
                            box-shadow: 0px 1px 2px 0px #5b687152;
                            box-shadow: 0px 0px 2px 0px #1a202452;
                            max-height: 242px;
                            overflow-y: auto;
                        "
                        class="dropdown-menu border-0"
                        aria-labelledby="dropdownMenuLink"
                    >
                        <div
                            style="
                                padding-top: 15px;
                                position: sticky;
                                top: 0px;
                                background: #fff;
                            "
                        >
                            <input
                                style="
                                    margin-bottom: 10px;
                                    background-image: url('assets/images/search-icon.svg');
                                "
                                type="text"
                                v-model="searchInputText"
                                placeholder="Search.."
                                class="nav-input me-0 mw-100"
                            />
                        </div>

                        <li
                            class="search-dropDown-list"
                            v-for="project in filteredProjects"
                            :key="project.uuid"
                            @click="handleAddToProjectClick(project.uuid)"
                        >
                            <a class="dropdown-item">{{ project.name }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <img
                style="margin-right: 25px"
                src="assets/images/right-arrow.svg"
                @click="handleEditClick"
            />
        </div>
    </div>
</template>

<script setup>
import { computed, nextTick, ref } from "vue";
import { useScheduleStore } from "../../stores/scheduleStore";
import { useToast } from "vue-toastification";

const ScheduleStore = useScheduleStore();
const toast = useToast();

const props = defineProps({
    material: {
        type: Object,
        default: {},
    },
    projects: {
        type: Array,
        default: [],
    },
});

const searchInputText = ref("");

const handleEditClick = () => {
    ScheduleStore.setModule("material-library");
    ScheduleStore.setMode("edit");
    ScheduleStore.setSchdedule(props.material);
    const offCanvas = new bootstrap.Offcanvas(
        document.getElementById("addSceduleCanvas")
    );
    offCanvas.show();
};

const filteredProjects = computed(() =>
    props.projects.filter(
        (project) =>
            !project.project_material?.some(
                (mat) => mat.material_library_id === props.material.id
            ) &&
            (searchInputText.value === "" ||
                project.name
                    .toLowerCase()
                    .includes(searchInputText.value.toLowerCase()))
    )
);

const handleAddToClick = async (event) => {
    $(".add-to-btn")
        .not(event.target)
        .each(function () {
            $(this).next().removeClass("showSearchContainer");
        });
    await nextTick();
    $(event.target).next()[0].classList.toggle("showSearchContainer");
};

const handleAddToProjectClick = (projectUuid) => {
    const project = props.projects.find(
        (project) => project.uuid === projectUuid
    );
    if (project.areas.length === 0) {
        toast.error("Please add Project Area.", {
            timeout: 3000,
            position: "top-right",
        });
    } else {
        ScheduleStore.setModule("material-library");
        ScheduleStore.setMode("add-to");
        const schedule = {
            ...props.material,
            project_uuid: project.uuid,
            areas: project.areas,
        };
        ScheduleStore.setSchdedule(schedule);
        const offCanvas = new bootstrap.Offcanvas(
            document.getElementById("addSceduleCanvas")
        );
        offCanvas.show();
    }
};
</script>
