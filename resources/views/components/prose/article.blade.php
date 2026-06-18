@props(['heritage' => false, 'date' => null])

@if ($heritage)
    <x-heritage-frame :date="$date">
        <div {{ $attributes->merge(['class' => 'prose-site prose-heritage !mx-0 !max-w-none !bg-transparent !p-0 !shadow-none !ring-0']) }}>
            {{ $slot }}
        </div>
    </x-heritage-frame>
@else
    <div {{ $attributes->merge(['class' => 'prose-site']) }}>
        {{ $slot }}
    </div>
@endif