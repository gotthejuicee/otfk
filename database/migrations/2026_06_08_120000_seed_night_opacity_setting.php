<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Налаштування яскравості нічної підсвітки (у відсотках, 0–30).
     * Керується з адмінки → Налаштування сайту → night_opacity.
     */
    public function up(): void
    {
        if (! DB::table('settings')->where('key', 'night_opacity')->exists()) {
            DB::table('settings')->insert([
                'key' => 'night_opacity',
                'value' => '13',
                'group' => 'general',
                'type' => 'number',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Сирий insert не тригерить подію моделі — скидаємо кеш налаштувань вручну.
        Cache::forget('settings.map');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'night_opacity')->delete();
    }
};
