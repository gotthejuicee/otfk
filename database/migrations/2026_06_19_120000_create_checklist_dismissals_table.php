<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Пункти чек-листа наповнення, які адміністратор приховав вручну
     * (напр. свідомо порожня сезонна сторінка). item_key — стабільний
     * ключ виду "page:5", "doccat:3", "setting:contact_phone".
     */
    public function up(): void
    {
        Schema::create('checklist_dismissals', function (Blueprint $table) {
            $table->id();
            $table->string('item_key')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_dismissals');
    }
};
