<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quick_links', function (Blueprint $table) {
            $table->id();
            $table->string('location')->default('home_tile')->index(); // home_tile | footer_partner
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('url')->default('#');
            $table->string('icon')->nullable();            // heroicon (для плиток)
            $table->string('color')->default('brand');     // brand | gold
            $table->boolean('open_new_tab')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quick_links');
    }
};
