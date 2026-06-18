<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Сила затемнення фото банера на головній (0–100).
     * Керується в адмінці: Контент → Банери → слайдер «Затемнення фото».
     */
    public function up(): void
    {
        if (! DB::table('settings')->where('key', 'banner_overlay_opacity')->exists()) {
            DB::table('settings')->insert([
                'key' => 'banner_overlay_opacity',
                'value' => '75',
                'group' => 'appearance',
                'type' => 'number',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Cache::forget('settings.map');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'banner_overlay_opacity')->delete();
        Cache::forget('settings.map');
    }
};