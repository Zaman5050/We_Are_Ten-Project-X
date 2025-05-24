@props([
    'class' => '',
    'title' => '',
    'count' => 0,
])

<div {{ $attributes->merge(['class' => 'col-md-2 col-sm-6 mb-3 '.$class]) }}>
    <p class="box-text text-uppercase mb-2">{{ $title }} </p>
    <div class="dashboard-box">
        <div class="text-center">
            <h1 class="box-heading">{{ $count }}</h1>
        </div>
    </div>
</div>