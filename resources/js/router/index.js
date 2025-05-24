// resources/js/router/index.js
import { createRouter, createWebHistory } from "vue-router";
import ProjectFiles from "../components/Project/ProjectDropboxFiles.vue"; // Adjust import based on your structure
import AddMaterialFromLibraryPopup from "../components/Schedule/AddMaterialFromLibraryPopup.vue"; // Adjust import based on your structure
import SupplierView from "../components/Supplier/Supplier.vue"; // Adjust import based on your structure
import AddCustomSchedulePopup from "../components/Schedule/AddCustomSchedulePopup.vue"; // Adjust import based on your structure
import AddCustomProcurementPopup from "../components/Procurement/AddCustomProcurementPopup.vue"; // Adjust import based on your structure
import AddProductFromLibraryPopup from "../components/Procurement/AddProductFromLibraryPopup.vue"; // Adjust import based on your structure
import CreateDropboxFolderModal from "../components/Project/CreateDropboxFolderModal.vue";
import ProcurementFinance from "../components/Finance/ProcurementFinance.vue"; // Adjust import based on your structure

const routes = [
    {
        path: "/projects/:project/files/:path?",
        name: "project-files",
        component: ProjectFiles,
    },
    {
        path: "/projects/:project/schedules",
        name: "add-schdules-from-material-library",
        component: AddMaterialFromLibraryPopup,
    },
    {
        path: "/supplier",
        name: "supplier",
        component: SupplierView,
    },
    {
        path: "/projects/:project/procurements",
        name: "create-schdule",
        component: AddCustomSchedulePopup,
    },
    {
        path: "/projects/:project/procurements",
        name: "create-procurement",
        component: AddCustomProcurementPopup,
    },
    {
        path: "/projects/:project/schedules",
        name: "add-procurement-from-product-library",
        component: AddProductFromLibraryPopup,
    },
    {
        path: "/projects/:project/financials-procurement",
        name: "procurement-in-finance",
        component: ProcurementFinance,
    },
    {
        path: "/projects/:project_uuid/files",
        name: "create-dropbox-folder-modal",
        component: CreateDropboxFolderModal,
    },
    // Other routes
];

const router = createRouter({
    history: createWebHistory(import.meta.env.APP_URL), // Use your Laravel app URL
    routes,
});

export default router;
