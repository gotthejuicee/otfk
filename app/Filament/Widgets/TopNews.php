<?php

namespace App\Filament\Widgets;

use App\Models\News;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopNews extends TableWidget
{
    protected static ?string $heading = 'Топ новин (перегляди та вподобайки)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(News::query()->where('is_published', true)->orderByDesc('views')->limit(7))
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Новина')->limit(70)
                    ->url(fn (News $record) => route('news.show', $record), shouldOpenInNewTab: true),
                Tables\Columns\TextColumn::make('published_at')->label('Дата')->date('d.m.Y'),
                Tables\Columns\TextColumn::make('views')->label('Перегляди')->numeric()->sortable()
                    ->icon('heroicon-o-eye')->color('gray'),
                Tables\Columns\TextColumn::make('likes')->label('Вподобайки')->numeric()->sortable()
                    ->icon('heroicon-o-heart')->color('danger'),
            ])
            ->paginated(false);
    }
}
