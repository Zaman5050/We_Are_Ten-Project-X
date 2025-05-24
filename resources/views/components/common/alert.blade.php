@props([
    'class' => '',
    'message' => '',
])


@if($message)
    <div {{ $attributes->merge(['class' => 'alert alert-'.$class]) }}>
        {{ $message }}
    </div>
@endif