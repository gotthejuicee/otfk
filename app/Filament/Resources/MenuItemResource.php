<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Filament\Support\ViewOnSite;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationGroup = 'Структура сайту';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Меню навігації';
    protected static ?string $modelLabel = 'пункт меню';
    protected static ?string $pluralModelLabel = 'Пункти меню';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('label')->label('Підпис')->required()->maxLength(255),
            Forms\Components\Select::make('parent_id')->label('Батьківський пункт')
                ->relationship('parent', 'label')->searchable()->preload()
                ->helperText('Залиште порожнім для пункту верхнього рівня.'),
            Forms\Components\Select::make('link_type')->label('Тип посилання')->required()->default('page')
                ->options(['page' => 'Сторінка', 'url' => 'Зовнішнє посилання', 'route' => 'Системний маршрут']),
            Forms\Components\Select::make('page_id')->label('Сторінка')
                ->relationship('page', 'title')->searchable()->preload()
                ->helperText('Для типу «Сторінка».'),
            Forms\Components\TextInput::make('url')->label('Посилання / назва маршруту')->maxLength(255)
                ->helperText('Для типів «Зовнішнє посилання» (URL) або «Системний маршрут» (напр. home, news.index).'),
            Forms\Components\TextInput::make('sort_order')->label('Порядок')->numeric()->default(0)
                ->helperText('Простіше змінити перетягуванням рядків у списку (кнопка «Змінити порядок»).'),
            Forms\Components\Toggle::make('open_new_tab')->label('Відкривати в новій вкладці'),
            Forms\Components\Toggle::make('is_visible')->label('Видимий')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')->label('Підпис')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('parent.label')->label('Батьківський')->badge()->placeholder('- верхній рівень -'),
                Tables\Columns\TextColumn::make('link_type')->label('Тип')->badge()
                    ->formatStateUsing(fn ($state) => ['page' => 'Сторінка', 'url' => 'Посилання', 'route' => 'Маршрут'][$state] ?? $state),
                Tables\Columns\ToggleColumn::make('is_visible')->label('Видимий'),
                Tables\Columns\TextColumn::make('sort_order')->label('Порядок')->numeric()->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->defaultGroup('parent.label')
            ->emptyStateHeading('Меню поки порожнє')
            ->emptyStateDescription('Пункти меню - це верхня навігація сайту. Додайте пункт верхнього рівня, а потім вкладені підпункти з посиланнями на сторінки.')
            ->actions([
                Tables\Actions\EditAction::make(),
                ViewOnSite::table(fn (MenuItem $record) => $record->href)
                    ->visible(fn (MenuItem $record) => $record->href !== '#'),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
