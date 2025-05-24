import { createApp } from "vue";
import router from "./router";
import VueDragscroll from "vue-dragscroll";
import VueTelInput from "vue-tel-input";
import JsonExcel from "vue-json-excel3";
import { createPinia } from "pinia";
import piniaPersistedState from "pinia-plugin-persistedstate";
import "vue-toastification/dist/index.css";

// List of Components

import CreateProject from "./components/Project/CreateProject.vue";
import StoreTask from "./components/Task/StoreTask.vue";
import TaskListings from "./components/Task/TaskListings.vue";
import Timesheet from "./components/Timesheet/Timesheet.vue";
import Supplier from "./components/Supplier/Supplier.vue";
import IconSpinner from "./components/IconSpinner.vue";
import ValidationError from "./components/ValidationError.vue";
import OpenModel from "./components/OpenModel.vue";
import Avatar from "./components/Avatar.vue";
import EditProject from "./components/Project/EditProject.vue";
import ProjectArea from "./components/Project/ProjectArea.vue";
import ProjectDropboxFiles from "@/components/Project/ProjectDropboxFiles.vue";
import ProjectSchedule from "@/components/Schedule/ProjectSchedule.vue";
import addTeam from "./components/Teams/addTeam.vue";
import Profile from "./components/Teams/Profile.vue";
import MemberAvatarList from "@/components/MemberAvatarList.vue";
import Chat from "@/components/Chat/Chat.vue";
import LeaveModal from "./components/Leave/LeaveModal.vue";
import PendingLeavesTable from "./components/Leave/PendingLeavesTable.vue";
import NavbarNotification from "./components/Common/NavbarNotification.vue";
import ApplyLeaveButton from "@/components/Leave/ApplyLeaveButton.vue";
import VueLoader from "@/components/Common/Loader.vue";
import ProjectProcurement from "@/components/Procurement/ProjectProcurement.vue";
import ProcurementDetailButton from "@/components/Procurement/ProcurementDetailButton.vue";
import AddCustomProcurementButton from "@/components/Procurement/AddCustomProcurementButton.vue";
import ScheduleDetailButton from "@/components/Schedule/ScheduleDetailButton.vue";
import AddCustomScheduleButton from "@/components/Schedule/AddCustomScheduleButton.vue";
import TenantDashboard from "@/components/Tenant/TenantDashboard.vue";
import MaterialLibrary from "@/components/MaterialLibrary/MaterialLibrary.vue";
import AddCustomSchedulePopup from "@/components/Schedule/AddCustomSchedulePopup.vue";
import ProductLibrary from "@/components/ProductLibrary/ProductLibrary.vue";
import DeleteModal from "@/components/Common/DeleteModal.vue";
import Category from "./components/Category/Category.vue";
import Financial from "./components/Project/Financial.vue";
import ProcurementFinance from "./components/Finance/ProcurementFinance.vue";
import ExchangeRate from "./components/ExchangeRate/ExchangeRate.vue";
import Toast, { useToast } from "vue-toastification";
import AddProcurementFromProductLibraryButton from "@/components/Procurement/AddProcurementFromProductLibraryButton.vue";
import ProjectCard from "./components/Project/ProjectCard.vue";
import CreateProjectButton from "./components/Project/CreateProjectButton.vue";
import AddFromMaterialLibraryButton from "@/components/Schedule/AddFromMaterialLibraryButton.vue";
import LogTimerBlock from "@/components/Task/LogTimerBlock.vue";




const components = {
    CreateProject,
    StoreTask,
    ValidationError,
    IconSpinner,
    OpenModel,
    TaskListings,
    Avatar,
    EditProject,
    ProjectArea,
    Timesheet,
    Supplier,
    ProjectDropboxFiles,
    ProjectSchedule,
    addTeam,
    MemberAvatarList,
    Chat,
    LeaveModal,
    PendingLeavesTable,
    NavbarNotification,
    ApplyLeaveButton,
    VueLoader,
    ProjectProcurement,
    ProcurementDetailButton,
    AddCustomProcurementButton,
    Profile,
    ScheduleDetailButton,
    AddCustomScheduleButton,
    TenantDashboard,
    MaterialLibrary,
    AddCustomSchedulePopup,
    ProductLibrary,
    DeleteModal,
    Category,
    Financial,
    ProcurementFinance,
    ExchangeRate,
    AddProcurementFromProductLibraryButton,
    ProjectCard,
    CreateProjectButton,
    AddFromMaterialLibraryButton,
    LogTimerBlock,
};

const app = createApp({
    components,
});
const pinia = createPinia();
pinia.use(piniaPersistedState);

const globalOptions = {
    mode: "international",
    autoFormat: true,
    dropdownOptions: {
        showDialCodeInList: true,
        showFlags: true,
        showSearchBox: true,
    },
};

app.use(router);
app.use(VueDragscroll);
app.use(VueTelInput, globalOptions);
app.component("downloadExcel", JsonExcel);
app.use(pinia);
app.use(Toast);
const toast = useToast();
window.Toast = toast;
app.mount("#app");

import "./chats.js";
