<?php

namespace App\Services;

use App\Models\News;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TelegramPoster
{
    /**
     * Публікує новину в Telegram-канал коледжу (якщо автопостинг увімкнено).
     * Повертає true при успішній відправці.
     */
    public static function post(News $news): bool
    {
        if (Setting::get('telegram_autopost') !== '1') {
            return false;
        }

        $token = trim((string) Setting::get('telegram_bot_token'));
        $channel = trim((string) Setting::get('telegram_channel'));

        if ($token === '' || $channel === '') {
            return false;
        }

        $url = route('news.show', $news);
        $excerpt = filled($news->excerpt) ? Str::limit(strip_tags($news->excerpt), 200) : '';

        // HTML-розмітка Telegram: <b>назва</b> + анотація + посилання
        $caption = '<b>' . e($news->title) . '</b>'
            . ($excerpt !== '' ? "\n\n" . e($excerpt) : '')
            . "\n\n" . '<a href="' . $url . '">Читати на сайті →</a>';

        try {
            if ($news->cover_image) {
                $resp = Http::timeout(20)->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                    'chat_id' => $channel,
                    'photo' => asset('storage/' . $news->cover_image),
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                ]);
            } else {
                $resp = Http::timeout(20)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $channel,
                    'text' => $caption,
                    'parse_mode' => 'HTML',
                ]);
            }

            return $resp->successful() && ($resp->json('ok') === true);
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
