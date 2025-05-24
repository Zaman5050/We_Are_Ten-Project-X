<template>
    <div class="d-flex align-items-center justify-content-between">
        <h2 class="signup-headings">Categories</h2>
        <button
            class="log-in"
            v-if="!isCreateNewCategoryEnable"
            @click="handleCreateNewCategory(true), resetFormData()"
        >
            Add New Category
        </button>
    </div>

    <div v-if="isCreateNewCategoryEnable">
        <form @submit.prevent="handleSubmit()">
            <div class="row">
                <div class="col-md-3">
                    <label for="exchange_rate" class="signup-labels"
                        >Name</label
                    >
                    <input
                        class="signup-inputs mw-100"
                        id="exchange_rate"
                        v-model="state.name"
                        type="text"
                        autocomplete="off"
                        @input="handleInputChange"
                    />
                    <input-validation-error-message :v="v$?.name" />
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-column">
                        <label class="signup-labels">Type</label>
                        <select
                            class="select"
                            v-model="state.type"
                            ref="categoryTypeRef"
                            :disabled="Object.keys(editedCategory).length > 0"
                        >
                            <option value="product" selected>Product</option>
                            <option value="material">Material</option>
                        </select>
                    </div>
                    <input-validation-error-message :v="v$?.type" />
                </div>
            </div>
            <div class="d-flex my-5 justify-content-center align-items-center">
                <button
                    class="btn btn-default"
                    @click="handleCreateNewCategory(false), resetFormData()"
                >
                    Cancel
                </button>
                <button class="log-in" type="submit" :disabled="isLoading">
                    Submit
                </button>
            </div>
        </form>
    </div>

    <div class="row">
        <div class="col-md-5 col-lg-5">
            <h4 class="fw-bold mb-3">Product Categories</h4>
            <div class="task-table-container table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th class="action">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="category in productCategories"
                            :key="category.uuid"
                        >
                            <td>{{ category?.name }}</td>
                            <td class="button">
                                <button
                                    class="btn btn-primary btn-sm fw-bold"
                                    @click="handleEditButtonClik(category)"
                                >
                                    Edit
                                </button>
                                <button
                                    class="btn btn-danger btn-sm fw-bold"
                                    @click="deleteCategory(category.uuid)"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr
                            class="table-body"
                            v-if="product_categories.length == 0"
                        >
                            <td class="d-none"></td>
                            <td colspan="2">No record found!</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="offset-2 col-md-5 col-lg-5">
            <div class="task-table-container table-responsive">
                <h4 class="fw-bold mb-3">Material Categories</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th class="action">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="category in materialCategories"
                            :key="category.uuid"
                        >
                            <td>{{ category?.name }}</td>
                            <td class="button">
                                <button
                                    class="btn btn-primary btn-sm fw-bold"
                                    @click="handleEditButtonClik(category)"
                                >
                                    Edit
                                </button>
                                <button
                                    class="btn btn-danger btn-sm fw-bold"
                                    @click="deleteCategory(category.uuid)"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr
                            class="table-body"
                            v-if="material_categories.length == 0"
                        >
                            <td class="d-none"></td>
                            <td colspan="2">No record found!</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, computed, nextTick, onMounted } from "vue";
import axios from "axios";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers, numeric } from "@vuelidate/validators";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { useToast } from "vue-toastification";

const props = defineProps({
    currencies: {
        type: Array,
        default: [],
    },
    product_categories: {
        type: Array,
        default: [],
    },
    material_categories: {
        type: Array,
        default: [],
    },
});

const state = reactive({
    name: "",
    type: "product",
});

const rules = computed(() => ({
    name: {
        required: helpers.withMessage("Category name is required.", required),
    },
    type: {
        required: helpers.withMessage("Category Type is required.", required),
    },
}));

const toast = useToast();
const serverSideErrors = reactive({});
const v$ = useVuelidate(rules, state, {
    $externalResults: serverSideErrors,
});
const productCategories = ref([]);
const materialCategories = ref([]);
const editedCategory = ref({});

const categoryTypeRef = ref(null);
const isCreateNewCategoryEnable = ref(false);
const isLoading = ref(false);

const initializeNiceSelect = () => {
    $(categoryTypeRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
    $(categoryTypeRef.value).niceSelect();

    $(categoryTypeRef.value).on("change", function () {
        state.type = $(this).val();
    });
};

const handleSubmit = async () => {
    try {
        v$.value.$clearExternalResults();
        v$.value.$touch(); // Mark fields as touched to show errors
        if (v$.value.$pending || v$.value.$invalid) return; // Wait for any async validation

        isLoading.value = true;
        const response =
            Object.keys(editedCategory.value).length === 0
                ? await axios.post(`/categories`, { ...state })
                : await axios.put(`/categories/${editedCategory.value.uuid}`, {
                      ...state,
                  });
        if (response.data) {
            isLoading.value = false;
            const category = response.data.category;
            if (Object.keys(editedCategory.value).length == 0) {
                category?.type === "product"
                    ? productCategories.value.push(category)
                    : materialCategories.value.push(category);
            } else {
                if (category?.type === editedCategory.value.type) {
                    if (category.type === "product") {
                        productCategories.value = productCategories.value.map(
                            (cat) => {
                                if (cat.uuid === category.uuid)
                                    return { ...category };
                                return cat;
                            }
                        );
                    } else {
                        materialCategories.value = materialCategories.value.map(
                            (cat) => {
                                if (cat.uuid === category.uuid)
                                    return { ...category };
                                return cat;
                            }
                        );
                    }
                } else {
                    if (category?.type === "product") {
                        materialCategories.value =
                            materialCategories.value.filter(
                                (cat) => cat.uuid !== category.uuid
                            );
                        productCategories.value.push(category);
                    } else {
                        productCategories.value =
                            productCategories.value.filter(
                                (cat) => cat.uuid !== category.uuid
                            );
                        materialCategories.value.push(category);
                    }
                }
            }

            toast.success(response?.data?.message || "Success!", {
                timeout: 3000,
                position: "top-right",
            });
            handleCreateNewCategory(false);
            resetFormData();
        }
    } catch (error) {
        isLoading.value = false;
        if (error.response.status === 500) {
            toast.error(error?.response?.data?.message || "Server Error!", {
                timeout: 3000,
                position: "top-right",
            });
        } else if (error.response.status === 422) {
            if (error.response?.data?.errors) {
                Object.assign(serverSideErrors, {
                    ...error.response?.data?.errors,
                });
            } else {
                console.error("Unexpected error:", error);
            }
        }
    }
};
const handleCreateNewCategory = async (value) => {
    isCreateNewCategoryEnable.value = value;
    if (value) {
        await nextTick();
        initializeNiceSelect(); // Re-initialize niceSelect after DOM update
    }
};
const resetFormData = () => {
    v$.value.$reset();
    v$.value.$clearExternalResults();
    state.name = "";
    state.type = "product";
    editedCategory.value = {};
};
onMounted(() => {
    productCategories.value = props.product_categories;
    materialCategories.value = props.material_categories;
});
const handleEditButtonClik = (category) => {
    editedCategory.value = category;
    state.name = category?.name;
    state.type = category?.type;
    handleCreateNewCategory(true);
};
const capitalizeEachWord = (str) => {
    if (!str) return str;
    return str
        .split(" ")
        .map(
            (word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
        )
        .join(" ");
};

const handleInputChange = () => {
    state.name = capitalizeEachWord(state.name);
    v$.$clearExternalResults(); // Keeps this functionality as needed
};
const deleteCategory = async (categoryUuid) => {
    if (!confirm("Are you sure you want to delete this category?")) return;

    try {
        const response = await axios.delete(`/categories`, {
            data: { category: categoryUuid },
        });
        toast.success(response.data.message);
        productCategories.value = productCategories.value.filter(
            (cat) => cat.uuid !== categoryUuid
        );
        materialCategories.value = materialCategories.value.filter(
            (cat) => cat.uuid !== categoryUuid
        );
    } catch (error) {
        toast.error(
            error.response?.data?.message || "Error deleting category."
        );
    }
};
</script>

<style scoped>
.category-item {
    display: flex;
    align-items: center;
    cursor: pointer;
    gap: 5px;
}
.category-item:hover {
    background-color: #f3f3f3;
}
.edit-icon {
    cursor: pointer;
}
.text-danger {
    color: red;
}
.button {
    display: flex;
    justify-content: space-around;
}
.action{
    text-align: center;
}
</style>
