<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Спеціальності коледжу.
     */
    public function up(): void
    {
        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('code')->nullable();              // код спеціальності, напр. «121»
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('degree')->nullable();            // освітній ступінь
            $table->string('study_form')->nullable();        // форма навчання
            $table->string('duration')->nullable();          // термін навчання
            $table->string('cover_image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialties');
    }
};
