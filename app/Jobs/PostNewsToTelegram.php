<?php

namespace App\Jobs;

use App\Models\News;
use App\Services\TelegramPoster;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Відправка новини в Telegram-канал. Диспатчиться через ->afterResponse(),
 * тож виконується вже після віддачі сторінки адміну — і не потребує
 * запущеного черго-воркера (важливо для шаред-хостингу).
 */
class PostNewsToTelegram
{
    use Dispatchable, Queueable;

    public function __construct(public News $news)
    {
    }

    public function handle(): void
    {
        TelegramPoster::post($this->news);
    }
}
