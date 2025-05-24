@props([
    'label' => '',
    'id' => '',
    'name' => '',
    'class' => '',
    'wrapClass' => '',
    'labelClass' => '',
])

@php
    $id = $id ?: $name;
@endphp

<div {{ $attributes->merge(['class' => 'sign-up-input-container ' . $wrapClass]) }}>
    <label {{ $attributes->merge(['class' => 'signup-labels ' . $labelClass]) }}
        for="{{ $id }}">{{ $label }}</label>

    <div class="create-new-project-select-container">
        <select id="{{ $id }}" name="{{ $name }}" 
        class="select @error('{{ $name }}') is-invalid @enderror {{ $class }}" >
             {{ $slot }}
        </select>
        @error('{{ $name }}')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
