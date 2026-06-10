<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Плитка «Розклад дзвінків» на сторінці розділу «Студенту».
     * Плитки розділу — це дочірні СТОРІНКИ; сам слаг /rozklad-dzvinkiv
     * перехоплюється явним маршрутом (сторінка-болванка ніколи не рендериться).
     */
    public function up(): void
    {
        $parent = DB::table('pages')->where('slug', 'studentu')->whereNull('parent_id')->first();

        if (! $parent || DB::table('pages')->where('slug', 'rozklad-dzvinkiv')->exists()) {
            return;
        }

        DB::table('pages')->insert([
            'parent_id' => $parent->id,
            'title' => 'Розклад дзвінків',
            'slug' => 'rozklad-dzvinkiv',
            'excerpt' => 'Час початку та закінчення пар і перерв.',
            'body' => null,
            'sort_order' => 0,
            'is_published' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('pages')->where('slug', 'rozklad-dzvinkiv')->delete();
    }
};
