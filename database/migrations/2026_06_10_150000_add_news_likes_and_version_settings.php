<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Вподобайки до новин (анонімні, по відбитку відвідувача) +
     * налаштування напису версії сайту в підвалі (текст і колір).
     */
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->unsignedInteger('likes')->default(0)->after('views');
        });

        Schema::create('news_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->string('fingerprint', 64);
            $table->timestamps();
            $table->unique(['news_id', 'fingerprint']);
        });

        $settings = [
            ['key' => 'site_version_label', 'value' => 'Альфа-версія', 'type' => 'text'],
            ['key' => 'site_version_color', 'value' => 'gold', 'type' => 'text'],
        ];

        foreach ($settings as $s) {
            if (! DB::table('settings')->where('key', $s['key'])->exists()) {
                DB::table('settings')->insert($s + [
                    'group' => 'general',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Cache::forget('settings.map');
    }

    public function down(): void
    {
        Schema::dropIfExists('news_likes');
        Schema::table('news', fn (Blueprint $table) => $table->dropColumn('likes'));
        DB::table('settings')->whereIn('key', ['site_version_label', 'site_version_color'])->delete();
    }
};
