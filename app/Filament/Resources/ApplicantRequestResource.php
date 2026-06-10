<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantRequestResource\Pages;
use App\Models\ApplicantRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApplicantRequestResource extends Resource
{
    protected static ?string $model = ApplicantRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Звернення';
    protected static ?string $navigationLabel = 'Заявки абітурієнтів';
    protected static ?string $modelLabel = 'заявку';
    protected static ?string $pluralModelLabel = 'Заявки абітурієнтів';

    /** Кількість необроблених заявок на бейджі в меню. */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('is_processed', false)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('ПІБ')->required()->maxLength(255),
            Forms\Components\TextInput::make('phone')->label('Телефон')->required()->maxLength(50),
            Forms\Components\TextInput::make('email')->label('Email')->email()->maxLength(255),
            Forms\Components\Select::make('specialty_id')->label('Спеціальність')
                ->relationship('specialty', 'title')->preload(),
            Forms\Components\Textarea::make('message')->label('Питання')->rows(3)->columnSpanFull(),
            Forms\Components\Toggle::make('is_processed')->label('Опрацьовано')
                ->helperText('Позначте після дзвінка абітурієнту.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Коли')->dateTime('d.m.Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('ПІБ')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('phone')->label('Телефон')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('specialty.title')->label('Спеціальність')->limit(30)->placeholder('—'),
                Tables\Columns\ToggleColumn::make('is_processed')->label('Опрацьовано'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_processed')->label('Опрацьовано'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplicantRequests::route('/'),
            'edit' => Pages\EditApplicantRequest::route('/{record}/edit'),
        ];
    }
}
