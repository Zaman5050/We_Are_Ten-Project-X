<template>
    <div
        class="modal fade"
        id="create-new-supplier"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
        tabindex="-1"
        aria-labelledby="staticBackdropLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button
                    @click="closeEvent()"
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
                </div>
                <div class="modal-body pt-0">
                    <form @submit.prevent="handleSubmit()" id="addEntryForm">
                        <input
                            type="text"
                            id="company_name"
                            v-model="state.company_name"
                            :class="[
                                'create-project-title mb-3',
                                {
                                    'is-invalid':
                                        checkValidation('company_name'),
                                },
                            ]"
                            placeholder="Enter Company Name"
                            @keydown.enter.prevent
                        />
                        <ValidationError
                            :errors="errors"
                            field="company_name"
                        />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label for="name" class="signup-labels"
                                        >Contact Name</label
                                    >
                                    <input
                                        type="text"
                                        id="name"
                                        v-model="state.name"
                                        :class="[
                                            'signup-inputs py-2 mw-100',
                                            {
                                                'is-invalid':
                                                    checkValidation('name'),
                                            },
                                        ]"
                                        placeholder="Enter Contact Name"
                                        @keydown.enter.prevent
                                    />
                                    <ValidationError
                                        :errors="errors"
                                        field="name"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label for="email" class="signup-labels"
                                        >Email Address</label
                                    >
                                    <input
                                        type="text"
                                        id="email"
                                        v-model="state.email"
                                        :class="[
                                            'signup-inputs py-2 mw-100',
                                            {
                                                'is-invalid':
                                                    checkValidation('email'),
                                            },
                                        ]"
                                        placeholder="Enter Email Address"
                                        @keydown.enter.prevent
                                    />
                                    <ValidationError
                                        :errors="errors"
                                        field="email"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label for="address" class="signup-labels"
                                        >Address</label
                                    >
                                    <input
                                        type="text"
                                        id="address"
                                        v-model="state.address"
                                        :class="[
                                            'signup-inputs py-2 mw-100',
                                            {
                                                'is-invalid':
                                                    checkValidation('address'),
                                            },
                                        ]"
                                        placeholder="Enter Address"
                                        @keydown.enter.prevent
                                    />
                                    <ValidationError
                                        :errors="errors"
                                        field="address"
                                    />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label
                                        for="phone_number"
                                        class="signup-labels"
                                        >Phone Number</label
                                    >
                                    <input
                                        type="text"
                                        id="phone_number"
                                        v-model="state.phone_number"
                                        :class="[
                                            'signup-inputs py-2 mw-100',
                                            {
                                                'is-invalid':
                                                    checkValidation(
                                                        'phone_number'
                                                    ),
                                            },
                                        ]"
                                        placeholder="Enter Phone Number"
                                        @keydown.enter.prevent
                                    />
                                    <ValidationError
                                        :errors="errors"
                                        field="phone_number"
                                    />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label class="signup-labels">
                                        Currency</label
                                    >
                                    <div
                                        class="create-new-project-select-container select-full"
                                    >
                                        <select
                                            ref="currencyRef"
                                            v-model="state.currency"
                                            :class="[
                                                'select',
                                                {
                                                    'is-invalid':
                                                        checkValidation(
                                                            'currency_id'
                                                        ),
                                                },
                                            ]"
                                        >
                                            <option value="" disabled>
                                                Select a currency
                                            </option>
                                            <option
                                                v-for="currency in currencies"
                                                :key="currency.id"
                                                :value="currency.id"
                                            >
                                                {{ currency.code }} ({{
                                                    currency.symbol
                                                }})
                                            </option>
                                        </select>
                                        <ValidationError
                                            :errors="errors"
                                            field="currency_id"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label
                                        for="trade_discount"
                                        class="signup-labels"
                                        >Trade Discount</label
                                    >
                                    <input
                                        type="text"
                                        id="trade_discount"
                                        v-model="state.trade_discount"
                                        :class="[
                                            'signup-inputs py-2 mw-100',
                                            {
                                                'is-invalid':
                                                    checkValidation(
                                                        'trade_discount'
                                                    ),
                                            },
                                        ]"
                                        placeholder="Enter Trade Discount"
                                        @keydown.enter.prevent
                                    />
                                    <ValidationError
                                        :errors="errors"
                                        field="trade_discount"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label class="signup-labels" for="timezone"
                                        >Timezone</label
                                    >
                                    <div
                                        class="create-new-supplier-select-container"
                                    >
                                        <multiselect
                                            v-model="state.timezone"
                                            :options="props.timezones"
                                            placeholder="Enter Select one"
                                            label="label"
                                            track-by="value"
                                            :multiple="false"
                                            open-direction="bottom"
                                            :class="[
                                                {
                                                    'is-invalid':
                                                        checkValidation(
                                                            'timezone'
                                                        ),
                                                },
                                            ]"
                                        >
                                        </multiselect>
                                        <ValidationError
                                            :errors="errors"
                                            field="timezone"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label for="website" class="signup-labels"
                                        >Website</label
                                    >
                                    <input
                                        type="text"
                                        id="website"
                                        v-model="state.website"
                                        :class="[
                                            'signup-inputs py-2 mw-100',
                                            {
                                                'is-invalid':
                                                    checkValidation('website'),
                                            },
                                        ]"
                                        placeholder="Enter Website"
                                        @keydown.enter.prevent
                                    />
                                    <ValidationError
                                        :errors="errors"
                                        field="website"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="sign-up-input-container mb-4 h-auto"
                                >
                                    <div class="upload__box-only-name">
                                        <div
                                            v-if="!imagePreview"
                                            class="upload__btn-box-only-name"
                                        >
                                            <label
                                                role="button"
                                                for="addAttachment"
                                            >
                                                <img
                                                    src="/assets/images/add-img-icon.svg"
                                                />
                                                &nbsp;
                                                <u>Add Image</u>
                                            </label>

                                            <input
                                                id="addAttachment"
                                                type="file"
                                                @change="onFileChange"
                                                accept="'.dwg, image/png, image/jpg, image/jpeg, image/gif'"
                                                :class="[
                                                    'upload__inputfile-only-name',
                                                    {
                                                        'is-invalid':
                                                            checkValidation(
                                                                'profile_file'
                                                            ),
                                                    },
                                                ]"
                                                hidden
                                            />
                                        </div>

                                        <div
                                            v-if="imagePreview"
                                            class="upload__img-wrap-only-name"
                                        >
                                            <div class="img-container">
                                                <!-- Display blob image -->
                                                <img
                                                    :src="imagePreview"
                                                    alt="Selected Image"
                                                    class="img-block"
                                                />

                                                <!-- Cross button to remove the image -->
                                                <button
                                                    type="button"
                                                    @click.prevent="deleteImage"
                                                    class="remove-image-button"
                                                >
                                                    &times;
                                                </button>
                                            </div>
                                        </div>
                                        <ValidationError
                                            :errors="errors"
                                            field="profile_file"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sign-up-input-container">
                                    <label class="signup-labels" for="itemSold"
                                        >Item Sold</label
                                    >
                                    <div
                                        class="create-new-supplier-select-container"
                                    >
                                        <multiselect
                                            v-model="state.itemSold"
                                            :options="props.categories"
                                            placeholder="Select one"
                                            label="name"
                                            track-by="id"
                                            :multiple="true"
                                            open-direction="bottom"
                                            :taggable="true"
                                            @tag="handleAddStage"
                                            @remove="handleRemoveStage"
                                            :class="[
                                                {
                                                    'is-invalid':
                                                        checkValidation(
                                                            'itemSold'
                                                        ),
                                                },
                                            ]"
                                        >
                                        </multiselect>
                                        <ValidationError
                                            :errors="errors"
                                            field="itemSold"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div
                    class="modal-footer border-0 pt-2 pb-4 justify-content-start flex-column align-items-start"
                >
                    <button
                        :disabled="loading"
                        class="log-in"
                        type="submit"
                        form="addEntryForm"
                    >
                        <IconSpinner v-show="loading" />&nbsp;&nbsp;Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    computed,
    ref,
    defineProps,
    watch,
    onMounted,
    nextTick,
    reactive,
} from "vue";
import axios from "axios";
import ValidationError from "../ValidationError.vue";
import Multiselect from "vue-multiselect";
import IconSpinner from "../IconSpinner.vue";
import { useToast } from "vue-toastification";
import { useSupplierStore } from "../../stores/supplierStore";
import { storeToRefs } from "pinia";
const supplierStore = useSupplierStore();
const { supplier, mode, href } = storeToRefs(supplierStore);

const checkValidation = (field) => {
    return errors.value?.[field]?.length;
};

const currencyRef = ref(null);
const errors = reactive({});
const loading = ref(false);
const imagePreview = ref(null);
const profileImage = ref(null);
const toast = useToast();

function resetForm() {
    state.name = "";
    state.company_name = "";
    state.action = "";
    state.email = "";
    state.address = "";
    state.trade_discount = "";
    state.currency = "";
    state.timezone = "";
    currencyRef.value = "";
    state.itemSold = "";
    state.website = "";
    state.phone_number = "";
    errors.value = {};
    imagePreview.value = null;
    state.attachments = [];
    state.profile_picture = "";
}
const closeEvent = () => {
    resetForm();
};

watch(
    () => supplier.value,
    (newVal) => {
        if (mode.value == "create") resetForm();
        else {
            state.name = newVal.name;
            state.email = newVal.email;
            state.address = newVal.address;
            state.trade_discount = newVal.trade_discount;
            state.currency = newVal.currency_id;
            state.timezone = props.timezones.find(
                (tz) => tz.value === newVal.timezone
            );
            currencyRef.value = newVal.currency_id;

            state.itemSold = props.categories.filter((category) =>
                newVal.categories.some((cat) => cat.id === category.id)
            );
            state.website = newVal.website;
            state.phone_number = newVal.phone_number;
            imagePreview.value = newVal.profile_picture;
            state.profile_picture = newVal.profile_picture;
            state.company_name = newVal.company_name;
            nextTick(() => {
                setInitialState();
            });
        }
    },
    { deep: true }
);

const setInitialState = () => {
    if (currencyRef.value) {
        $(currencyRef.value).niceSelect("destroy");
        $(currencyRef.value).niceSelect();

        $(currencyRef.value).val(state.currency);
        $(currencyRef.value).niceSelect();
        $(currencyRef.value).on("change", function () {
            state.currency = $(this).val();
        });
    }
};

const state = reactive({
    company_name: "",
    name: "",
    email: "",
    address: "",
    phone_number: "",
    currency: "",
    trade_discount: "",
    timezone: "",
    attachment: null,
    website: "",
    profile_picture: "",
    itemSold: "",
    action: "",
});

const props = defineProps({
    action: "",
    timezones: {
        type: Object,
        required: true,
        default: [],
    },
    currencies: {
        type: Array,
        default: {},
    },
    categories: {
        type: Object,
        required: true,
        default: [],
    },
    currentCompany: {
        type: Object,
        required: true,
        default: {},
    },
});

const onFileChange = (event) => {
    const fileInput = event.target;
    const file = event.target.files[0];
    const MAX_FILE_SIZE = 20 * 1024 * 1024; // 20MB

    if (file) {
        if (file.size > MAX_FILE_SIZE) {
            // Show a toast notification
            toast.error("You cannot upload an image larger than 20MB.", {
                timeout: 3000,
                position: "top-right",
            });
            fileInput.value = "";
            return;
        }
        profileImage.value = file;
        imagePreview.value = URL.createObjectURL(file); // Create preview URL
    }
};

const deleteImage = () => {
    profileImage.value = null;
    imagePreview.value = null;
};

onMounted(() => {
    imagePreview.value = props.profile_picture ?? null;
    setInitialState();
});

const handleAddStage = (newCategoryName) => {
    let existingCategory = props.categories.find(
        (category) =>
            category.name.toLowerCase() === newCategoryName.toLowerCase()
    );

    if (existingCategory) {
        state.itemSold = existingCategory;
    } else {
        const newCategory = {
            name: newCategoryName,
            company_id: props.currentCompany.id, // Adjust as necessary
        };

        props.categories.push(newCategory);

        state.itemSold.push(newCategory);
    }
};

const handleRemoveStage = (item) => {
    if (item && item.id) {
        state.itemSold = state.itemSold.filter(
            (selectedItem) => selectedItem.id !== item.id
        );
    } else {
        console.error("Unexpected item format:", item);
    }
};

// Form submission function
const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};
    try {
        const formData = {
            name: state.name,
            email: state.email,
            address: state.address,
            phone_number: state.phone_number,
            currency_id: state.currency,
            trade_discount: state.trade_discount,
            timezone: state.timezone.value,
            website: state.website,
            itemSold: state.itemSold,
            company_id: props.currentCompany.id,
            company_name: state.company_name,
        };
        if (profileImage.value) {
            formData.profile_file = profileImage.value;
        }

        let apiUrl = "/supplier/store";
        if (mode.value == "edit") {
            apiUrl = `/supplier/update/${supplier.value.uuid}`;
        }

        const response = await axios.post(apiUrl, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
        loading.value = false;
        if (response.status === 200) {
            resetForm();
            if (href.value == "board-view") {
                window.location.assign("/supplier?board-view=true");
            } else {
                window.location.assign("/supplier");
            }
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
            console.error("Validation errors:");
        } else {
            console.error("Error saving supplier:", error);
        }
    } finally {
        loading.value = false;
    }
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style scoped>
.multiselect__tag {
    background: #252c32 !important;
}
.create-new-supplier-select-container {
    max-width: 800px;
}
.create-project-title {
    width: 100%;
    border: none;
}
</style>
