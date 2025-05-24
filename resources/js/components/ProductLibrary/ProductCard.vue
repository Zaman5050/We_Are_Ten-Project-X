<template>
    <div class="library-card-container">
        <div class="library-card-img-container position-relative">
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
                    @click="$emit('handleToBeDeletedProductId', product.uuid)"
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
                    product?.cover_image?.length > 0
                        ? product?.cover_image[0]['url']
                        : 'assets/images/project-list-default.svg'
                "
            />
        </div>
        <p style="font-size: 14px" class="mb-3">
            {{ product?.category?.name }}
        </p>
        <h5 class="library-card-img-heading mb-3">
            {{ product.product_name }}
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
import { useProcurementStore } from "../../stores/procurmentStore";
const ProcurmentStore = useProcurementStore();

const props = defineProps({
    product: {
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
    ProcurmentStore.setModule("product-library");
    ProcurmentStore.setMode("edit");
    ProcurmentStore.setProcurement(props.product);
    const offCanvas = new bootstrap.Offcanvas(
        document.getElementById("addProcurementCanvas")
    );
    offCanvas.show();
};

const filteredProjects = computed(() =>
    props.projects
        .filter((project) =>
            project.procurement_budget.some(
                (procurement) =>
                    procurement.category_id === props.product.category_id
            )
        )
        .filter((project) =>
            searchInputText.value === ""
                ? true
                : project.name
                      .toLowerCase()
                      .includes(searchInputText.value.toLowerCase())
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
    ProcurmentStore.setModule("product-library");
    ProcurmentStore.setMode("add-to");
    const procurement = {
        ...props.product,
        project_uuid: project.uuid,
        areas: project.areas,
        procurement_budget: project.procurement_budget,
        project_start_date: project.start_date,
        project_due_date: project.due_date,
        base_currency_exchange_rates:
            project?.currency?.base_currency_exchange_rates,
        base_currency_id: project?.currency_id,
    };
    ProcurmentStore.setProcurement(procurement);
    const offCanvas = new bootstrap.Offcanvas(
        document.getElementById("addProcurementCanvas")
    );
    offCanvas.show();
};
</script>
