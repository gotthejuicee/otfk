<?php

namespace App\Filament\Support;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page as FilamentPage;

/**
 * База для сторінок-форм «людських» налаштувань (Етап 2 ADMIN-UX-PLAN.md):
 * замість сирого key-value CRUD адмін бачить звичайну форму з підписами.
 *
 * Сторінка-нащадок оголошує keys() — перелік ключів таблиці settings, якими
 * керує. mount() наповнює форму з Setting::map(), save() пише значення через
 * firstOrNew + save (група виставляється лише новоствореним ключам, наявні
 * group/type не перетираються). Кеш settings.map скидає обсервер у
 * Setting::booted() — окремий Cache::forget не потрібен.
 */
abstract class SettingsFormPage extends FilamentPage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Налаштування';

    protected static string $view = 'filament.pages.settings-form';

    /** Група в таблиці settings для новостворених ключів. */
    protected static string $settingsGroup = 'general';

    /** @var array<string, mixed> */
    public ?array $data = [];

    /** @return array<int, string> ключі settings, якими керує сторінка */
    abstract protected static function keys(): array;

    public function mount(): void
    {
        $map = Setting::map();

        $state = [];
        foreach (static::keys() as $key) {
            $state[$key] = $map[$key] ?? null;
        }

        $this->form->fill($this->fromSettings($state));
    }

    /** Перетворення «рядок з БД → стан форми» (напр., '1' → true для Toggle). */
    protected function fromSettings(array $state): array
    {
        return $state;
    }

    /** Зворотне перетворення «стан форми → рядок для БД». */
    protected function toSettings(array $state): array
    {
        return $state;
    }

    /** @return array<Action> */
    public function getFormActions(): array
    {
        return [
            Action::make('save')->label('Зберегти')->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->toSettings($this->form->getState());

        foreach (static::keys() as $key) {
            $setting = Setting::firstOrNew(['key' => $key]);

            if (! $setting->exists) {
                $setting->group = static::$settingsGroup;
            }

            $setting->value = (string) ($state[$key] ?? '');
            $setting->save();
        }

        Notification::make()->title('Налаштування збережено')->success()->send();
    }
}
