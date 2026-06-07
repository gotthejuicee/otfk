<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedbackMessageResource\Pages;
use App\Models\FeedbackMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeedbackMessageResource extends Resource
{
    protected static ?string $model = FeedbackMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Звернення';
    protected static ?string $navigationLabel = 'Звернення';
    protected static ?string $modelLabel = 'звернення';
    protected static ?string $pluralModelLabel = 'Звернення';

    public static function canCreate(): bool
    {
        return false; // звернення надходять лише з форми на сайті
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('is_read', false)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Імʼя')->disabled(),
            Forms\Components\TextInput::make('email')->label('Email')->disabled(),
            Forms\Components\TextInput::make('phone')->label('Телефон')->disabled(),
            Forms\Components\TextInput::make('subject')->label('Тема')->disabled(),
            Forms\Components\Textarea::make('message')->label('Повідомлення')->disabled()->rows(5)->columnSpanFull(),
            Forms\Components\Toggle::make('is_read')->label('Прочитано'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_read')->label('')->boolean()
                    ->trueIcon('heroicon-o-envelope-open')->falseIcon('heroicon-s-envelope')->falseColor('warning'),
                Tables\Columns\TextColumn::make('name')->label('Імʼя')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('subject')->label('Тема')->searchable()->limit(40)->placeholder('-'),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('Надіслано')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([Tables\Actions\EditAction::make()->label('Переглянути')])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeedbackMessages::route('/'),
            'create' => Pages\CreateFeedbackMessage::route('/create'),
            'edit' => Pages\EditFeedbackMessage::route('/{record}/edit'),
        ];
    }
}
