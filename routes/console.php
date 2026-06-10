<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Щотижнева резервна копія бази (щонеділі о 03:30). Зберігаємо 14 останніх копій.
// Потрібно лише якщо на хостингу cron викликає «php artisan schedule:run» щохвилини.
// Якщо ж налаштовано прямий cron на «artisan otfk:backup» — періодичність задає сам cron.
Schedule::command('otfk:backup')
    ->weeklyOn(0, '03:30')
    ->withoutOverlapping();

// Чистимо статистику відвідувань, старішу за пів року (агрегати займають місце дарма)
Schedule::call(fn () => \App\Models\SiteVisit::where('date', '<', now()->subDays(180)->toDateString())->delete())
    ->weeklyOn(0, '04:00')
    ->name('prune-site-visits');
