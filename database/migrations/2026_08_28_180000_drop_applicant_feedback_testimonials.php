<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Прибирає функціонал, якого немає на основному сайті otfk.od.ua:
     * онлайн-заявку абітурієнта, форму звернень (зворотний звʼязок) та відгуки.
     * Разом з таблицями видаляються сторінка-плитка і пункт меню «Залишити заявку»
     * та сид-FAQ, що посилалися на форму заявки.
     */
    public function up(): void
    {
        Schema::dropIfExists('applicant_requests');
        Schema::dropIfExists('feedback_messages');
        Schema::dropIfExists('testimonials');

        DB::table('pages')->where('slug', 'zayavka')->delete();
        DB::table('menu_items')->where('url', '/zayavka')->delete();

        // Сид-FAQ про онлайн-заявку більше не відповідає дійсності — прибираємо.
        DB::table('faqs')->where('question', 'Як подати заявку на вступ онлайн?')->delete();

        // Сид-FAQ про звʼязок згадував форму зворотного звʼязку та заявку —
        // правимо лише незмінений сид-текст (guard від затирання правок адмінки).
        DB::table('faqs')
            ->where('question', 'Як звʼязатися з приймальною комісією?')
            ->where('answer', 'Телефон і адреса вказані на сторінці «Контакти» та у підвалі сайту. Також можна написати через форму зворотного звʼязку або залишити заявку — ми передзвонимо.')
            ->update(['answer' => 'Телефон і адреса вказані на сторінці «Контакти» та у підвалі сайту. Зателефонуйте або напишіть на електронну пошту — відповімо в робочий час.', 'updated_at' => now()]);

        DB::table('settings')->where('key', 'feedback_email')->delete();

        Cache::forget('settings.map');
        Cache::forget('menu.navigation');
    }

    public function down(): void
    {
        // Контент (сторінку, меню, FAQ) не відновлюємо — тільки схему таблиць.
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

        Schema::create('feedback_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('quote');
            $table->string('photo')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
