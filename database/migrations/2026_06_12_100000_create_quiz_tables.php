<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Профорієнтаційний квіз для абітурієнтів + плитка/пункт меню розділу.
     */
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_question_id')->constrained('quiz_questions')->cascadeOnDelete();
            $table->string('label');
            $table->foreignId('specialty_id')->nullable()->constrained('specialties')->nullOnDelete();
            $table->unsignedTinyInteger('points')->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Питання сідаються, якщо спеціальності вже існують (на проді — так;
        // у свіжих БД квіз досіється через SiteSeeder після спеціальностей).
        if (DB::table('specialties')->exists()) {
            (new \Database\Seeders\QuizSeeder())->run();
        }

        // Плитка в розділі «Абітурієнту» (слаг перехоплюється маршрутом /kviz)
        $parentPage = DB::table('pages')->where('slug', 'abituriyentu')->whereNull('parent_id')->first();

        if ($parentPage && ! DB::table('pages')->where('slug', 'kviz')->exists()) {
            DB::table('pages')->insert([
                'parent_id' => $parentPage->id,
                'title' => 'Яка спеціальність мені підходить?',
                'slug' => 'kviz',
                'excerpt' => 'Пройди короткий тест і дізнайся.',
                'sort_order' => 0,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $parentMenu = DB::table('menu_items')->where('label', 'Абітурієнту')->whereNull('parent_id')->first();

        if ($parentMenu && ! DB::table('menu_items')->where('url', '/kviz')->exists()) {
            $maxSort = (int) DB::table('menu_items')->where('parent_id', $parentMenu->id)->max('sort_order');
            DB::table('menu_items')->insert([
                'parent_id' => $parentMenu->id,
                'label' => 'Тест: яка спеціальність підходить?',
                'link_type' => 'url',
                'url' => '/kviz',
                'sort_order' => $maxSort + 1,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
        DB::table('pages')->where('slug', 'kviz')->delete();
        DB::table('menu_items')->where('url', '/kviz')->delete();
    }
};
