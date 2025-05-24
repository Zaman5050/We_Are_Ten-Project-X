@props([
    'label' => '',
    'id' => '',
    'name' => '',
    'placeholder' => '',
    'type' => 'text',
    'class' => '',
    'value' => '',
    'wrapClass' => '',
    'labelClass' => '',
])

@php
    $id = $id ?: $name;
    $placeholder = $placeholder ?: $label;
@endphp

<div {{ $attributes->merge(['class' => 'sign-up-input-container ' . $wrapClass]) }}>
    <label for="{{ $id }}"
        {{ $attributes->merge(['class' => 'signup-labels ' . $labelClass]) }}>{{ $label }}</label>
    <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        class="signup-inputs @error($name) is-invalid @enderror {{ $class }}" placeholder="{{ $placeholder }}">
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
