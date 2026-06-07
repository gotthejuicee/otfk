<?php

use App\Models\DocumentCategory;
use App\Models\MenuItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Додає категорії «Публічна інформація» (DocumentCategory) + пункти меню на /dokumenty/{slug}.
     * Ідемпотентно: наявні (за slug / підписом) пропускаються.
     */
    public function up(): void
    {
        $root = MenuItem::whereNull('parent_id')->where('label', 'Публічна інформація')->first();

        // [Підпис, slug]
        $items = [
            ['До обговорення', 'do-obhovorennya'],
            ['Результати опитування здобувачів освіти', 'rezultaty-opytuvannya'],
            ['Репозитарій ВСП «ОТФК ОНТУ»', 'repozytariy'],
            ['Визнання результатів навчання, здобутих на ТОТ України', 'vyznannya-rezultativ-tot'],
            ['Вибори директора', 'vybory-dyrektora'],
            ['Вибори ректора ОНТУ', 'vybory-rektora'],
            ['Громадська організація', 'hromadska-orhanizatsiya'],
            ['Аудиторний фонд коледжу', 'audytornyy-fond'],
            ['Фінансовий звіт про надходження та використання коштів', 'finansovyy-zvit'],
            ['Інформація про отриману благодійну допомогу', 'blahodiyna-dopomoha'],
            ['Висновок про відповідність вимогам інклюзивності', 'vysnovok-inklyuzyvnist'],
            ['Цивільний захист та охорона праці', 'tsyvilnyy-zahyst-ohorona-pratsi'],
        ];

        $catOrder = (int) DocumentCategory::max('sort_order');
        $menuOrder = $root ? (int) MenuItem::where('parent_id', $root->id)->max('sort_order') : 0;

        foreach ($items as [$title, $slug]) {
            DocumentCategory::firstOrCreate(
                ['slug' => $slug],
                ['title' => $title, 'sort_order' => ++$catOrder]
            );

            if ($root && ! MenuItem::where('parent_id', $root->id)->where('label', $title)->exists()) {
                MenuItem::create([
                    'label' => $title,
                    'link_type' => 'url',
                    'url' => '/dokumenty/' . $slug,
                    'parent_id' => $root->id,
                    'sort_order' => ++$menuOrder,
                    'is_visible' => true,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Безпечний відкат: контент не видаляємо.
    }
};
