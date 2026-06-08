<x-layouts.app title="Сторінку не знайдено">
    <section class="container-site flex min-h-[55vh] flex-col items-center justify-center py-20 text-center">
        <p class="text-7xl font-extrabold leading-none text-brand-700 sm:text-8xl" style="font-family:var(--font-display)">404</p>
        <h1 class="mt-5 text-2xl font-bold text-slate-900 sm:text-3xl">Сторінку не знайдено</h1>
        <p class="mt-3 max-w-md text-slate-500">Можливо, її переміщено або видалено. Скористайтесь пошуком або поверніться на головну сторінку.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('home') }}" class="btn-accent">На головну</a>
            <a href="{{ route('search') }}" class="btn-outline">Пошук по сайту</a>
        </div>
    </section>
</x-layouts.app>
