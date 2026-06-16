<?php

namespace App\Observers;

use App\Jobs\PostNewsToTelegram;
use App\Models\News;
use App\Services\TelegramPoster;

class NewsObserver
{
    /**
     * Автопостинг новини в Telegram — рівно один раз, коли вона стає «живою».
     *
     * Зовнішній HTTP-запит свідомо винесено з циклу збереження: позначку
     * telegram_posted_at ставимо одразу (атомарний захист від дублів при
     * повторному збереженні), а сам пост летить після віддачі відповіді
     * (afterResponse) — миттєве «Зберегти» в адмінці, без черги й воркера.
     */
    public function saved(News $news): void
    {
        if (! $this->shouldConsider($news)) {
            return;
        }

        // Атомарно «застовпити» постинг: позначку ставимо лише якщо вона ще порожня.
        // Друге (конкурентне) збереження оновить 0 рядків і не дублюватиме пост у канал.
        $claimed = News::whereKey($news->getKey())
            ->whereNull('telegram_posted_at')
            ->update(['telegram_posted_at' => now()]);

        if ($claimed === 0) {
            return;
        }

        $news->telegram_posted_at = now();

        PostNewsToTelegram::dispatch($news)->afterResponse();
    }

    private function shouldConsider(News $news): bool
    {
        if ($news->telegram_posted_at !== null) {
            return false;
        }

        $isLive = $news->is_published
            && ($news->published_at === null || $news->published_at->lte(now()));

        return $isLive && TelegramPoster::enabled();
    }
}
