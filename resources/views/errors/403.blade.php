<x-layouts.app title="Доступ заборонено">

    <section class="border-b border-slate-200/70 bg-slate-50/80">
        <div class="container-site py-12 lg:py-16">
            <div class="relative overflow-hidden rounded-2xl bg-white px-6 py-10 shadow-sm ring-1 ring-slate-200/80 sm:px-10 sm:py-12">
                <p aria-hidden="true"
                   class="pointer-events-none absolute -right-4 top-1/2 hidden -translate-y-1/2 font-display text-[12rem] font-extrabold leading-none text-brand-50 lg:block">403</p>

                <div class="relative max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gold-700 ring-1 ring-gold-300/70">
                        <x-ico name="lock-closed" class="h-4 w-4" aria-hidden="true" /> Помилка 403
                    </span>
                    <h1 class="mt-3 text-3xl font-extrabold leading-tight text-brand-950 sm:text-4xl">Доступ заборонено</h1>
                    <div class="accent-rule"></div>
                    <p class="mt-5 text-lg leading-relaxed text-slate-500">
                        У вас немає прав для перегляду цієї сторінки. Якщо це помилка — напишіть нам, і ми розберемося.
                    </p>

                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('home') }}" class="btn-primary">
                            <x-ico name="home" class="h-4 w-4" aria-hidden="true" /> На головну
                        </a>
                        <a href="{{ route('contacts') }}" class="btn-outline">Написати нам</a>
                        <a href="{{ route('search') }}" class="btn-outline">Пошук по сайту</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>
