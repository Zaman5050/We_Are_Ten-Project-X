@props([
    'name' => '?',
    'background' => randomColor(),
    'uuid' => '',
    'active' => false
])

<svg class="{{ 'member-img-icon avatar '. ($active ? 'active' : '') }}"  {{ $uuid ? "data-avatar-uuid={$uuid}" : '' }}
xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
    viewBox="0 0 24 24" version="1.1">
    <circle fill="{{ $background }}" cx="12" width="24" height="24" cy="12" r="12" /><text x="50%"
        y="50%" class="avatar-text" alignment-baseline="middle" text-anchor="middle" font-size="10" font-weight="600"
        dy=".1em" dominant-baseline="middle" fill="#fff">{{ $name }}</text>
</svg>