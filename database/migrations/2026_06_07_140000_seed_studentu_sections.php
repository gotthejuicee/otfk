<?php

use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Додає підрозділи «Студенту» (сторінка-заглушка + пункт меню).
     * Ідемпотентно: наявні пункти (за підписом) пропускаються, контент не чіпається.
     */
    public function up(): void
    {
        $root = MenuItem::whereNull('parent_id')->where('label', 'Студенту')->first();
        if (! $root) {
            return; // меню ще не засіяне
        }

        $parentPageId = Page::where('slug', 'studentu')->value('id');
        $body = '<p>Розділ наповнюється. Тут буде розміщено актуальну та детальну інформацію. '
            . 'Скористайтесь формою на сторінці «Контакти», якщо потрібні додаткові відомості.</p>';

        // [Підпис, slug]
        $items = [
            ['Підготовка до НМТ', 'pidhotovka-do-nmt'],
            ['Вибіркові дисципліни', 'vybirkovi-dystsypliny'],
            ['Цифрові видання у галузях', 'tsyfrovi-vydannya-u-haluzyah'],
            ['Пожежна безпека', 'pozhezhna-bezpeka'],
            ['Охорона праці', 'ohorona-pratsi'],
        ];

        $order = (int) MenuItem::where('parent_id', $root->id)->max('sort_order');

        foreach ($items as [$label, $slug]) {
            if (MenuItem::where('parent_id', $root->id)->where('label', $label)->exists()) {
                continue;
            }

            $page = Page::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $label,
                    'parent_id' => $parentPageId,
                    'section' => 'studentu',
                    'body' => $body,
                    'is_published' => true,
                    'sort_order' => 0,
                ]
            );

            MenuItem::create([
                'label' => $label,
                'link_type' => 'page',
                'page_id' => $page->id,
                'parent_id' => $root->id,
                'sort_order' => ++$order,
                'is_visible' => true,
            ]);
        }
    }

    public function down(): void
    {
        // Безпечний відкат: контент сторінок не видаляємо.
    }
};
