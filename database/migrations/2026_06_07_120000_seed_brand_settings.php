<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Додає налаштування назви коледжу (якщо ще немає), щоб їх можна було
     * редагувати в адмінці. firstOrCreate-логіка - не перезаписує існуючі.
     */
    public function up(): void
    {
        $now = now();

        $defaults = [
            'brand_short' => 'ОТФК ОНТУ',
            'brand_name' => 'Одеський технічний фаховий коледж',
        ];

        foreach ($defaults as $key => $value) {
            if (! DB::table('settings')->where('key', $key)->exists()) {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'group' => 'general',
                    'type' => 'text',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['brand_short', 'brand_name'])->delete();
    }
};
