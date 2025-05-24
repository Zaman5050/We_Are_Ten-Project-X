@extends('layouts.tenant.index')

@section('title', 'Supplier')

@section('content')
    <!-- Main inner content -->

    <div class="sales-report-area">
        <supplier :suppliers="{{ @$suppliers }}" :current-company="{{ $currentCompany }}" :categories="{{ @$categories }}" :timezones="{{ json_encode($timezones) }}" :currencies="{{ $activeCurrencies }}"></supplier>
    </div>
    <!-- main content area end -->
@endsection

@push('scripts')
<script>
$(document).on('click', '.editSupplier', function(e) {
    e.preventDefault();
    openModel('create-new-supplier');
});
function openModel(id) {
                var bsModel = document.getElementById(id);
                bsModel && new bootstrap.Modal(bsModel).show();
            }
</script>
    @endpush