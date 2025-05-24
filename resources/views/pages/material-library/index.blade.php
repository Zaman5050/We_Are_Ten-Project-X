@extends('layouts.tenant.index')

@section('title', 'Material Library')

@section('content')

    <material-library :materials="{{ json_encode($allCompanyMaterials) }}" :suppliers="{{ json_encode($suppliers) }}"
        :projects="{{ json_encode($projects) }}" :categories="{{ json_encode($categories) }}" />

@endsection
