<?php

namespace App\Filament\Resources\BannerResource\Pages;

use App\Filament\Resources\BannerResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Forms\Components\Slider;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ListRecords;

class ListBanners extends ListRecords implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = BannerResource::class;

    protected static string $view = 'filament.resources.banner-resource.pages.list-banners';

    public ?array $overlay = [];

    public function mount(): void
    {
        parent::mount();

        $this->overlayForm->fill([
            'opacity' => min(100, max(0, (int) (Setting::get('banner_overlay_opacity') ?? 75))),
        ]);
    }

    protected function getForms(): array
    {
        return ['overlayForm'];
    }

    public function overlayForm(Form $form): Form
    {
        return $form
            ->schema([
                Slider::make('opacity')
                    ->label('Сила затемнення')
                    ->min(0)
                    ->max(100)
                    ->step(5)
                    ->live(debounce: 400)
                    ->afterStateUpdated(function (?int $state): void {
                        Setting::updateOrCreate(
                            ['key' => 'banner_overlay_opacity'],
                            [
                                'value' => (string) min(100, max(0, $state ?? 75)),
                                'group' => 'appearance',
                                'type' => 'number',
                            ],
                        );
                    })
                    ->helperText('0 — без затемнення (лише фото), 100 — максимальне затемнення для читабельного тексту.'),
            ])
            ->statePath('overlay');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}