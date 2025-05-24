<template>
    <div  class="supplier-board d-flex flex-wrap gap-3">
            <!-- Supplier Type (You can replace this with a relevant supplier field) -->
            <div class="d-flex gap-3 flex-column" v-for="(supplier, index) in suppliers" :key="index">
                <h3 class="px-3 text-uppercase text-secondary fs-14">
                    {{ supplier?.company_name }}
                </h3>

                <div
                    class="supplier-listing-box project-listing-main-container d-block"
                >
                    <!-- Dropdown for actions -->
                    <div class="text-end mb-3">
                        <div>
                            <button
                                class="edit-dell-btn me-2 editSupplier"
                                type="button"
                                @click="editSupplier(supplier)"
                            >
                                <img src="assets/images/edit-icon.svg" />
                            </button>
                            <button
                                class="edit-dell-btn"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#delete-task"
                                @click="setSupplierForDeletion(supplier.uuid)"
                            >
                                <img src="assets/images/delete-icon.svg" />
                            </button>
                        </div>
                    </div>

                    <!-- Supplier Info -->
                    <div class="mb-3">
                        <a href="#">
                            <img
                                class="supplier-list-img"
                                :src="
                                    supplier?.profile_picture ||
                                    'assets/images/project-list-default.svg'
                                "
                                alt="Supplier Logo"
                            />
                        </a>
                    </div>
                    <div class="d-flex mb-3">
                        <img
                            class="me-2"
                            src="/assets/images/tag-icon.svg"
                            alt="Tags Icon"
                        />
                        <div class="d-flex gap-2 flex-wrap">
                            <span v-for="tag in supplier?.categories" :key="tag.id" class="tags">{{ tag.name }}</span>
                        </div>
                    </div>
                    <!-- Footer Info -->
                    <div>
                        <div
                            class="mb-2 d-flex justify-content-between align-items-center"
                        >
                            <p>{{ supplier?.name }}</p>
                        </div>
                        <div
                            class="mb-2 d-flex justify-content-between align-items-center"
                        >
                            <p>{{ supplier?.email }}</p>
                        </div>
                        <div
                            class="d-flex justify-content-between align-items-center address-wrapper"
                        >
                            <p class="supplier-address">
                                {{ supplier?.address }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <div class="modal fade " id="delete-task" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <!-- <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5> -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="delete-warning">Are You Sure To Want  Delete This Supplier?</p>
                </div>
                <div class="modal-footer border-0 pt-0 flex-nowrap">
                    <button style="width: 50%; background: #fff; color: #000;" type="button"
                        class="apply-leave-btn"  data-bs-dismiss="modal" aria-label="Close">No</button>
                    <button style="width: 50%;" type="submit"  @click="deleteSupplier(selectedSupplierUuid)"  class="apply-leave-btn">Yes</button>
                    <form hidden>
                        <button type="submit" >Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useSupplierStore } from "../../stores/supplierStore";

const router = useRouter();
const supplierStore = useSupplierStore();
const selectedSupplierUuid = ref(null);

const editSupplier = (supplier) => {
    supplierStore.setSupplier(supplier);
    supplierStore.setMode("edit");
    supplierStore.setHref("board-view");
    const model = new bootstrap.Modal(
        document.getElementById("create-new-supplier")
    );
};

const setSupplierForDeletion = (uuid) => {
    selectedSupplierUuid.value = uuid;
};

defineProps({
    suppliers: {
        type: Array,
        default: () => [],
    },
});

const deleteSupplier = async (uuid) => {
    try {
        const response = await axios.delete(`/supplier/${uuid}`);
        if (response.data.error) {
            alert("Somthing went wrong");
        }
        window.location.href = '/supplier?board-view=true';

    } catch (error) {
        alert(error.message);
    }
};
</script>

<style scoped>
/* Main Container Styles */
.supplier-board {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.supplier-listing-main-container {
    padding: 15px;
    width: 31%; /* Set to 31% to fit 3 cards per row with some spacing */
    height: 328.24px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.1);
}

/* Supplier Listing Box */
.supplier-listing-box {
    height: 100%;
    /* height: 350px;  */
    padding: 10px 17px 17px 17px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.supplier-list-img {
    width: 100%;
    height: 116.41px;
    object-fit: cover;
    border-radius: 6px;
}



.dropdown-menu {
    box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.2);
}
.fs-14 {
    font-size: 14px;
}

@media (max-width: 1024px) {
    .supplier-listing-main-container {
        width: 48%;
    }
}

@media (max-width: 768px) {
    .supplier-listing-main-container {
        width: 100%;
    }
}

.supplier-address {
    font-size: 16px;
    word-wrap: break-word;
    white-space: normal;
    overflow: hidden;
    text-overflow: ellipsis;
    max-height: 3em;
    line-height: 1.5em;
}

.address-wrapper {
    max-width: 350px;
}
</style>
