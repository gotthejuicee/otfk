<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Нерозривний дефіс U+2011 («огляд‑конкурс», «ТОП‑10») зі старого сайту →
     * звичайний дефіс. Інакше пошук «ТОП-10» не знаходить «ТОП‑10».
     */
    public function up(): void
    {
        $nb = "\u{2011}";

        foreach (['title', 'excerpt', 'body'] as $col) {
            DB::table('news')->where($col, 'like', "%{$nb}%")->update([
                $col => DB::raw("REPLACE({$col}, '{$nb}', '-')"),
            ]);
        }
    }

    public function down(): void
    {
        // Незворотньо (звичайний дефіс лишається) — і це ок.
    }
};
