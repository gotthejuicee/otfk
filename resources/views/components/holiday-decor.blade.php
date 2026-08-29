@props(['theme' => null])

@php
    // Святкові прикраси сайту: стрічка над шапкою + падаючі частинки.
    // Тема обирається в адмінці («Підвал і вигляд»), довідник — App\Support\HolidayTheme.
    $holiday = \App\Support\HolidayTheme::config($theme);
@endphp

@if ($holiday)
    <div class="holiday-decor" data-holiday="{{ $theme }}" aria-hidden="true">
        {{-- Тонка святкова стрічка на самій горі сторінки (прокручується разом зі сторінкою) --}}
        <div class="holiday-ribbon" style="background: {{ $holiday['ribbon'] }}"></div>

        {{-- Падаючі частинки: позиції/тайминги детерміновані від індексу, без rand() --}}
        @if ($holiday['particles'] !== [])
            <div class="holiday-particles{{ ($holiday['mono'] ?? false) ? ' holiday-particles-mono' : '' }}">
                @for ($i = 0; $i < 14; $i++)
                    <span style="left: {{ ($i * 83 + 7) % 100 }}%;
                                 font-size: {{ number_format(0.8 + ($i % 3) * 0.35, 2, '.', '') }}rem;
                                 animation-duration: {{ number_format(9 + ($i % 5) * 2.3, 1, '.', '') }}s;
                                 animation-delay: -{{ number_format($i * 1.7, 1, '.', '') }}s;">{{ $holiday['particles'][$i % count($holiday['particles'])] }}</span>
                @endfor
            </div>
        @endif
    </div>

    <style>
        .holiday-ribbon {
            height: 6px;
        }

        .holiday-particles {
            position: fixed;
            inset: 0;
            z-index: 25; /* під липкою шапкою (z-40), над контентом */
            overflow: hidden;
            pointer-events: none;
        }

        .holiday-particles span {
            position: absolute;
            top: -8vh;
            opacity: 0.7;
            animation: holiday-fall linear infinite;
            will-change: transform;
        }

        .holiday-particles-mono span {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-weight: 700;
            color: #16a34a;
            opacity: 0.45;
        }

        @keyframes holiday-fall {
            to { transform: translateY(115vh) rotate(300deg); }
        }

        /* Бейдж біля логотипа в шапці (рендериться в layout, коли тема активна) */
        .holiday-logo-badge {
            position: absolute;
            top: -0.45rem;
            left: -0.55rem;
            z-index: 1;
            font-size: 1.05rem;
            line-height: 1;
            transform: rotate(-18deg);
            filter: drop-shadow(0 1px 1px rgb(0 0 0 / 0.25));
            pointer-events: none;
        }

        @media (prefers-reduced-motion: reduce) {
            .holiday-particles { display: none; }
        }
    </style>
@endif
