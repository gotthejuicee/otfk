<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Support\ViewOnSite;
use App\Models\Page;
use App\Support\UniqueSlug;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Структура сайту';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Сторінки';
    protected static ?string $modelLabel = 'сторінку';
    protected static ?string $pluralModelLabel = 'Сторінки';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Контент')->schema([
                Forms\Components\TextInput::make('title')->label('Назва сторінки')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->label('URL (slug)')->maxLength(255)
                    ->prefix(url('/') . '/')
                    ->helperText('Залиште порожнім - згенерується автоматично.'),
                Forms\Components\Select::make('parent_id')->label('Батьківський розділ')
                    ->relationship('parent', 'title')->searchable()->preload(),
                Forms\Components\Textarea::make('excerpt')->label('Короткий опис')->rows(2)->columnSpanFull(),
                Forms\Components\Toggle::make('is_heritage')
                    ->label('Урочисте оформлення (heritage)')
                    ->helperText('Увімкніть для сторінок історії, хроніки та ювілейних матеріалів — стиль «листа» на сайті.')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Ключова сторінка розділу')
                    ->helperText('На сторінці батьківського розділу така сторінка виноситься нагору окремою великою карткою.')
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('body')->label('Основний текст')
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('pages')
                    ->fileAttachmentsVisibility('public')
                    ->helperText('Зображення можна вставляти просто в текст — кнопкою прикріплення в редакторі.')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('cover_image')->label('Зображення')->image()->directory('pages')->imageEditor()->imageResizeMode('contain')->imageResizeTargetWidth('1600')->imageResizeTargetHeight('1600')->columnSpanFull(),
            ])->columns(2),
            Forms\Components\Section::make('Налаштування')->schema([
                Forms\Components\Toggle::make('is_published')->label('Опубліковано')->default(true),
                Forms\Components\TextInput::make('sort_order')->label('Порядок')->numeric()->default(0),
                Forms\Components\TextInput::make('section')->label('Розділ (службове поле)')->maxLength(255),
                Forms\Components\TextInput::make('meta_title')->label('SEO-заголовок')->maxLength(255),
                Forms\Components\Textarea::make('meta_description')->label('SEO-опис')->rows(2)->maxLength(500)->columnSpanFull(),
            ])->columns(2)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Назва')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('parent.title')->label('Розділ')->badge()->placeholder('-')->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('URL')->color('gray')->toggleable(),
                Tables\Columns\ToggleColumn::make('is_published')->label('Опубл.'),
                Tables\Columns\IconColumn::make('is_heritage')->label('Heritage')->boolean()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_featured')->label('Ключова')->boolean()->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')->label('Порядок')->numeric()->sortable()->toggleable(),
            ])
            ->defaultSort('title')
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')->label('Розділ')
                    ->relationship('parent', 'title')->searchable()->preload(),
                Tables\Filters\TernaryFilter::make('is_published')->label('Публікація')
                    ->trueLabel('Опубліковані')->falseLabel('Лише чернетки')->placeholder('Всі'),
            ])
            ->emptyStateHeading('Сторінок ще немає')
            ->emptyStateDescription('Сторінки - це постійні розділи сайту: «Історія», «Бібліотека», «Абітурієнту» тощо. Створіть першу сторінку, і вона зʼявиться на сайті за своєю адресою.')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make()
                    ->label('Дублювати')
                    ->beforeReplicaSaved(function (Page $replica, Page $record) {
                        $replica->title = $record->title . ' (копія)';
                        $replica->slug = UniqueSlug::copyOf(Page::class, $record->slug);
                        $replica->is_published = false;
                    })
                    ->successRedirectUrl(fn (Page $replica) => static::getUrl('edit', ['record' => $replica]))
                    ->successNotificationTitle('Копію створено чернеткою'),
                ViewOnSite::table(fn (Page $record) => url('/' . $record->slug)),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
