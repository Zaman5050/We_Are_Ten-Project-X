<template>
    <div>
        <div
            v-if="projects && projects.length > 0"
            :class="{
                'project-listing-whole-container': true,
                'd-flex align-items-center':
                    projects && projects.length > 0,
            }"
        >
            <div
                class="d-flex gap-3 flex-column mb-2"
                v-for="project in projects"
                :key="project.uuid"
            >
                <h6 class="px-3 text-uppercase text-secondary">
                    {{ project.type }}
                </h6>
                <div
                    style="height: 100%"
                    class="project-listing-main-container"
                >
                    <p class="box-text mb-2">{{ project.catagory }}</p>
                    <div
                        style="height: 100%"
                        class="project-listing-box d-flex flex-column justify-content-between"
                    >
                        <div>
                            <div class="text-end mb-3">
                                <div class="dropdown three-dots-dropdown">
                                    <a
                                        style="
                                            width: fit-content;
                                            margin-left: auto;
                                        "
                                        class="d-flex justify-content-end filter-btn border-0 dropdown-toggle gap-3"
                                        href="#"
                                        role="button"
                                        id="dropdownMenuLink"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        <img
                                            src="/assets/images/three-dots.svg"
                                            alt="Menu"
                                        />
                                    </a>
                                    <ul
                                        style="
                                            width: 100%;
                                            min-width: 138px;
                                            padding: 15px 18px;
                                            box-shadow: 0px 1px 2px 0px
                                                #5b687152;
                                            box-shadow: 0px 0px 2px 0px
                                                #1a202452;
                                        "
                                        class="dropdown-menu border-0"
                                        aria-labelledby="dropdownMenuLink"
                                    >
                                        <li>
                                            <button
                                                class="dropdown-item mb-2"
                                                type="submit"
                                                @click="
                                                    handleEditClick(project)
                                                "
                                            >
                                                Edit
                                            </button>
                                        </li>
                                        <li>
                                            <button
                                                style="color: #f8624e"
                                                class="dropdown-item mb-2"
                                                type="submit"
                                                @click="
                                                    deleteProjectLink(
                                                        project.uuid
                                                    )
                                                "
                                            >
                                                Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <a
                                href="javascript:void(0)"
                                @click="handleEditClick(project)"
                            >
                                <img
                                    class="project-list-img"
                                    :src="
                                        project.attachment?.media_url ||
                                        '/assets/images/project-list-default.svg'
                                    "
                                    alt="Project"
                                />
                            </a>
                            <div class="d-flex align-items-start">
                                <img
                                    class="me-2"
                                    src="/assets/images/tag-icon.svg"
                                    alt="Tag"
                                />
                                <div class="mb-3">
                                    <span
                                        v-for="tag in project.tags"
                                        :key="tag.id"
                                        style="background: #bd94f2de"
                                        class="tags me-2"
                                    >
                                        {{ tag.tag_name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div
                                class="mb-3 d-flex justify-content-between gap-3 align-items-center"
                            >
                                <p style="font-size: 16px; color: #1a1b1f">
                                    {{ project.name }}
                                </p>
                                <div></div>
                                <span
                                    v-if="project.stages.length > 0"
                                    class="tags"
                                >
                                    {{ project.stages[0].stage_name }}
                                </span>
                            </div>
                            <div
                                class="mb-3 d-flex justify-content-between gap-3"
                            >
                                <div style="font-size: 16px" class="d-flex">
                                    <p>Date Created:</p>
                                    <span
                                        class="text-secondary"
                                        style="color: #000"
                                    >
                                        {{ formatDate(project.created_at) }}
                                    </span>
                                </div>
                                <div
                                    class="member-img-icon-container d-flex justify-content-end"
                                >
                                    <member-avatar-list
                                        :users="project.members"
                                        :size="project.members.length"
                                        :uindex="project.id"
                                    ></member-avatar-list>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, onMounted, ref, computed } from "vue";
import { useProjectStore } from "../../stores/projectStore";
import { useToast } from "vue-toastification";
import axios from "axios";

const projectStore = useProjectStore();

const props = defineProps({
    projects: {
        type: Object,
        default: {},
    },
});
const formatDate = (date) => {
    return moment(date).format("DD/MM/YYYY"); // Format date using moment.js
};
const handleEditClick = (project) => {
    projectStore.setProject(project);
    projectStore.setMode("edit");
    const modalElement = document.getElementById("create-new-project");
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
};
const deleteProjectLink = (project) => {
    axios.delete(`/projects/${project}`);
    window.location.reload();
};
</script>

<!-- Add Multiselect CSS. Can be added as a static asset or inside a component. -->
<style src="vue-multiselect/dist/vue-multiselect.css"></style>
