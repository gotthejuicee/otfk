<?php

namespace App\Filament\Pages;

use App\Filament\Resources\DepartmentResource;
use App\Filament\Resources\DocumentCategoryResource;
use App\Filament\Resources\GalleryResource;
use App\Filament\Resources\PageResource;
use App\Filament\Resources\SettingResource;
use App\Models\ChecklistDismissal;
use App\Models\Department;
use App\Models\DocumentCategory;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\Setting;
use Filament\Notifications\Notification;
use Filament\Pages\Page as FilamentPage;
use Illuminate\Support\Facades\Cache;

/**
 * Живий чек-лист наповнення сайту: показує, що ще порожнє або заглушка,
 * з прямими посиланнями на редагування. Допомагає довести контент до кінця.
 */
class ContentChecklist extends FilamentPage
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Що наповнити';

    protected static ?string $title = 'Що ще наповнити';

    protected static ?string $navigationGroup = 'Структура сайту';

    protected static ?int $navigationSort = -1;

    protected static string $view = 'filament.pages.content-checklist';

    /** Скільки символів «живого» тексту вважаємо мінімумом, щоб сторінка не була заглушкою. */
    private const STUB_MIN_CHARS = 200;

    /** Сторінки без змісту (порожні або дуже короткі / з маркерами «в розробці»). */
    public function stubPages(): array
    {
        $phrases = ['у розробці', 'в розробці', 'буде додано', 'готується', 'готуються', 'незабаром', 'невдовзі', 'матеріали готуються'];

        return Page::query()->orderBy('section')->orderBy('title')->get()
            ->filter(function (Page $p) use ($phrases) {
                $text = trim(strip_tags((string) $p->body));
                $hasPhrase = collect($phrases)->contains(fn ($ph) => mb_stripos($text, $ph) !== false);

                return mb_strlen($text) < self::STUB_MIN_CHARS || $hasPhrase;
            })
            ->map(fn (Page $p) => [
                'key' => 'page:' . $p->getKey(),
                'label' => $p->title,
                'meta' => $p->section ?: '—',
                'url' => PageResource::getUrl('edit', ['record' => $p]),
            ])
            ->values()->all();
    }

    /** Категорії документів без жодного документа. */
    public function emptyDocumentCategories(): array
    {
        return DocumentCategory::query()->withCount('documents')->orderBy('title')->get()
            ->filter(fn (DocumentCategory $c) => $c->documents_count === 0)
            ->map(fn (DocumentCategory $c) => [
                'key' => 'doccat:' . $c->getKey(),
                'label' => $c->title,
                'meta' => '0 документів',
                'url' => DocumentCategoryResource::getUrl('edit', ['record' => $c]),
            ])
            ->values()->all();
    }

    /** Відділення/комісії без персоналу або без опису. */
    public function departmentsNeedingWork(): array
    {
        return Department::query()->withCount('staff')->orderBy('title')->get()
            ->filter(fn (Department $d) => $d->staff_count === 0 || mb_strlen(trim(strip_tags((string) $d->description))) < 80)
            ->map(fn (Department $d) => [
                'key' => 'dept:' . $d->getKey(),
                'label' => $d->title,
                'meta' => $d->staff_count === 0 ? 'немає персоналу' : 'короткий опис',
                'url' => DepartmentResource::getUrl('edit', ['record' => $d]),
            ])
            ->values()->all();
    }

    /** Галереї без фотографій. */
    public function emptyGalleries(): array
    {
        return Gallery::query()->withCount('photos')->orderBy('title')->get()
            ->filter(fn (Gallery $g) => $g->photos_count === 0)
            ->map(fn (Gallery $g) => [
                'key' => 'gallery:' . $g->getKey(),
                'label' => $g->title,
                'meta' => '0 фото',
                'url' => GalleryResource::getUrl('edit', ['record' => $g]),
            ])
            ->values()->all();
    }

    /** Важливі налаштування, які ще не заповнені. */
    public function missingSettings(): array
    {
        $keys = [
            'contact_phone' => 'Телефон',
            'contact_email' => 'Електронна пошта',
            'contact_address' => 'Адреса',
            'work_hours' => 'Графік роботи',
            'map_embed' => 'Карта на сторінці контактів',
            'social_facebook' => 'Facebook',
            'social_instagram' => 'Instagram',
            'site_description' => 'Опис сайту (SEO)',
        ];

        $rows = Setting::query()->whereIn('key', array_keys($keys))->get()->keyBy('key');
        $missing = [];

        foreach ($keys as $key => $label) {
            if (blank(optional($rows->get($key))->value)) {
                $record = $rows->get($key);
                $missing[] = [
                    'key' => 'setting:' . $key,
                    'label' => $label,
                    'meta' => $key,
                    'url' => $record
                        ? SettingResource::getUrl('edit', ['record' => $record])
                        : SettingResource::getUrl('index'),
                ];
            }
        }

        return $missing;
    }

    /** Усі автоматично знайдені пункти (з усіх груп). */
    public function allDetected(): array
    {
        return array_merge(
            $this->stubPages(),
            $this->emptyDocumentCategories(),
            $this->departmentsNeedingWork(),
            $this->emptyGalleries(),
            $this->missingSettings(),
        );
    }

    /** Ключі пунктів, які адмін приховав вручну. */
    public function dismissedKeys(): array
    {
        return ChecklistDismissal::query()->pluck('item_key')->all();
    }

    /** Прибрати пункт з чек-листа вручну (напр., свідомо порожня сторінка). */
    public function dismiss(string $key): void
    {
        ChecklistDismissal::firstOrCreate(['item_key' => $key]);
        Cache::forget('content_checklist_badge');

        Notification::make()->title('Приховано з чек-листа')->success()->send();
    }

    /** Повернути приховане назад у чек-лист. */
    public function restore(string $key): void
    {
        ChecklistDismissal::query()->where('item_key', $key)->delete();
        Cache::forget('content_checklist_badge');

        Notification::make()->title('Повернуто до чек-листа')->success()->send();
    }

    /** Лічильник у меню — активні пункти (без прихованих). Кеш 60с. */
    public static function getNavigationBadge(): ?string
    {
        $total = Cache::remember('content_checklist_badge', 60, function () {
            $page = new static();
            $dismissed = $page->dismissedKeys();

            return collect($page->allDetected())
                ->reject(fn ($item) => in_array($item['key'], $dismissed, true))
                ->count();
        });

        return $total > 0 ? (string) $total : null;
    }
}
