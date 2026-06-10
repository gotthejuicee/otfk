<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Відгуки студентів/випускників (головна) + FAQ з розміткою для Google.
     */
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // ПІБ
            $table->string('role')->nullable();           // «Випускник 2024, спец. 123 КІ»
            $table->text('quote');
            $table->string('photo')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Безпечні стартові питання (про можливості сайту — точні за побудовою).
        // Специфічні питання вступу додає коледж в адмінці.
        $faqs = [
            ['Як подати заявку на вступ онлайн?', "Заповніть форму «Залишити заявку» на сторінці /zayavka — вкажіть імʼя, телефон і спеціальність, що цікавить. Приймальна комісія зателефонує вам найближчим часом.", 1],
            ['Де подивитися розклад дзвінків?', 'Актуальний розклад пар і перерв — на сторінці «Розклад дзвінків» у розділі «Студенту». Під час занять у верхній частині сайту видно, яка пара триває зараз.', 2],
            ['Як звʼязатися з приймальною комісією?', 'Телефон і адреса вказані на сторінці «Контакти» та у підвалі сайту. Також можна написати через форму зворотного звʼязку або залишити заявку — ми передзвонимо.', 3],
        ];

        foreach ($faqs as [$q, $a, $sort]) {
            DB::table('faqs')->insert([
                'question' => $q, 'answer' => $a, 'sort_order' => $sort,
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        // Плитка «Питання та відповіді» в розділі «Абітурієнту» (слаг перехоплюється маршрутом /faq)
        $parentPage = DB::table('pages')->where('slug', 'abituriyentu')->whereNull('parent_id')->first();

        if ($parentPage && ! DB::table('pages')->where('slug', 'faq')->exists()) {
            DB::table('pages')->insert([
                'parent_id' => $parentPage->id,
                'title' => 'Питання та відповіді',
                'slug' => 'faq',
                'excerpt' => 'Відповіді на найчастіші питання вступників.',
                'sort_order' => 0,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $parentMenu = DB::table('menu_items')->where('label', 'Абітурієнту')->whereNull('parent_id')->first();

        if ($parentMenu && ! DB::table('menu_items')->where('url', '/faq')->exists()) {
            $maxSort = (int) DB::table('menu_items')->where('parent_id', $parentMenu->id)->max('sort_order');
            DB::table('menu_items')->insert([
                'parent_id' => $parentMenu->id,
                'label' => 'Питання та відповіді',
                'link_type' => 'url',
                'url' => '/faq',
                'sort_order' => $maxSort + 1,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('faqs');
        DB::table('pages')->where('slug', 'faq')->delete();
        DB::table('menu_items')->where('url', '/faq')->delete();
    }
};
