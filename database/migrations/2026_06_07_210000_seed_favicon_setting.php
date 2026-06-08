<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Додає налаштування favicon (тип image), щоб його можна було завантажити в адмінці.
     */
    public function up(): void
    {
        if (! DB::table('settings')->where('key', 'favicon')->exists()) {
            DB::table('settings')->insert([
                'key' => 'favicon',
                'value' => '',
                'group' => 'general',
                'type' => 'image',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'favicon')->delete();
    }
};
