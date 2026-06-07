<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationGroup = 'Контент';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Відео';
    protected static ?string $modelLabel = 'відео';
    protected static ?string $pluralModelLabel = 'Відео';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->label('Назва')->required()->maxLength(255)->columnSpanFull(),
            Forms\Components\TextInput::make('youtube_id')->label('ID відео YouTube')->required()->maxLength(255)
                ->helperText('Напр., для youtube.com/watch?v=dQw4w9WgXcQ це «dQw4w9WgXcQ».'),
            Forms\Components\DatePicker::make('published_at')->label('Дата')->default(now()),
            Forms\Components\Textarea::make('description')->label('Опис')->rows(3)->columnSpanFull(),
            Forms\Components\TextInput::make('sort_order')->label('Порядок')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->label('Опубліковано')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('youtube_id')->label('')->square()
                    ->getStateUsing(fn ($record) => "https://img.youtube.com/vi/{$record->youtube_id}/default.jpg"),
                Tables\Columns\TextColumn::make('title')->label('Назва')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('published_at')->label('Дата')->date('d.m.Y')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label('Опубл.')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Порядок')->numeric()->sortable()->toggleable(),
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
