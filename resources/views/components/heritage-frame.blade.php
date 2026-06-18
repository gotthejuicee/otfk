@props(['date' => null, 'showSignoff' => true])

@php
    $dateline = 'Одеса';
    if ($date) {
        $dateline .= ' · ' . $date->copy()->locale('uk')->translatedFormat('j F Y');
    }
@endphp

<div class="heritage-frame">
    <p class="heritage-dateline">{{ $dateline }}</p>
    <div class="heritage-rule" aria-hidden="true"></div>

    {{ $slot }}

    <div class="heritage-seal" aria-hidden="true" title="Офіційний матеріал коледжу">
        <x-ico name="academic-cap" class="h-7 w-7" />
    </div>

    @if ($showSignoff)
        <p class="heritage-signoff">
            З повагою,<br>
            <span class="not-italic font-semibold text-brand-900">{{ config('app.name') }}</span>
        </p>
    @endif
</div>