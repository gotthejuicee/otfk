<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Налаштування';
    protected static ?string $navigationLabel = 'Налаштування сайту';
    protected static ?string $modelLabel = 'налаштування';
    protected static ?string $pluralModelLabel = 'Налаштування сайту';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('key')->label('Ключ')->required()->maxLength(255)
                ->disabledOn('edit')->helperText('Технічний ідентифікатор, напр. contact_phone.'),
            Forms\Components\Select::make('type')->label('Тип значення')->default('text')->live()
                ->options([
                    'text' => 'Текст',
                    'textarea' => 'Багаторядковий',
                    'url' => 'Посилання',
                    'html' => 'HTML',
                    'image' => 'Зображення',
                ]),
            Forms\Components\FileUpload::make('image_value')->label('Зображення')->image()->imageEditor()
                ->directory('settings')->columnSpanFull()
                ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                ->helperText('Напр. логотип сайту. Рекомендований формат - PNG з прозорим тлом.'),
            Forms\Components\Textarea::make('value')->label('Значення')->rows(3)->columnSpanFull()
                ->visible(fn (Forms\Get $get) => $get('type') !== 'image'),
            Forms\Components\TextInput::make('group')->label('Група')->default('general')->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->label('Ключ')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('value')->label('Значення')->limit(60)->color('gray'),
                Tables\Columns\TextColumn::make('group')->label('Група')->badge()->sortable(),
            ])
            ->defaultSort('group')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
