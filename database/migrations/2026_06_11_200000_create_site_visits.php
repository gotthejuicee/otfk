<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Приватна статистика відвідуваності: агрегати «дата + шлях + кількість».
     * Без кук і сторонніх сервісів; спецрядок _visits — візити (сесії) за день.
     */
    public function up(): void
    {
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('path', 191);
            $table->unsignedInteger('hits')->default(1);
            $table->unique(['date', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
