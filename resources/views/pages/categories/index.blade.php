@extends('layouts.tenant.index')

@section('content')
    <!-- Main inner content -->
    <div class="sales-report-area">
        <category :product_categories="{{ json_encode($productCategories) }}"
            :material_categories="{{ json_encode($materialCategories) }}" />
    </div>
    <!-- main content area end -->
@endsection
