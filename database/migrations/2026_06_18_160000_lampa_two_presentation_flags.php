<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * «Лампа 2»: heritage для категорій новин + архівний стиль фотогалерей.
     */
    public function up(): void
    {
        Schema::table('news_categories', function (Blueprint $table) {
            $table->boolean('is_heritage')->default(false)->after('sort_order');
        });

        Schema::table('galleries', function (Blueprint $table) {
            $table->boolean('is_archive')->default(false)->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('news_categories', function (Blueprint $table) {
            $table->dropColumn('is_heritage');
        });

        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn('is_archive');
        });
    }
};