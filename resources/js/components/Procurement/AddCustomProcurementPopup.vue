<template>
    <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="addProcurementCanvas"
        aria-labelledby="addProcurementCanvasLabel"
    >
        <div class="offcanvas-body add-material-ofcanvas-body pb-0">
            <vue-loader v-if="isLoading" />
            <h1 class="offcanvas3-heading">
                {{
                    state.product_name != ""
                        ? state.product_name
                        : "Product Name"
                }}
            </h1>
            <div
                class="dashboard-heading-container pb--20 d-flex justify-content-between align-items-center"
            >
                <div class="dashboard-button-container">
                    <button
                        :class="[
                            'toggle-button px-2',
                            activeTab === 'summary'
                                ? 'active-offcanvas-tab'
                                : '',
                        ]"
                        @click="setActiveTab('summary')"
                    >
                        Summary
                    </button>
                    <button
                        :class="[
                            'toggle-button px-2',
                            activeTab === 'financial'
                                ? 'active-offcanvas-tab'
                                : '',
                        ]"
                        @click="setActiveTab('financial')"
                    >
                        Financials
                    </button>
                    <button
                        :class="[
                            'toggle-button px-2',
                            activeTab === 'attachment'
                                ? 'active-offcanvas-tab'
                                : '',
                        ]"
                        @click="setActiveTab('attachment')"
                    >
                        Attachments
                    </button>
                    <button
                        :class="[
                            'toggle-button px-2',
                            activeTab === 'procurement-tab'
                                ? 'active-offcanvas-tab'
                                : '',
                        ]"
                        @click="setActiveTab('procurement-tab')"
                    >
                        Procurement
                    </button>
                </div>
            </div>

            <div
                style=""
                class="d-flex flex-column justify-content-between side-canvas-h"
            >
                <div>
                    <div class="content" v-show="activeTab === 'summary'">
                        <div class="row">
                            <div class="col-12">
                                <file-uploader
                                    @handleAttachmentUpload="handleImageUpload"
                                    :attachments="state.cover_image"
                                    :sub-path="`projects/${route.params.project}/procurements`"
                                    :upload_url="'/file/upload'"
                                    :delete_url="'/file/delete'"
                                    :acceptedFileTypes="'image/png, image/jpeg, image/gif'"
                                    :disabled="mode === 'add-to'"
                                />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="">
                                    <label class="signup-labels"
                                        >Product name</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.product_name"
                                    />
                                    <input-validation-error-message
                                        :v="v$?.product_name"
                                    />
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="">
                                    <label class="signup-labels"
                                        >Product description</label
                                    >
                                    <textarea
                                        style="resize: none"
                                        class="signup-inputs mw-100 p-description py-2"
                                        placeholder=""
                                        v-model="state.description"
                                        >{{ state.description }}</textarea
                                    >
                                </div>
                            </div>

                            <div
                                class="col-4"
                                v-if="
                                    module === 'procurement' || mode == 'add-to'
                                "
                            >
                                <div class="">
                                    <label class="signup-labels"
                                        >quantity</label
                                    >
                                    <input
                                        type="number"
                                        min="1"
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.quantity"
                                    />
                                    <input-validation-error-message
                                        :v="v$?.quantity"
                                    />
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="">
                                    <label class="signup-labels"
                                        >brand name</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.brand_name"
                                    />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="">
                                    <label class="signup-labels">SKU</label>
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.sku"
                                    />
                                    <input-validation-error-message
                                        :v="v$?.sku"
                                    />
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="">
                                    <label class="signup-labels"
                                        >product url</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.product_url"
                                    />
                                </div>
                            </div>
                        </div>
                        <div
                            class="row"
                            v-if="suppliers.length && !isNewSupplierEnable"
                        >
                            <div class="col-8">
                                <div class="">
                                    <label class="signup-labels"
                                        >supplier</label
                                    >
                                    <div
                                        class="create-new-project-select-container select-full"
                                    >
                                        <select
                                            class="select"
                                            v-model="state.supplier_library_id"
                                            ref="supplierRef"
                                        >
                                            <option
                                                v-for="supplier in suppliers"
                                                :key="supplier.uuid"
                                                :value="supplier.id"
                                            >
                                                {{ supplier.name }}
                                            </option>
                                        </select>
                                        <input-validation-error-message
                                            v-if="!isNewSupplierEnable"
                                            :v="v$.supplier_library_id"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div
                                class="col-4 d-flex justify-content-end align-items-end"
                            >
                                <span
                                    class="create-one d-block"
                                    @click="handleAddNewSuppplierClick(true)"
                                >
                                    Add Custom Supplier</span
                                >
                            </div>
                        </div>

                        <div
                            class="row"
                            v-if="!suppliers.length || isNewSupplierEnable"
                        >
                            <div class="col-6">
                                <h5 class="offcanvas-heading">
                                    Supplier information
                                </h5>
                            </div>
                            <div
                                class="col-6 d-flex align-items-center justify-content-end"
                            >
                                <div
                                    class="collaps-container"
                                    v-if="suppliers.length"
                                >
                                    <button
                                        class="collaps-expand-btn"
                                        @click="
                                            handleAddNewSuppplierClick(false)
                                        "
                                    >
                                        Cancel
                                    </button>
                                    <img
                                        class="collaps-icon"
                                        src="/assets/images/arrow-icon.svg"
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels"
                                        >Company Name</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.supplier.company_name"
                                        @blur="
                                            isNewSupplierEnable && v$.$touch()
                                        "
                                    />
                                    <input-validation-error-message
                                        v-if="isNewSupplierEnable"
                                        :v="v$.supplier.company_name"
                                        :disabled="mode === 'add-to'"
                                        @blur="
                                            isNewSupplierEnable && v$.$touch()
                                        "
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels"
                                        >Contact Name</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.supplier.name"
                                        @blur="
                                            isNewSupplierEnable && v$.$touch()
                                        "
                                    />
                                    <input-validation-error-message
                                        v-if="isNewSupplierEnable"
                                        :v="v$.supplier?.name"
                                        :disabled="mode === 'add-to'"
                                        @blur="
                                            isNewSupplierEnable && v$.$touch()
                                        "
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels"
                                        >Email Address</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.supplier.email"
                                        @blur="
                                            isNewSupplierEnable && v$.$touch()
                                        "
                                    />
                                    <input-validation-error-message
                                        v-if="isNewSupplierEnable"
                                        :v="v$?.supplier?.email"
                                        :disabled="mode === 'add-to'"
                                        @blur="
                                            isNewSupplierEnable && v$.$touch()
                                        "
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels"
                                        >Phone Number</label
                                    >
                                    <vue-tel-input
                                        @on-input="
                                            (phoneString, obj) => (telObj = obj)
                                        "
                                        :validCharactersOnly="true"
                                        class="signup-inputs mw-100"
                                        id="phone_number"
                                        name="phone_number"
                                        v-model="state.supplier.phone_number"
                                        @keydown.enter.prevent
                                        :disabled="mode === 'add-to'"
                                        @blur="
                                            isNewSupplierEnable && v$.$touch()
                                        "
                                    />
                                    <input-validation-error-message
                                        v-if="isNewSupplierEnable"
                                        :v="v$?.supplier?.phone_number"
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels">Address</label>
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.supplier.address"
                                        @blur="
                                            isNewSupplierEnable && v$.$touch()
                                        "
                                    />
                                    <input-validation-error-message
                                        v-if="isNewSupplierEnable"
                                        :v="v$?.supplier?.address"
                                        :disabled="mode === 'add-to'"
                                        @blur="
                                            isNewSupplierEnable && v$.$touch()
                                        "
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div
                                    class="create-new-project-select-container"
                                >
                                    <label class="signup-labels"
                                        >Items Sold</label
                                    >
                                    <multiselect
                                        v-model="state.supplier_category_id"
                                        :options="
                                            computedCategoriesProcurementBudget ||
                                            []
                                        "
                                        placeholder="Select one"
                                        label="name"
                                        track-by="id"
                                        class="select"
                                        :multiple="true"
                                        open-direction="bottom"
                                        :taggable="true"
                                        @tag="handleAddStage"
                                        @remove="handleRemoveStage"
                                        :disabled="mode === 'add-to'"
                                    ></multiselect>
                                </div>
                                <input-validation-error-message
                                    v-if="isNewSupplierEnable"
                                    :v="v$?.supplier_category_id"
                                    :disabled="mode === 'add-to'"
                                    @blur="isNewSupplierEnable && v$.$touch()"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h5 class="offcanvas-heading">
                                    Product Specification
                                </h5>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels">height</label>
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.height"
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels">depth</label>
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.depth"
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels">width</label>
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.width"
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels">length</label>
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.length"
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels">color</label>
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.color"
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels">finish</label>
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.finish"
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels"
                                        >material</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder="Rope & Teak"
                                        v-model="state.material"
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels"
                                        >Product Category</label
                                    >
                                    <div
                                        class="create-new-project-select-container"
                                        v-if="mode !== 'add-to'"
                                    >
                                        <select
                                            class="select"
                                            v-model="state.category_id"
                                            ref="categoryRef"
                                        >
                                            <option
                                                v-for="category in computedProjectProcurementBudget"
                                                :key="category?.category?.uuid"
                                                :value="category?.category?.id"
                                            >
                                                {{ category?.category?.name }}
                                            </option>
                                        </select>
                                        <input-validation-error-message
                                            :v="v$?.category_id"
                                        />
                                    </div>
                                    <input
                                        v-else
                                        class="signup-inputs mw-100"
                                        placeholder=""
                                        v-model="state.category"
                                        :disabled="mode === 'add-to'"
                                    />
                                </div>
                            </div>
                            <div
                                class="col-6"
                                v-if="
                                    !isNewAreaEnable &&
                                    ((module === 'product-library' &&
                                        mode === 'add-to') ||
                                        module === 'procurement') &&
                                    computedProjectAreas.length > 0
                                "
                            >
                                <div class="">
                                    <label class="signup-labels"
                                        >Project Area</label
                                    >
                                    <div
                                        class="create-new-project-select-container"
                                    >
                                        <select
                                            class="select"
                                            v-model="state.project_area_id"
                                            ref="projectAreaIdRef"
                                            id="project_area_id"
                                        >
                                            <option
                                                v-for="area in computedProjectAreas"
                                                :key="area.uuid"
                                                :value="area.id"
                                            >
                                                {{ area.area_name }}
                                            </option>
                                        </select>
                                        <input-validation-error-message
                                            :v="v$?.project_area_id"
                                        />
                                        <div class="col-12">
                                            <a
                                                class="create-one d-block"
                                                @click="
                                                    handleAddProjectArea(true)
                                                "
                                            >
                                                Add Another Area
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="row"
                                v-if="
                                    (((module === 'product-library' &&
                                        mode === 'add-to') ||
                                        module === 'procurement') &&
                                        computedProjectAreas.length === 0) ||
                                    isNewAreaEnable
                                "
                            >
                                <div class="col-6 pb-3">
                                    <h6 class="offcanvas-heading">
                                        Project Area Information
                                    </h6>
                                </div>
                                <div
                                    class="col-6 d-flex align-items-center justify-content-end pb-3"
                                >
                                    <div
                                        class="collaps-container"
                                        v-if="computedProjectAreas.length"
                                    >
                                        <button
                                            class="collaps-expand-btn"
                                            @click="handleAddProjectArea(false)"
                                        >
                                            Cancel
                                        </button>
                                        <img
                                            class="collaps-icon"
                                            src="{{ asset('assets/images/arrow-icon.svg') }}"
                                        />
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="">
                                        <label class="signup-labels"
                                            >AREA NAME
                                        </label>
                                        <input
                                            class="signup-inputs mw-100"
                                            placeholder="Enter Area Name"
                                            v-model="state.area.area_name"
                                        />
                                    </div>
                                    <input-validation-error-message
                                        :v="v$?.area.area_name"
                                    />
                                </div>
                                <div class="col-4">
                                    <div class="">
                                        <label class="signup-labels"
                                            >DIMENSIONS</label
                                        >

                                        <div class="input-group mb-3">
                                            <input
                                                type="text"
                                                class="signup-inputs mw-100"
                                                placeholder="Enter Dimensions"
                                                aria-label="Recipient's username"
                                                aria-describedby="basic-addon2"
                                                v-model="
                                                    state.area.area_dimention
                                                "
                                            />
                                            <input-validation-error-message
                                                :v="v$?.area.area_dimention"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="">
                                        <label class="signup-labels"
                                            >AREA CODE</label
                                        >
                                        <input
                                            class="signup-inputs mw-100"
                                            placeholder="Enter Area Code"
                                            v-model="state.area.area_code"
                                        />
                                    </div>
                                    <input-validation-error-message
                                        :v="v$?.area.area_code"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="offcanvas-heading mb-0">
                                    Custom Specification
                                </h5>
                            </div>
                            <div
                                class="row d-flex align-items-center mt-3"
                                v-for="customSpecification in state.custom_specifications"
                                :key="customSpecification.uuid"
                            >
                                <div class="col-4">
                                    <div class="">
                                        <label class="signup-labels"
                                            >label</label
                                        >
                                        <input
                                            class="signup-inputs mw-100"
                                            placeholder=""
                                            v-model="customSpecification.label"
                                        />
                                    </div>
                                </div>

                                <div class="col-7">
                                    <div class="">
                                        <label class="signup-labels"
                                            >description</label
                                        >
                                        <input
                                            class="signup-inputs mw-100"
                                            placeholder=""
                                            v-model="
                                                customSpecification.custom_description
                                            "
                                        />
                                    </div>
                                </div>
                                <div class="col-1">
                                    <button
                                        style="
                                            filter: invert(1);
                                            margin-top: 24px;
                                        "
                                        class="border-0 bg-transparent"
                                        @click="
                                            handleRemoveCustomSpecificationClick(
                                                customSpecification.uuid
                                            )
                                        "
                                        type="button"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            fill="currentColor"
                                            class="bi bi-trash text-white"
                                            viewBox="0 0 16 16"
                                        >
                                            <path
                                                d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"
                                            />
                                            <path
                                                d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="col-12">
                                <a
                                    class="create-one mb-4 d-block"
                                    @click="handleAddCustomSpecification"
                                    :disabled="mode === 'add-to'"
                                >
                                    Add New Specification
                                </a>
                            </div>
                            <div class="col-12">
                                <div class="h-auto mb-3">
                                    <label class="signup-labels">notes</label>
                                    <textarea
                                        style="
                                            height: 105px;
                                            resize: none;
                                            padding-top: 20px;
                                        "
                                        class="signup-inputs py-2 mw-100"
                                        placeholder="Enter notes or additional information."
                                        v-model="state.notes"
                                        >{{ state.notes }}</textarea
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="content" v-show="activeTab === 'financial'">
                        <div class="row">
                            <div class="col-4">
                                <div class="">
                                    <label class="signup-labels"
                                        >Buying currency</label
                                    >
                                    <div
                                        class="create-new-project-select-container"
                                    >
                                        <select
                                            class="select"
                                            v-model="state.quote_currency_id"
                                            ref="quoteCurrencyIdRef"
                                            :disabled="mode === 'edit'"
                                        >
                                            <option
                                                v-for="currency in currencies"
                                                :key="currency.uuid"
                                                :value="currency.id"
                                            >
                                                {{ currency.symbol }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="signup-labels"
                                        >Retail price</label
                                    >
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span
                                                class="input-group-text signup-inputs"
                                                id="basic-addon2"
                                            >
                                                {{
                                                    getCurrencySymbol(
                                                        state.quote_currency_id
                                                    )
                                                }}
                                            </span>
                                        </div>
                                        <input
                                            type="text"
                                            class="form-control signup-inputs mw-100"
                                            placeholder="0"
                                            aria-label="Retail Price"
                                            aria-describedby="basic-addon2"
                                            v-model="state.retail_price"
                                        />
                                    </div>
                                    <input-validation-error-message
                                        :v="v$?.retail_price"
                                    />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="signup-labels"
                                        >Trade discount</label
                                    >
                                    <div class="input-group mb-3">
                                        <input
                                            type="text"
                                            class="form-control signup-inputs mw-100"
                                            placeholder="0"
                                            aria-label="Trade discount0"
                                            aria-describedby="basic-addon2"
                                            v-model="state.trade_discount"
                                        />
                                        <div class="input-group-append">
                                            <span
                                                class="input-group-text signup-inputs"
                                                id="basic-addon2"
                                                >%
                                            </span>
                                        </div>
                                    </div>
                                    <input-validation-error-message
                                        :v="v$?.trade_discount"
                                    />
                                </div>
                            </div>
                            <div
                                class="col-4"
                                v-if="
                                    module === 'procurement' ||
                                    mode === 'add-to'
                                "
                            >
                                <div class="">
                                    <label class="signup-labels"
                                        >Base currency</label
                                    >
                                    <div
                                        class="create-new-project-select-container"
                                    >
                                        <input
                                            class="signup-inputs mw-100"
                                            readonly
                                            placeholder=""
                                            :value="state.base_currency_symbol"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="">
                                    <label class="signup-labels"
                                        >Upfront Amount</label
                                    >
                                    <div class="d-flex mb-3 position-relative">
                                        <input
                                            type="text"
                                            style="
                                                border-radius: 6px 0 0 6px;
                                                padding: 0 9px;
                                            "
                                            class="signup-inputs mw-100"
                                            placeholder="Upfront 50"
                                            aria-label="Trade discount0"
                                            aria-describedby="basic-addon2"
                                            v-model="state.trade_terms"
                                        />
                                        <div class="input-group-append">
                                            <span
                                                class="input-group-text signup-inputs"
                                                id="basic-addon2"
                                                >%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4" v-if="!isAuthUserDesigner">
                                <div class="">
                                    <label class="signup-labels">Markup</label>
                                    <div class="d-flex mb-3 position-relative">
                                        <input
                                            type="text"
                                            style="
                                                border-radius: 6px 0 0 6px;
                                                padding: 0 9px;
                                            "
                                            class="signup-inputs mw-100"
                                            placeholder="0"
                                            aria-label="Trade discount0"
                                            aria-describedby="basic-addon2"
                                            v-model="state.markup"
                                        />
                                        <div class="input-group-append">
                                            <span
                                                class="input-group-text signup-inputs"
                                                id="basic-addon2"
                                                >%
                                            </span>
                                        </div>
                                    </div>
                                    <input-validation-error-message
                                        :v="v$?.markup"
                                    />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="">
                                    <label class="signup-labels"
                                        >Exchange Rate</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder="0.0"
                                        v-model="state.exchange_rate"
                                        disabled
                                    />
                                    <input-validation-error-message
                                        :v="v$?.exchange_rate"
                                    />
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="signup-labels"
                                        >Trade price</label
                                    >
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span
                                                class="input-group-text signup-inputs"
                                                id="basic-addon2"
                                                >{{
                                                    getCurrencySymbol(
                                                        state.quote_currency_id
                                                    )
                                                }}
                                            </span>
                                        </div>
                                        <input
                                            type="text"
                                            class="form-control signup-inputs mw-100"
                                            placeholder="0"
                                            aria-label="Trade Price"
                                            aria-describedby="basic-addon2"
                                            v-model="tradePrice"
                                            disabled
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mb-3">
                                    <label class="signup-labels"
                                        >Actual price</label
                                    >
                                    <div class="input-group">
                                        <div class="input-group-append">
                                            <span
                                                class="input-group-text signup-inputs"
                                                id="basic-addon2"
                                                >{{
                                                    getCurrencySymbol(
                                                        state.base_currency_id
                                                    )
                                                }}
                                            </span>
                                        </div>
                                        <input
                                            type="text"
                                            class="form-control signup-inputs mw-100"
                                            placeholder="0"
                                            aria-label="Actual Price"
                                            aria-describedby="basic-addon2"
                                            v-model="actualPrice"
                                            disabled
                                        />
                                        <input-validation-error-message
                                            :v="v$?.actualPrice"
                                        />
                                        <div class="col-12">
                                            <input-validation-error-message
                                                :v="v$?.totalCost"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="!isAuthUserDesigner">
                            <div
                                class="col-4"
                                v-if="
                                    module === 'procurement' || mode == 'add-to'
                                "
                            >
                                <div class="">
                                    <label class="signup-labels"
                                        >Total Ext VAT</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder="0.5"
                                        v-model="totalExclusiveTax"
                                        disabled
                                    />
                                </div>
                            </div>
                            <div
                                class="col-4"
                                v-if="
                                    module === 'procurement' || mode == 'add-to'
                                "
                            >
                                <div class="">
                                    <label class="signup-labels"
                                        >Total Incl VAT</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder="0.5"
                                        v-model="totalInclusiveTax"
                                        disabled
                                    />
                                </div>
                            </div>
                            <div
                                class="col-4"
                                v-if="
                                    module === 'procurement' ||
                                    mode === 'add-to'
                                "
                            >
                                <div class="">
                                    <label class="signup-labels"
                                        >Profit Per Unit</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        placeholder="0.5"
                                        v-model="profit"
                                        disabled
                                    />
                                </div>
                            </div>
                            <div
                                class="col-4"
                                v-if="
                                    module === 'procurement' ||
                                    mode === 'add-to'
                                "
                            >
                                <div class="">
                                    <label class="signup-labels"
                                        >Total Profit
                                    </label>
                                    <input
                                        class="signup-inputs mw-100"
                                        type="text"
                                        :value="totalProfit"
                                        disabled
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="content" v-show="activeTab === 'attachment'">
                        <file-uploader
                            @handleAttachmentUpload="handleAttachmentUpload"
                            :attachments="state.attachments"
                            :sub-path="`projects/${route.params.project}/documents`"
                            :upload_url="'/file/upload'"
                            :delete_url="'/file/delete'"
                            :acceptedFileTypes="'.dwg, .pdf, .doc, .docx, .csv, .txt, .zip, .xml, image/png, image/jpeg, image/gif'"
                            multiple
                            :disabled="mode === 'add-to'"
                        />
                    </div>

                    <div
                        class="content"
                        v-show="activeTab === 'procurement-tab'"
                    >
                        <div class="row">
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels"
                                        >production lead time</label
                                    >
                                    <div class="d-flex mb-3 position-relative">
                                        <input
                                            min="1"
                                            type="number"
                                            class="signup-inputs mw-100"
                                            placeholder="0"
                                            v-model="state.production_lead_time"
                                        />

                                        <div class="input-group-append">
                                            <span
                                                class="input-group-text signup-inputs"
                                                id="basic-addon2"
                                                >Weeks
                                            </span>
                                        </div>
                                    </div>
                                    <input-validation-error-message
                                        :v="v$?.production_lead_time"
                                    />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="">
                                    <label class="signup-labels"
                                        >shipping lead time</label
                                    >
                                    <div class="d-flex mb-3 position-relative">
                                        <input
                                            min="1"
                                            type="number"
                                            class="signup-inputs mw-100"
                                            placeholder="0"
                                            v-model="state.shipping_lead_time"
                                        />

                                        <div class="input-group-append">
                                            <span
                                                class="input-group-text signup-inputs"
                                                id="basic-addon2"
                                                >Weeks
                                            </span>
                                        </div>
                                    </div>
                                    <input-validation-error-message
                                        :v="v$?.shipping_lead_time"
                                    />
                                </div>
                            </div>
                            <div
                                class="col-6"
                                v-if="
                                    module === 'procurement' ||
                                    mode === 'add-to'
                                "
                            >
                                <div class="">
                                    <label class="signup-labels"
                                        >Order date</label
                                    >
                                    <flat-pickr
                                        v-model="state.order_date"
                                        placeholder="DD/MM/YYYY"
                                        :config="DateConfig"
                                        :class="[
                                            'signup-inputs flatpickr-input mw-100 ',
                                        ]"
                                    />
                                </div>
                                <input-validation-error-message
                                    :v="v$?.order_date"
                                />
                            </div>
                            <div
                                class="col-6"
                                v-if="
                                    module === 'procurement' ||
                                    mode === 'add-to'
                                "
                            >
                                <div class="shipping-date-container">
                                    <label class="signup-labels"
                                        >shipping date</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        type="text"
                                        :value="state.shipping_date"
                                        disabled
                                    />
                                </div>
                                <input-validation-error-message
                                    :v="v$?.shipping_date"
                                />
                            </div>
                            <div
                                class="col-6"
                                v-if="
                                    module === 'procurement' ||
                                    mode === 'add-to'
                                "
                            >
                                <div class="">
                                    <label class="signup-labels"
                                        >delivery date</label
                                    >
                                    <input
                                        class="signup-inputs mw-100"
                                        type="text"
                                        :value="state.delivery_date"
                                        disabled
                                    />
                                </div>
                                <input-validation-error-message
                                    :v="v$?.delivery_date"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="">
                        <button
                            style="max-width: 400px; margin: 0 auto 20px auto"
                            class="log-in"
                            type="button"
                            @click="handleSubmit"
                            :disabled="isLoading"
                        >
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, onMounted, ref, computed, watch, nextTick } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import { v4 as uuidv4 } from "uuid";
import { useVuelidate } from "@vuelidate/core";
import { required, helpers, maxLength, email } from "@vuelidate/validators";
import FileUploader from "../Common/FileUploader.vue";
import InputValidationErrorMessage from "../Common/InputValidationErrorMessage.vue";
import { useProcurementStore } from "../../stores/procurmentStore";
import { storeToRefs } from "pinia";
import VueLoader from "../Common/Loader.vue";
import flatPickr from "vue-flatpickr-component";
import "vue-tel-input/vue-tel-input.css";
import { useUserStore } from "../../stores/userStore";
import { computeTimezoneDate } from "@/helpers.js";
import moment from "moment-timezone";
import { useToast } from "vue-toastification";
import Multiselect from "vue-multiselect";

const props = defineProps({
    suppliers: {
        type: Array,
        default: [],
    },
    currencies: {
        type: Array,
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
    project: {
        type: Object,
        default: {},
    },
});

const procurmentStore = useProcurementStore();
const {
    procurement,
    mode,
    module,
    categories: allCategories,
} = storeToRefs(procurmentStore);

const activeTab = ref("summary");
const isNewSupplierEnable = ref(false);
const isNewAreaEnable = ref(false);
const isSupplierCategoriesSelectEnable = ref(true);
const supplierRef = ref(null);
const categoryRef = ref(null);
const quoteCurrencyIdRef = ref(null);
const baseCurrencyIdRef = ref(null);
const projectAreaIdRef = ref(null);
const supplierCategoryRef = ref(null);
const customSpecificationObject = ref({
    label: "",
    custom_description: "",
});
const exchangeRates = ref([]);
const telObj = ref(null);
const isLoading = ref(false);

const route = useRoute();

const state = reactive({
    cover_image: [],
    project_id: "",
    category_id: "",
    category: "",
    supplier_library_id: "",
    project_area_id: "",
    product_name: "",
    description: "",
    sku: "",
    brand_name: "",
    product_url: "",
    height: "",
    depth: "",
    width: "",
    length: "",
    color: "",
    finish: "",
    quantity: "",
    custom_specifications: [],
    supplier: {
        company_name: "",
        name: "",
        email: "",
        phone_number: "",
        address: "",
    },
    material: "",
    quote_currency_id: "",
    base_currency_symbol: props.project.currency?.symbol || "£",
    base_currency_id: props.project?.currency_id,
    exchange_rate: "",
    retail_price: "",
    trade_discount: 0,
    trade_terms: "",
    production_lead_time: "",
    shipping_lead_time: "",
    order_date: "",
    shipping_date: "",
    delivery_date: "",
    notes: "",
    attachments: [],
    markup: 0,
    supplier_category_id: "",
    actual_price: 0,
    actual_quantity: 0,
    area: {
        area_name: "",
        area_dimention: "",
        area_code: "",
    },
    actualPrice: "",
});

const userStore = useUserStore();
const { authUser } = storeToRefs(userStore);
const isAuthUserDesigner = ref(false);
const toast = useToast();

const actualPrice = computed(() => {
    const price =
        parseFloat(tradePrice.value) / parseFloat(state.exchange_rate || 1);
    const result = (
        price + (state.markup === "" ? 0 : price * (state.markup / 100))
    ).toFixed(2);
    state.actualPrice = result;
    return result;
});

const totalCost = computed(() => {
    const quantity = parseFloat(state.quantity || 0);
    return (quantity * parseFloat(actualPrice.value) || 0).toFixed(2); // Total cost for validation
});

const selectedCategoryAmount = computed(() => {
    const procuremenBugets =
        module.value === "product-library"
            ? procurement.value.procurement_budget
            : props.project.procurement_budget;
    const selectedCategory = procuremenBugets?.find(
        (category) => category.category_id === state.category_id
    );
    if (!selectedCategory) return 0;

    // Use 'let' because 'adjustedRemaining' will be conditionally reassigned
    let adjustedRemaining = 0;

    if (module.value === "procurement" && mode.value === "edit") {
        adjustedRemaining =
            parseFloat(selectedCategory.remaining || 0) +
            parseFloat(state.actual_price || 0) *
                parseFloat(state.quantity || 0);
    } else {
        adjustedRemaining =
            parseFloat(selectedCategory.remaining || 0) +
            parseFloat(state.actual_price || 0);
    }
    if (module.value === "product-library" && mode.value === "add-to") {
        adjustedRemaining = parseFloat(selectedCategory.remaining || 0);
    }
    return adjustedRemaining;
});

const selectedCategoryQuantity = computed(() => {
    const procuremenBugets =
        module.value === "product-library"
            ? procurement.value.procurement_budget
            : props.project.procurement_budget;
    const selectedCategory = procuremenBugets?.find(
        (category) => category.category_id === state.category_id
    );
    if (!selectedCategory) return 0;
    const adjustedQuantity =
        parseFloat(selectedCategory.remainingQuantity || 0) +
        parseFloat(state.actual_quantity || 0);
    return adjustedQuantity;
});

const rules = computed(() => ({
    product_name: {
        required: helpers.withMessage("Product name is required.", required),
    },
    sku: {
        required: helpers.withMessage("SKU is required.", required),
    },
    quantity:
        (module.value === "product-library" && mode.value === "add-to") ||
        module.value === "procurement"
            ? {
                  required: helpers.withMessage(
                      "Quantity is required.",
                      required
                  ),
                  min: helpers.withMessage(
                      "Quantity cannot be negative.",
                      (value) => value >= 0
                  ),
                  max: helpers.withMessage(
                      () =>
                          selectedCategoryQuantity.value > 0
                              ? `Quantity cannot exceed the remaining quantity of ${selectedCategoryQuantity.value}.`
                              : "No budget is available for the selected category.",
                      (value) => value <= selectedCategoryQuantity.value
                  ),
              }
            : {},
    retail_price:
        module.value === "procurement"
            ? {
                  required: helpers.withMessage(
                      "Retail price is required.",
                      required
                  ),
              }
            : {},
    trade_discount:
        module.value === "procurement"
            ? {
                  required: helpers.withMessage(
                      "Trade discount is required.",
                      required
                  ),
              }
            : {},
    production_lead_time: {
        required: helpers.withMessage(
            "Production lead time is required.",
            required
        ),
        min: helpers.withMessage(
            "Production lead time cannot be negative.",
            (value) => value >= 0
        ),
    },
    shipping_lead_time: {
        required: helpers.withMessage(
            "Shipping lead time is required.",
            required
        ),
        min: helpers.withMessage(
            "Shipping lead time cannot be negative.",
            (value) => value >= 0
        ),
    },
    order_date:
        module.value === "procurement" || mode.value === "add-to"
            ? {
                  required: helpers.withMessage(
                      "Order Date is required.",
                      required
                  ),
              }
            : {},
    shipping_date:
        module.value === "procurement" || mode.value === "add-to"
            ? {
                  notExceedDueDate: helpers.withMessage(
                      "Shipping date must not exceed the project due date",
                      (value) => {
                          if (!value) return true;
                          const dueDate = moment(
                              module.value === "product-library" &&
                                  mode.value === "add-to"
                                  ? procurement.value.project_due_date
                                  : props.project.due_date,
                              "YYYY-MM-DD"
                          );
                          const shippingDate = moment(value, "DD/MM/YYYY");
                          return shippingDate.isSameOrBefore(dueDate);
                      }
                  ),
              }
            : {},
    delivery_date:
        module.value === "procurement" || mode.value === "add-to"
            ? {
                  notExceedDueDate: helpers.withMessage(
                      "Delivery date must not exceed the project due date",
                      (value) => {
                          if (!value) return true;
                          const dueDate = moment(
                              module.value === "product-library" &&
                                  mode.value === "add-to"
                                  ? procurement.value.project_due_date
                                  : props.project.due_date,
                              "YYYY-MM-DD"
                          );
                          const deliveryDate = moment(value, "DD/MM/YYYY");
                          return deliveryDate.isSameOrBefore(dueDate);
                      }
                  ),
              }
            : {},
    project_area_id:
        (module.value === "procurement" || mode.value === "add-to") &&
        !isNewAreaEnable.value
            ? {
                  required: helpers.withMessage(
                      "Project Area is required.",
                      required
                  ),
              }
            : {},
    category_id: {
        required: helpers.withMessage("Category is required.", required),
    },
    supplier_category_id: isNewSupplierEnable.value
        ? {
              required: helpers.withMessage("Category is required.", required),
          }
        : {},
    supplier_library_id: !isNewSupplierEnable.value
        ? {
              required: helpers.withMessage("Supplier is required.", required),
          }
        : {},
    supplier: {
        company_name: isNewSupplierEnable.value
            ? {
                  required: helpers.withMessage(
                      "Company name is required.",
                      required
                  ),
                  maxLength: maxLength(255),
              }
            : {},
        name: isNewSupplierEnable.value
            ? {
                  required: helpers.withMessage(
                      "Contact name is required.",
                      required
                  ),
                  maxLength: maxLength(255),
              }
            : {},
        email: isNewSupplierEnable.value
            ? {
                  required: helpers.withMessage("Email is required.", required),
                  email: helpers.withMessage("Invalid email format.", email),
                  maxLength: maxLength(255),
              }
            : {},
        phone_number: isNewSupplierEnable.value
            ? {
                  required: helpers.withMessage(
                      "Phone number is required.",
                      required
                  ),
                  maxLength: maxLength(15),
                  handlePhoneNumberValidation: helpers.withMessage(
                      "Invalid phone number format.",
                      (value) => {
                          return telObj.value?.valid || !value;
                      }
                  ),
              }
            : {},
        address: isNewSupplierEnable.value
            ? {
                  required: helpers.withMessage(
                      "Address is required.",
                      required
                  ),
                  maxLength: maxLength(255),
              }
            : {},
    },
    area: {
        area_code: isNewAreaEnable.value
            ? {
                  required: helpers.withMessage(
                      "Area Code is required.",
                      required
                  ),
                  maxLength: maxLength(255),
              }
            : {},
        area_dimention: isNewAreaEnable.value
            ? {
                  required: helpers.withMessage(
                      "Area Dimensions is required.",
                      required
                  ),
                  maxLength: maxLength(255),
              }
            : {},
        area_name: isNewAreaEnable.value
            ? {
                  required: helpers.withMessage(
                      "Area Name is required.",
                      required
                  ),
                  maxLength: maxLength(255),
              }
            : {},
    },
    markup: isAuthUserDesigner.value
        ? {}
        : {
              required: helpers.withMessage("Markup is required.", required),
          },
    actualPrice:
        module.value === "procurement"
            ? {
                  required: helpers.withMessage(
                      "Actual price is required.",
                      required
                  ),
              }
            : {},
    totalCost: {
        max: helpers.withMessage(
            () =>
                selectedCategoryAmount.value > 0
                    ? `The total cost (${totalCost.value}) cannot exceed the remaining budget of ${selectedCategoryAmount.value}.`
                    : "No budget is available for the selected category.",
            () => totalCost.value <= selectedCategoryAmount.value
        ),
    },
    exchange_rate:
        module.value === "procurement" || mode.value === "add-to"
            ? {
                  required: helpers.withMessage(
                      "Exchange Rate is required.",
                      required
                  ),
              }
            : {},
}));

const v$ = useVuelidate(rules, state);
const setActiveTab = (tab) => (activeTab.value = tab);

const setPoundCurrencyAsDefault = () => {
    if (!quoteCurrencyIdRef.value) {
        if (props.currencies.length > 0) {
            const poundCurrency = props.currencies.find(
                (currency) => currency.symbol === "£"
            );

            state.quote_currency_id =
                poundCurrency?.id ||
                props.project?.currency_id ||
                props.currencies[0].id;

            $(quoteCurrencyIdRef.value).val(state.quote_currency_id);

            $(quoteCurrencyIdRef.value).niceSelect();
        }
        return;
    }

    try {
        $(quoteCurrencyIdRef.value).niceSelect("destroy");
    } catch (error) {
        console.warn("Error destroying niceSelect:", error);
    }
    if (props.currencies.length > 0) {
        const poundCurrency = props.currencies.find(
            (currency) => currency.symbol === "£"
        );

        state.quote_currency_id =
            poundCurrency?.id ||
            props.project?.currency_id ||
            props.currencies[0].id;

        $(quoteCurrencyIdRef.value).val(state.quote_currency_id);

        $(quoteCurrencyIdRef.value).niceSelect();
    }
};
const computedCurrency = computed(() => {
    if (state.quote_currency_id === "") {
        setPoundCurrencyAsDefault();
    }

    const quoteCurrency = props.currencies.find(
        (currency) => currency.id === state.quote_currency_id
    );

    if (module.value === "product-library") {
        return quoteCurrency?.base_currency_exchange_rates;
    }

    return props.project?.currency?.base_currency_exchange_rates;
});
onMounted(async () => {
    isAuthUserDesigner.value =
        authUser.value.roles.findIndex((role) => role.name === "designer") !==
        -1;
    initializeNiceSelect();
    handleAddCustomSpecification();
    setPoundCurrencyAsDefault();
    setExchangeRateInState();
    if (props.project_areas.length > 0) {
        state.project_area_id = props.project_areas[0].id;
        $(projectAreaIdRef).val(state.project_area_id);
    }
    getExchangeRateFromProjectBaseCurrency(computedCurrency.value);
    setExchangeRateInState();
    if (
        ((module.value === "product-library" && mode.value === "add-to") ||
            module.value === "procurement") &&
        computedProjectAreas.value.length === 0
    ) {
        isNewAreaEnable.value = true;
    } else {
        isNewAreaEnable.value = false;
    }
    if (!props.suppliers.length) {
        isNewSupplierEnable.value = true;
    } else {
        isNewSupplierEnable.value = false;
    }
});

const initializeNiceSelect = () => {
    $(supplierRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
    $(supplierRef.value).niceSelect();

    $(supplierCategoryRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
    $(supplierCategoryRef.value).niceSelect();

    $(categoryRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
    $(categoryRef.value).niceSelect();
    if (
        module.value === "procurement" ||
        module.value === "product-library" ||
        mode.value === "add-to"
    ) {
        $(quoteCurrencyIdRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
        $(quoteCurrencyIdRef.value).niceSelect();

        $(baseCurrencyIdRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
        $(baseCurrencyIdRef.value).niceSelect();

        $(quoteCurrencyIdRef.value).on("change", function () {
            state.quote_currency_id = parseInt($(this).val());
            if (!(module.value == "procurement" && mode.value == "edit")) {
                setExchangeRateInState();
            }
        });
    }

    $(projectAreaIdRef.value).niceSelect("destroy"); // Destroy existing instance to avoid duplicates
    $(projectAreaIdRef.value).niceSelect();
    if (state.supplier_library_id) {
        $(supplierRef.value)
            .val(state.supplier_library_id)
            .niceSelect("update");
    }
    if (state.category_id) {
        $(categoryRef.value).val(state.category_id).niceSelect("update");
    }
    // Initialize NiceSelect
    $(supplierRef.value).on("change", function () {
        state.supplier_library_id = parseInt($(this).val());
    });
    $(categoryRef.value).on("change", function () {
        state.category_id = parseInt($(this).val());
    });

    $(projectAreaIdRef.value).on("change", function () {
        state.project_area_id = parseInt($(this).val());
    });
    $(supplierCategoryRef.value).on("change", function () {
        state.supplier_category_id = parseInt($(this).val());
    });
};

const handleAddCustomSpecification = () => {
    state.custom_specifications = [
        ...state.custom_specifications,
        Object.assign(
            {},
            { ...customSpecificationObject.value, uuid: uuidv4() }
        ),
    ];
};

const handleRemoveCustomSpecificationClick = (uuid) => {
    state.custom_specifications = state.custom_specifications.filter(
        (customSpecification) => customSpecification.uuid !== uuid
    );
};

const handleAddNewSuppplierClick = async (value) => {
    isNewSupplierEnable.value = value;
    isSupplierCategoriesSelectEnable.value = !value;
    state.supplier_library_id = "";
    state.supplier = {
        company_name: "",
        name: "",
        email: "",
        phone_number: "",
        address: "",
    };
    await nextTick();
    initializeNiceSelect();
};

const getCurrencySymbol = (currencyId) => {
    if (!currencyId) return "£";
    return props.currencies.find((currency) => currency.id === currencyId)
        ?.symbol;
};

const handleSubmit = async () => {
    try {
        v$.value.$touch(); // Mark fields as touched to show errors
        if (v$.value.$pending) return; // Wait for any async validation

        if (v$.value.$invalid) {
            const isAreaInvalid = !isNewAreaEnable
                ? v$.value.project_area_id?.$invalid
                : v$.value.area.area_code?.$invalid ||
                  v$.value.area.area_dimention?.$invalid ||
                  v$.value.area.area_name?.$invalid;
            if (
                v$.value.product_name?.$invalid ||
                v$.value.sku?.$invalid ||
                v$.value.quantity?.$invalid ||
                isAreaInvalid ||
                v$.value.category_id?.$invalid ||
                v$.value.supplier_library_id?.$invalid ||
                v$.value.supplier_category_id?.$invalid
            ) {
                setActiveTab("summary");
            } else if (
                v$.value.retail_price?.$invalid ||
                v$.value.trade_discount?.$invalid ||
                v$.value.markup?.$invalid ||
                v$.value.exchange_rate?.$invalid ||
                v$.value.actualPrice?.$invalid
            ) {
                setActiveTab("financial");
            } else if (
                v$.value.production_lead_time?.$invalid ||
                v$.value.shipping_lead_time?.$invalid ||
                v$.value.order_date?.$invalid ||
                v$.value.shipping_date?.$invalid ||
                v$.value.delivery_date?.$invalid
            ) {
                setActiveTab("procurement-tab");
            }
            return false;
        }

        let response;
        const data = {
            ...state,
        };
        isLoading.value = true;
        if (module.value === "procurement") {
            if (mode.value === "create") {
                response = await axios.post(
                    `/projects/${route.params.project}/procurements`,
                    { ...data }
                );
            } else if (mode.value === "edit") {
                response = await axios.put(
                    `/projects/${route.params.project}/procurements/${[
                        procurement.value.uuid,
                    ]}`,
                    {
                        ...data,
                        product_uuid: procurement?.value?.product?.uuid,
                    }
                );
            }
        } else if (module.value === "product-library") {
            if (mode.value === "create") {
                response = await axios.post(`/product-library`, {
                    ...data,
                });
            } else if (mode.value === "edit") {
                response = await axios.put(
                    `/product-library/${procurement?.value?.uuid}`,
                    {
                        ...data,
                    }
                );
            } else if (mode.value === "add-to") {
                const payload = {
                    ...state,
                    product_library_id: procurement.value?.id,
                    quantity: data.quantity,
                    project_area_ids: data.project_area_id,
                };
                response = await axios.post(
                    `/projects/${procurement?.value?.project_uuid}/add-procurement-from-library`,
                    payload
                );
            }
        }
        if (response.data) {
            isLoading.value = false;
            window.location.reload();
        }
    } catch (error) {
        isLoading.value = false;
        console.error("Error creating project:", error);
    }
};

watch(
    () => procurement.value,
    (newVal) => {
        if (
            ((module.value === "product-library" && mode.value === "add-to") ||
                module.value === "procurement") &&
            computedProjectAreas.value.length === 0
        ) {
            isNewAreaEnable.value = true;
        } else {
            isNewAreaEnable.value = false;
        }
        if (!props.suppliers.length) {
            isNewSupplierEnable.value = true;
        } else {
            isNewSupplierEnable.value = false;
        }
        nextTick(() => initializeNiceSelect());

        if (mode.value == "create") {
            resetState();
            setPoundCurrencyAsDefault();
            setActiveTab("summary");
        } else {
            v$.value.$reset();
            setActiveTab("summary");
            const product =
                module.value === "procurement" ? newVal?.product : newVal;
            state.cover_image = product?.cover_image;
            state.product_name = product?.product_name;
            state.description = product?.description;
            state.sku = product?.sku;
            state.brand_name = product?.brand_name;
            state.product_url = product?.product_url;
            state.height = product?.height;
            state.depth = product?.depth;
            state.width = product?.width;
            state.length = product?.length;
            state.color = product?.color;
            state.finish = product?.finish;
            state.material = product?.material;
            state.notes = product?.notes;
            state.custom_specifications = product?.specifications;
            state.attachments = product?.attachments;
            state.supplier_library_id = parseInt(product?.supplier?.id);
            state.trade_discount = newVal?.trade_discount;
            state.retail_price = newVal?.retail_price;
            state.trade_terms = newVal?.trade_terms;
            state.production_lead_time = newVal?.production_lead_time;
            state.shipping_lead_time = newVal?.shipping_lead_time;
            state.markup = newVal?.markup;
            state.actual_price = newVal?.actual_price;
            state.actual_quantity = newVal?.quantity;
            state.category_id = product.category?.id;
            state.exchange_rate = newVal?.exchange_rate;
            state.quote_currency_id = parseInt(newVal?.quote_currency_id);
            if (
                !(module.value === "product-library" && mode.value === "edit")
            ) {
                const baseCurrency = props.currencies.find(
                    (currency) =>
                        currency.id === parseFloat(newVal?.base_currency_id)
                );
                state.base_currency_id = parseInt(baseCurrency?.id);
                state.base_currency_symbol = baseCurrency?.symbol;
            }
            if (module.value === "procurement") {
                state.quantity = newVal?.quantity;
                state.order_date = new Date(newVal?.order_date);
                state.shipping_date = new Date(newVal?.shipping_date);
                state.delivery_date = new Date(newVal?.delivery_date);
                state.project_area_id = parseInt(newVal?.project_area_id);
                state.quote_currency_id = parseInt(newVal?.quote_currency_id);
                state.exchange_rate = newVal?.exchange_rate;
            } else if (
                module.value === "product-library" &&
                mode.value === "add-to"
            ) {
                if (props.currencies.length > 0) {
                    const poundCurrency = props.currencies.find(
                        (currency) => currency.symbol === "£"
                    );
                    state.base_currency_id =
                        poundCurrency?.id ||
                        props.project?.currency_id ||
                        props.currencies[0].id;
                    state.base_currency_symbol = poundCurrency?.symbol;
                }
                getExchangeRateFromProjectBaseCurrency(
                    newVal?.base_currency_exchange_rates
                );
                setExchangeRateInState();
            }
            if (product.category) {
                state.category = product.category?.name;
            }
        }
    },
    { deep: true }
);

const resetState = () => {
    state.cover_image = [];
    state.project_id = "";
    state.category_id = "";
    state.category = "";
    state.supplier_library_id = "";
    state.project_area_id = "";
    state.product_name = "";
    state.description = "";
    state.sku = "";
    state.brand_name = "";
    state.product_url = "";
    state.height = "";
    state.depth = "";
    state.width = "";
    state.length = "";
    state.color = "";
    state.finish = "";
    state.quantity = "";
    state.custom_specifications = [];
    state.supplier = Object.assign(
        {},
        {
            company_name: "",
            name: "",
            email: "",
            phone_number: "",
            address: "",
        }
    );
    state.material = "";
    state.quote_currency_id = "";
    (state.base_currency_symbol = props.project.currency?.symbol || "£"),
        (state.base_currency_id = props.project?.currency_id);
    state.exchange_rate = "";
    state.retail_price = "";
    state.trade_discount = 0;
    state.trade_terms = "";
    state.production_lead_time = "";
    state.shipping_lead_time = "";
    state.order_date = "";
    state.shipping_date = "";
    state.delivery_date = "";
    state.notes = "";
    state.attachments = [];
    state.markup = 0;
    state.area = Object.assign(
        {},
        {
            area_name: "",
            area_dimention: "",
            area_code: "",
        }
    );
    state.actualPrice = "";
    v$.value.$reset();
    setExchangeRateInState();
};

const handleAttachmentUpload = (ids) => {
    state.attachments = [...new Set(ids.filter((x) => !!x))];
};

const handleImageUpload = (ids) => {
    state.cover_image = ids;
};

const DateConfig = computed(() => {
    return {
        dateFormat: "d/m/Y",
        minDate: computeTimezoneDate(
            module.value === "product-library" && mode.value === "add-to"
                ? procurement.value.project_start_date
                : props.project.start_date
        ),
        maxDate: computeTimezoneDate(
            module.value === "product-library" && mode.value === "add-to"
                ? procurement.value.project_due_date
                : props.project.due_date
        ),
        onDayCreate: function (dObj, dStr, fp, dayElem) {
            const min = new Date(fp.config.minDate);
            const max = new Date(fp.config.maxDate);
            const dayDate = new Date(dayElem.dateObj);

            if (dayDate < min || dayDate > max) {
                dayElem.classList.add("custom-disabled-tooltip");
                dayElem.setAttribute(
                    "title",
                    "Please select a date within project date range"
                );
            }
        },
    };
});


const computedProjectAreas = computed(() =>
    module.value === "product-library" && mode.value === "add-to"
        ? procurement?.value?.areas
        : props.project_areas
);

const computedProjectProcurementBudget = computed(() =>
    module.value === "product-library"
        ? allCategories.value.map((category) =>
              Object.assign({}, { category: category })
          )
        : props.project.procurement_budget
);
const computedCategoriesProcurementBudget = computed(() =>
    module.value === "product-library"
        ? allCategories.value.map((category) => ({
              id: category.id,
              name: category.name,
          }))
        : props.project.procurement_budget?.map((item) => ({
              id: item.category?.id,
              name: item.category?.name,
          })) || []
);

// Computed property to get the maximum amount of the selected category

const tradePrice = computed(() =>
    (
        parseFloat(state.retail_price) -
            parseFloat(state.retail_price) *
                (parseFloat(state.trade_discount) / 100) || 0
    ).toFixed(2)
);

const totalExclusiveTax = computed(
    () =>
        (
            parseFloat(actualPrice.value) * parseFloat(state.quantity || 1)
        ).toFixed(2) || 0
);
const totalInclusiveTax = computed(
    () =>
        (
            parseFloat(totalExclusiveTax.value) +
            parseFloat(totalExclusiveTax.value) * (5 / 100)
        ).toFixed(2) || 0
);

const profit = computed(() => {
    let operator;
    if (state.exchange_rate < 1) {
        operator = "*";
    } else {
        operator = "/";
    }

    const actualPriceFloat = parseFloat(actualPrice.value);
    const tradePriceFloat = parseFloat(tradePrice.value);
    const exchangeRate = parseFloat(state.exchange_rate || 1);

    // Perform the operation
    let result = 0;
    if (operator === "*") {
        result = actualPriceFloat - (tradePriceFloat * exchangeRate).toFixed(2);
    } else if (operator === "/") {
        result = actualPriceFloat - (tradePriceFloat / exchangeRate).toFixed(2);
    }

    // Return the result rounded to two decimal places
    return result.toFixed(2) || 0;
});

watch(
    () => [
        state.order_date,
        state.production_lead_time,
        state.shipping_lead_time,
    ],
    () => {
        const orderDateMoment = moment(state.order_date, "DD/MM/YYYY"); // Parse the date using the right format
        if (orderDateMoment.isValid() && state.production_lead_time != null) {
            const orderDate = orderDateMoment.toDate();

            const productionLeadTime = parseInt(state.production_lead_time, 10);
            const shippingLeadTime = parseInt(
                state.shipping_lead_time || 0,
                10
            ); // Default to 0 if undefined

            const shippingDate = new Date(orderDate);
            shippingDate.setDate(orderDate.getDate() + productionLeadTime * 7);

            const deliveryDate = new Date(shippingDate);
            if (shippingLeadTime === 0) {
                deliveryDate.setTime(shippingDate.getTime());
            } else {
                deliveryDate.setDate(
                    shippingDate.getDate() + shippingLeadTime * 7
                );
            }

            const formattedShippingDate =
                moment(shippingDate).format("DD/MM/YYYY");
            const formattedDeliveryDate =
                moment(deliveryDate).format("DD/MM/YYYY");

            state.shipping_date = formattedShippingDate;
            state.delivery_date = formattedDeliveryDate;
        }
    },
    { immediate: true }
);
const getExchangeRateFromProjectBaseCurrency = (rates) => {
    try {
        if (rates) {
            exchangeRates.value = rates.reduce((acc, item) => {
                const key = item.quote_currency.code;
                const value = parseFloat(item.exchange_rate); // Convert exchange_rate to a number
                acc[key] = value;
                return acc;
            }, {});
        }
    } catch (error) {
        console.error(
            `error in getExchangeRateFromProjectBaseCurrency: ${error}`
        );
    }
};
const setExchangeRateInState = () => {
    if (state.quote_currency_id === "") {
        setPoundCurrencyAsDefault();
    }
    if (state.base_currency_id === state.quote_currency_id) {
        state.exchange_rate = 1;
        return;
    }

    const quoteCurrency = props.currencies.find(
        (currency) => currency.id === state.quote_currency_id
    );

    if (!quoteCurrency) {
        toast.error("Selected currency is invalid.", {
            timeout: 3000,
            position: "top-right",
        });
        setPoundCurrencyAsDefault();
        return;
    }
    if (quoteCurrency.code == "GBP") {
        state.exchange_rate = 1;
    } else {
        if (Object.keys(exchangeRates.value).length > 0) {
            const rate = exchangeRates.value[quoteCurrency.code];
            if (rate) {
                state.exchange_rate = rate;
            } else {
                state.exchange_rate = "";
                toast.error(
                    `Exchange rate for ${quoteCurrency.code} is not available.`,
                    {
                        timeout: 3000,
                        position: "top-right",
                    }
                );
                setPoundCurrencyAsDefault();
            }
        } else {
            state.exchange_rate = "";
            toast.error("Exchange rates are not available.", {
                timeout: 3000,
                position: "top-right",
            });
            setPoundCurrencyAsDefault();
        }
    }
};

const totalProfit = computed(
    () =>
        (parseFloat(profit.value) * parseFloat(state.quantity || 1)).toFixed(
            2
        ) || 0
);

const handleAddProjectArea = async (value) => {
    isNewAreaEnable.value = value;
    state.project_area_id = "";
    state.area = {
        area_name: "",
        area_dimention: "",
        area_code: "",
    };
    nextTick(() => initializeNiceSelect());
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style scoped lang="scss">
.side-canvas-h {
    height: -webkit-calc(100% - (127px));
}
[class^="col-"] {
    margin: 8px 0;
}

</style>
