@extends('layouts.tenant.index')

@section('title', 'Dashboard')

@section('content')
<!-- Main inner content -->
<tenant-dashboard :data="{{ json_encode($data) }}">
    <template v-slot:header>
        @can('isDesigner')
            <apply-leave-button :authuserleaves="{{ json_encode(auth()->user()->remaining_leaves) }}"/>
        @endcan
    </template>
    
    <template v-slot:create-btn>
        <x-project.create />
    </template>
         

</tenant-dashboard>
<!-- main content area end -->


<x-project.modal />

@endsection
@push('scripts')
    <script>
            $(document).ready(function() {

                document.addEventListener('DOMContentLoaded', function () {
                    document.getElementById('toggleButton').addEventListener('click', function() {
                        var rows = document.querySelectorAll('#myTable');
                        var icon = document.querySelector('.collaps-icon');
                        let shadowElement = document.getElementById('dashboard-collaps-container');
                        var button = this;

                        if (button.textContent === 'Expand') {
                            rows.forEach((row) => {
                                row.classList.remove('custom-collapsed');
                            });
                            icon.style.transform = 'rotate(180deg)';
                            button.textContent = 'Collapse';
                            shadowElement.classList.remove('shadow-container');
                        } else {
                            rows.forEach((row) => {
                                row.classList.add('custom-collapsed');
                            });
                            icon.style.transform = 'rotate(0deg)';
                            button.textContent = 'Expand';
                            shadowElement.classList.add('shadow-container');
                        }
                    });
                });


       
            })

    </script>
@endpush