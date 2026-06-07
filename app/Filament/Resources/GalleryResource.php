<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Контент';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Фотогалереї';
    protected static ?string $modelLabel = 'галерею';
    protected static ?string $pluralModelLabel = 'Фотогалереї';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->label('Назва альбому')->required()->maxLength(255)->columnSpanFull(),
            Forms\Components\TextInput::make('slug')->label('URL (slug)')->maxLength(255)
                ->helperText('Залиште порожнім - згенерується автоматично.'),
            Forms\Components\DatePicker::make('published_at')->label('Дата')->default(now()),
            Forms\Components\Textarea::make('description')->label('Опис')->rows(2)->columnSpanFull(),
            Forms\Components\FileUpload::make('cover_image')->label('Обкладинка')->image()->directory('gallery')->imageEditor(),
            Forms\Components\Toggle::make('is_published')->label('Опубліковано')->default(true),
            Forms\Components\Repeater::make('photos')
                ->relationship()
                ->label('Фотографії')
                ->schema([
                    Forms\Components\FileUpload::make('image')->label('Зображення')->image()->directory('gallery')->required()->columnSpan(2),
                    Forms\Components\TextInput::make('caption')->label('Підпис')->maxLength(255)->columnSpan(2),
                ])
                ->columns(2)
                ->orderColumn('sort_order')
                ->collapsible()
                ->grid(2)
                ->addActionLabel('Додати фото')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->label('')->square(),
                Tables\Columns\TextColumn::make('title')->label('Назва')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('photos_count')->label('Фото')->counts('photos')->badge(),
                Tables\Columns\TextColumn::make('published_at')->label('Дата')->date('d.m.Y')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Опубл.')->boolean(),
            ])
            ->defaultSort('sort_order')
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
