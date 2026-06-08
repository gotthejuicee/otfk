<x-layouts.app title="Доступ заборонено">
    <section class="container-site flex min-h-[55vh] flex-col items-center justify-center py-20 text-center">
        <p class="text-7xl font-extrabold leading-none text-brand-700 sm:text-8xl" style="font-family:var(--font-display)">403</p>
        <h1 class="mt-5 text-2xl font-bold text-slate-900 sm:text-3xl">Доступ заборонено</h1>
        <p class="mt-3 max-w-md text-slate-500">У вас немає прав для перегляду цієї сторінки.</p>
        <div class="mt-8">
            <a href="{{ route('home') }}" class="btn-accent">На головну</a>
        </div>
    </section>
</x-layouts.app>
