@props(['specialty', 'showProgram' => true])

{{-- Картка спеціальності: обкладинок у базі немає, тож акцент — код, іконка напряму та геометрична сітка --}}
@php
    $facts = array_filter([
        ['academic-cap', $specialty->degree],
        ['calendar-days', $specialty->study_form],
        ['clock', $specialty->duration],
    ], fn ($r) => filled($r[1]));
@endphp

<article {{ $attributes->class(['card card-interactive group relative flex flex-col overflow-hidden']) }}>
    <div class="relative overflow-hidden bg-gradient-to-br from-brand-800 to-brand-950 px-6 py-7 sm:px-8">
        <svg aria-hidden="true" class="pointer-events-none absolute inset-0 h-full w-full text-white/[0.07]">
            <defs>
                <pattern id="sp-grid-{{ $specialty->id }}" width="28" height="28" patternUnits="userSpaceOnUse">
                    <path d="M28 0H0V28" fill="none" stroke="currentColor" stroke-width="1" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#sp-grid-{{ $specialty->id }})" />
        </svg>

        <div class="relative flex items-start justify-between gap-4">
            <div class="min-w-0">
                @if ($specialty->code)
                    <span class="block font-display text-4xl font-extrabold leading-none text-white sm:text-5xl">{{ $specialty->code }}</span>
                @endif
                <h3 class="mt-3 text-lg font-bold leading-snug text-white">
                    <a href="{{ route('specialties.show', $specialty) }}" class="after:absolute after:inset-0 focus:outline-none focus-visible:underline">{{ $specialty->title }}</a>
                </h3>
            </div>
            <x-ico :name="$specialty->icon_name" aria-hidden="true"
                   class="h-14 w-14 shrink-0 text-white/30 transition duration-500 group-hover:text-gold-400/70 sm:h-16 sm:w-16" />
        </div>
    </div>

    <div class="flex flex-1 flex-col p-6 sm:p-7">
        @if ($facts)
            <ul class="space-y-2.5 text-sm">
                @foreach ($facts as [$icon, $value])
                    <li class="flex items-start gap-2.5 text-slate-600">
                        <x-ico :name="$icon" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" aria-hidden="true" />
                        <span>{{ $value }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($showProgram && $specialty->short_description)
            <p @class([
                'text-sm leading-relaxed text-slate-500',
                'mt-5 border-t border-slate-100 pt-5' => (bool) $facts,
            ])>{{ $specialty->short_description }}</p>
        @endif

        @if ($showProgram && $specialty->programs->isNotEmpty())
            <div class="mt-5 flex items-start gap-2.5 border-t border-slate-100 pt-5 text-sm">
                <x-ico name="book-open" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" aria-hidden="true" />
                <span class="min-w-0 font-semibold text-slate-800">{{ $specialty->programs->first()->title }}</span>
            </div>
        @endif

        <span class="btn-outline mt-6 self-start border-gold-300 text-gold-700 ring-gold-300 transition group-hover:bg-gold-50">
            Детальніше <x-ico name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" />
        </span>
    </div>
</article>
