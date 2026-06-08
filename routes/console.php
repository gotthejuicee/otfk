<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Щоденна резервна копія бази о 03:30. Зберігаємо 14 останніх копій.
// Працює лише якщо на хостингу налаштовано cron «php artisan schedule:run» щохвилини.
Schedule::command('otfk:backup')
    ->dailyAt('03:30')
    ->withoutOverlapping()
    ->runInBackground();
