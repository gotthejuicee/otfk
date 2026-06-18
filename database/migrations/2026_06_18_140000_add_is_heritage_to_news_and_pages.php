<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Режим «heritage» — урочистий листоподібний оформлення для ювілейних/історичних матеріалів.
     */
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->boolean('is_heritage')->default(false)->after('is_featured');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('is_heritage')->default(false)->after('is_published');
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('is_heritage');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('is_heritage');
        });
    }
};