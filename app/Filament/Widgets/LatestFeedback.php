<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\FeedbackMessageResource;
use App\Models\FeedbackMessage;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestFeedback extends BaseWidget
{
    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Останні звернення з форми зворотного звʼязку';

    public function table(Table $table): Table
    {
        return $table
            ->query(FeedbackMessage::query()->latest())
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\IconColumn::make('is_read')->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')->trueColor('success')
                    ->falseIcon('heroicon-o-bell-alert')->falseColor('warning'),
                Tables\Columns\TextColumn::make('name')->label('Імʼя')->weight('bold')->searchable(),
                Tables\Columns\TextColumn::make('subject')->label('Тема')->limit(28)->placeholder('-'),
                Tables\Columns\TextColumn::make('message')->label('Повідомлення')->limit(45)->color('gray'),
                Tables\Columns\TextColumn::make('created_at')->label('Отримано')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')->label('Переглянути')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (FeedbackMessage $record) => FeedbackMessageResource::getUrl('edit', ['record' => $record])),
            ])
            ->emptyStateHeading('Звернень ще немає')
            ->emptyStateIcon('heroicon-o-inbox');
    }
}
