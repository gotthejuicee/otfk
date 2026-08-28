<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BellPeriodResource\Pages;
use App\Models\BellPeriod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BellPeriodResource extends Resource
{
    protected static ?string $model = BellPeriod::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationGroup = 'Контент';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Розклад дзвінків';
    protected static ?string $modelLabel = 'пару';
    protected static ?string $pluralModelLabel = 'Розклад дзвінків';

    /** Підписи змін — однакові в формі, таблиці та фільтрі. */
    public static function shiftOptions(): array
    {
        return [1 => '1 зміна', 2 => '2 зміна'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('shift')->label('Зміна')->options(static::shiftOptions())
                ->default(1)->required()
                ->helperText('Друга зміна цілком ховається кнопкою «Друга зміна» у списку пар — пари при цьому лишаються в базі.'),
            Forms\Components\TextInput::make('number')->label('Номер пари')->numeric()->required()
                ->minValue(1)->maxValue(10)
                ->helperText('Номер у межах своєї зміни: у кожної зміни це 1, 2, 3, 4…'),
            Forms\Components\TimePicker::make('starts')->label('Початок пари')->seconds(false)->required()
                ->helperText('Дзвінок на пару. Перерва — проміжок від кінця попередньої пари до цього часу.'),
            Forms\Components\TimePicker::make('ends')->label('Кінець пари')->seconds(false)->required()
                ->helperText('Дзвінок з пари — початок перерви. Змінюючи ці часи, ви керуєте і перервами.'),
            Forms\Components\Toggle::make('is_active')->label('Активна')->default(true)
                ->helperText('Неактивні пари не показуються на сайті.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shift')->label('Зміна')->badge()
                    ->formatStateUsing(fn ($state) => static::shiftOptions()[$state] ?? $state)
                    ->color(fn ($state) => $state === 2 ? 'warning' : 'primary'),
                Tables\Columns\TextColumn::make('number')->label('Пара')->weight('bold'),
                Tables\Columns\TextColumn::make('starts')->label('Початок')->time('H:i'),
                Tables\Columns\TextColumn::make('ends')->label('Кінець')->time('H:i'),
                Tables\Columns\IconColumn::make('is_active')->label('Активна')->boolean(),
            ])
            ->defaultSort(fn (Builder $query) => $query->orderBy('shift')->orderBy('number'))
            ->filters([
                Tables\Filters\SelectFilter::make('shift')->label('Зміна')->options(static::shiftOptions()),
            ])
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
            'index' => Pages\ListBellPeriods::route('/'),
            'create' => Pages\CreateBellPeriod::route('/create'),
            'edit' => Pages\EditBellPeriod::route('/{record}/edit'),
        ];
    }
}
