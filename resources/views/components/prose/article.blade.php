@props(['heritage' => false, 'date' => null, 'dropCap' => true])

@php
    $proseClass = 'prose-site' . ($dropCap ? '' : ' prose-site--no-dropcap');
    if ($heritage) {
        $proseClass .= ' prose-heritage !mx-0 !max-w-none !bg-transparent !p-0 !shadow-none !ring-0';
    }
@endphp

@if ($heritage)
    <x-heritage-frame :date="$date">
        <div {{ $attributes->merge(['class' => $proseClass]) }}>
            {{ $slot }}
        </div>
    </x-heritage-frame>
@else
    <div {{ $attributes->merge(['class' => $proseClass]) }}>
        {{ $slot }}
    </div>
@endif