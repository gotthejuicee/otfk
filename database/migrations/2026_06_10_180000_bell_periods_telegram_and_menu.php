<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Розклад дзвінків (редагується в адмінці) + автопостинг новин у Telegram
     * (налаштування) + позначка «вже опубліковано в TG» для новин + пункт меню.
     */
    public function up(): void
    {
        Schema::create('bell_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('number');          // номер пари
            $table->time('starts');
            $table->time('ends');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('news', function (Blueprint $table) {
            $table->timestamp('telegram_posted_at')->nullable()->after('likes');
        });

        // Типовий розклад (часи легко змінити в адмінці: Контент → Розклад дзвінків)
        $rows = [
            [1, '08:30', '09:50'],
            [2, '10:00', '11:20'],
            [3, '11:50', '13:10'],
            [4, '13:20', '14:40'],
            [5, '14:50', '16:10'],
            [6, '16:20', '17:40'],
        ];

        foreach ($rows as [$n, $s, $e]) {
            DB::table('bell_periods')->insert([
                'number' => $n, 'starts' => $s, 'ends' => $e, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Налаштування Telegram-автопостингу
        $settings = [
            ['key' => 'telegram_autopost', 'value' => '0', 'type' => 'text'],
            ['key' => 'telegram_bot_token', 'value' => '', 'type' => 'text'],
            ['key' => 'telegram_channel', 'value' => '', 'type' => 'text'],
        ];

        foreach ($settings as $s) {
            if (! DB::table('settings')->where('key', $s['key'])->exists()) {
                DB::table('settings')->insert($s + ['group' => 'general', 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // Пункт меню «Розклад дзвінків» у розділі «Студенту» (якщо розділ існує)
        $parent = DB::table('menu_items')->where('label', 'Студенту')->whereNull('parent_id')->first();

        if ($parent && ! DB::table('menu_items')->where('url', '/rozklad-dzvinkiv')->exists()) {
            $maxSort = (int) DB::table('menu_items')->where('parent_id', $parent->id)->max('sort_order');
            DB::table('menu_items')->insert([
                'parent_id' => $parent->id,
                'label' => 'Розклад дзвінків',
                'link_type' => 'url',
                'url' => '/rozklad-dzvinkiv',
                'sort_order' => $maxSort + 1,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Cache::forget('settings.map');
        Cache::forget('bell_periods');
    }

    public function down(): void
    {
        Schema::dropIfExists('bell_periods');
        Schema::table('news', fn (Blueprint $table) => $table->dropColumn('telegram_posted_at'));
        DB::table('settings')->whereIn('key', ['telegram_autopost', 'telegram_bot_token', 'telegram_channel'])->delete();
        DB::table('menu_items')->where('url', '/rozklad-dzvinkiv')->delete();
    }
};
