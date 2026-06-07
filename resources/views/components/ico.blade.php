@props(['name' => 'square-3-stack-3d', 'variant' => 'outline'])

@php
    $iconName = ($variant === 'solid' ? 'heroicon-s-' : 'heroicon-o-') . $name;
@endphp

{!! svg($iconName, $attributes->get('class', ''), $attributes->except('class')->getAttributes())->toHtml() !!}
