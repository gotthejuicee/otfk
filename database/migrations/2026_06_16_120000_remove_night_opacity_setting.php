<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Нічна підсвітка та темна тема прибрані — сайт лише світлий.
     * Налаштування яскравості оверлея більше не використовується.
     */
    public function up(): void
    {
        DB::table('settings')->where('key', 'night_opacity')->delete();
    }

    public function down(): void
    {
        DB::table('settings')->insertOrIgnore([
            'key' => 'night_opacity',
            'value' => '13',
            'group' => 'appearance',
            'type' => 'number',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
