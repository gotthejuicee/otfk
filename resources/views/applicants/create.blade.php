<x-layouts.app title="Залишити заявку" description="Онлайн-заявка для вступників до Одеського технічного фахового коледжу ОНТУ: залиште контакти — приймальна комісія звʼяжеться з вами.">

    <section class="bg-brand-950">
        <div class="container-site py-12 lg:py-14">
            <nav class="flex flex-wrap items-center gap-2 text-sm text-brand-300">
                <a href="{{ route('home') }}" class="hover:text-white">Головна</a>
                <x-ico name="chevron-right" class="h-4 w-4" />
                <a href="{{ url('/abituriyentu') }}" class="hover:text-white">Абітурієнту</a>
                <x-ico name="chevron-right" class="h-4 w-4" />
                <span class="text-white">Залишити заявку</span>
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Залишити заявку</h1>
            <p class="mt-4 max-w-2xl text-brand-100">Заповніть форму — приймальна комісія зателефонує вам, відповість на питання та допоможе зі вступом.</p>
            <div class="accent-rule"></div>
        </div>
    </section>

    <section class="container-site py-12">
        <div class="mx-auto max-w-2xl">
            <div class="card p-6 sm:p-8">
                @if (session('status'))
                    <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <x-ico name="check-circle" variant="solid" class="h-5 w-5 shrink-0 text-emerald-500" />
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('applicants.store') }}" class="space-y-5">
                    @csrf
                    {{-- Honeypot (антиспам) --}}
                    <div class="hidden" aria-hidden="true">
                        <label for="website">Не заповнюйте це поле</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div>
                        <label for="name" class="label">Прізвище та імʼя <span class="text-rose-500">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required class="input @error('name') ring-rose-400 @enderror" placeholder="Шевченко Тарас">
                        @error('name') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="phone" class="label">Телефон <span class="text-rose-500">*</span></label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required class="input @error('phone') ring-rose-400 @enderror" placeholder="+380 __ ___ __ __">
                            @error('phone') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="label">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="input @error('email') ring-rose-400 @enderror">
                            @error('email') <p class="mt-1.5 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="specialty_id" class="label">Яка спеціальність цікавить?</label>
                        <select id="specialty_id" name="specialty_id" class="input">
                            <option value="">— Ще не визначився / не визначилась —</option>
                            @foreach ($specialties as $sp)
                                <option value="{{ $sp->id }}" @selected(old('specialty_id', request('specialty_id')) == $sp->id)>{{ $sp->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="message" class="label">Питання чи коментар</label>
                        <textarea id="message" name="message" rows="4" class="input" placeholder="Напр.: чи є місця на бюджет після 9 класу?">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="btn-primary w-full sm:w-auto">
                        <x-ico name="paper-airplane" class="h-4 w-4" /> Надіслати заявку
                    </button>
                    <p class="text-xs text-slate-400">Надсилаючи форму, ви даєте згоду на обробку вказаних контактних даних для звʼязку з вами.</p>
                </form>
            </div>
        </div>
    </section>

</x-layouts.app>
