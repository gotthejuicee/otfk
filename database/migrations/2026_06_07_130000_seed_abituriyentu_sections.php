<?php

use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Додає підрозділи «Абітурієнту» (сторінка-заглушка + пункт меню).
     * Ідемпотентно: наявні пункти (за підписом) пропускаються, контент не чіпається.
     */
    public function up(): void
    {
        $root = MenuItem::whereNull('parent_id')->where('label', 'Абітурієнту')->first();
        if (! $root) {
            return; // меню ще не засіяне
        }

        $parentPageId = Page::where('slug', 'abituriyentu')->value('id');
        $body = '<p>Розділ наповнюється. Тут буде розміщено актуальну та детальну інформацію. '
            . 'Скористайтесь формою на сторінці «Контакти», якщо потрібні додаткові відомості.</p>';

        // [Підпис, slug]
        $items = [
            ['Роз\'яснення щодо термінів прийому документів', 'rozyasnennya-terminiv-pryyomu'],
            ['Обсяг державного замовлення', 'obsyah-derzhavnoho-zamovlennya'],
            ['Перелік конкурсних предметів', 'perelik-konkursnykh-predmetiv'],
            ['Документи, необхідні для вступу', 'dokumenty-neobhidni-dlya-vstupu'],
            ['Інструкція для створення електронного кабінету', 'instruktsiya-elektronnyy-kabinet'],
            ['Особливості подання документів в електронній формі', 'osoblyvosti-podannya-elektronno'],
            ['Сертифікати', 'sertyfikaty'],
            ['Підготовчі курси', 'pidhotovchi-kursy'],
            ['Програми вступних випробувань', 'prohramy-vstupnykh-vyprobuvan'],
            ['Розклад вступних випробувань (на базі 9 класів)', 'rozklad-vyprobuvan-9-klas'],
            ['Розклад фахових випробувань (диплом кваліфікованого робітника, 11 клас)', 'rozklad-fakhovykh-vyprobuvan'],
            ['Кількість вакантних місць / поповнення груп ІІ та ІІІ курсів', 'kilkist-vakantnykh-mists'],
            ['Рейтингові списки та списки рекомендованих', 'reytynhovi-spysky-rekomendovani'],
        ];

        $order = (int) MenuItem::where('parent_id', $root->id)->max('sort_order');

        foreach ($items as [$label, $slug]) {
            // пропустити, якщо такий пункт уже є в цьому розділі
            if (MenuItem::where('parent_id', $root->id)->where('label', $label)->exists()) {
                continue;
            }

            $page = Page::firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $label,
                    'parent_id' => $parentPageId,
                    'section' => 'abituriyentu',
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
