<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Заявки абітурієнтів (лід-форма) + банер термінових оголошень (налаштування)
     * + плитка/пункт меню «Залишити заявку» в розділі «Абітурієнту».
     */
    public function up(): void
    {
        Schema::create('applicant_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 50);
            $table->string('email')->nullable();
            $table->foreignId('specialty_id')->nullable()->constrained('specialties')->nullOnDelete();
            $table->text('message')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        // Банер оголошень: текст (порожній = прихований), тип кольору, посилання
        $settings = [
            ['key' => 'announcement_text', 'value' => '', 'type' => 'textarea'],
            ['key' => 'announcement_type', 'value' => 'info', 'type' => 'text'],
            ['key' => 'announcement_url', 'value' => '', 'type' => 'url'],
        ];

        foreach ($settings as $s) {
            if (! DB::table('settings')->where('key', $s['key'])->exists()) {
                DB::table('settings')->insert($s + ['group' => 'general', 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // Плитка в розділі «Абітурієнту» (слаг перехоплюється маршрутом /zayavka)
        $parentPage = DB::table('pages')->where('slug', 'abituriyentu')->whereNull('parent_id')->first();

        if ($parentPage && ! DB::table('pages')->where('slug', 'zayavka')->exists()) {
            DB::table('pages')->insert([
                'parent_id' => $parentPage->id,
                'title' => 'Залишити заявку',
                'slug' => 'zayavka',
                'excerpt' => 'Онлайн-заявка для вступників: ми зателефонуємо вам.',
                'sort_order' => 0,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Пункт меню в «Абітурієнту»
        $parentMenu = DB::table('menu_items')->where('label', 'Абітурієнту')->whereNull('parent_id')->first();

        if ($parentMenu && ! DB::table('menu_items')->where('url', '/zayavka')->exists()) {
            $maxSort = (int) DB::table('menu_items')->where('parent_id', $parentMenu->id)->max('sort_order');
            DB::table('menu_items')->insert([
                'parent_id' => $parentMenu->id,
                'label' => 'Залишити заявку',
                'link_type' => 'url',
                'url' => '/zayavka',
                'sort_order' => $maxSort + 1,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Cache::forget('settings.map');
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_requests');
        DB::table('settings')->whereIn('key', ['announcement_text', 'announcement_type', 'announcement_url'])->delete();
        DB::table('pages')->where('slug', 'zayavka')->delete();
        DB::table('menu_items')->where('url', '/zayavka')->delete();
    }
};
