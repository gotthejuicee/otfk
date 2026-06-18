@props(['title', 'breadcrumbs' => [], 'showRule' => true, 'heritage' => false])

<section @class(['bg-brand-950', 'heritage-hero' => $heritage])>
    <div class="container-site py-12 lg:py-14">
        @if (count($breadcrumbs))
            <x-breadcrumbs :items="$breadcrumbs" />
        @endif
        <h1 {{ $attributes->class(['mt-3 text-3xl font-extrabold text-white sm:text-4xl']) }}>{{ $title }}</h1>
        @if (isset($slot) && ! $slot->isEmpty())
            {{ $slot }}
        @endif
        @if ($showRule)
            <div class="accent-rule"></div>
        @endif
    </div>
</section>