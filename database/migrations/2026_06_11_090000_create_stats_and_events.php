<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * «Коледж у цифрах» (лічильники на головній) + «Події» (календар подій).
     */
    public function up(): void
    {
        Schema::create('stat_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');                 // підпис: «Студентів»
            $table->string('value');                  // значення: «1000+», «85%», «6»
            $table->string('icon')->nullable();       // heroicon, напр. user-group
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('url')->nullable();        // посилання «детальніше» (новина/зовнішнє)
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->index(['is_published', 'starts_at']);
        });

        // Стартові цифри — РЕДАГУЙТЕ реальні значення в адмінці (Контент → Коледж у цифрах)
        $stats = [
            ['Студентів', '1000+', 'user-group', 1],
            ['Років досвіду', '90+', 'academic-cap', 2],
            ['Спеціальностей', '6', 'squares-2x2', 3],
            ['Викладачів', '80+', 'users', 4],
        ];

        foreach ($stats as [$label, $value, $icon, $sort]) {
            DB::table('stat_items')->insert([
                'label' => $label, 'value' => $value, 'icon' => $icon, 'sort_order' => $sort,
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('stat_items');
    }
};
