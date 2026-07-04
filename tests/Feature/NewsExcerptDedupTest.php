<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NewsExcerptDedupTest extends TestCase
{
    use RefreshDatabase;

    /** Тіло статті рендериться після </head> — там excerpt не має дублюватися. */
    private function bodyRegion(News $news): string
    {
        $html = $this->get(route('news.show', $news))->assertOk()->getContent();

        return Str::after($html, '</head>');
    }

    public function test_lead_hidden_when_body_starts_with_excerpt(): void
    {
        $sentinel = 'СЕНТИНЕЛ вступне речення новини';
        $news = News::create([
            'title' => 'Дубль',
            'slug' => 'dubl-news-test',
            'excerpt' => $sentinel . '.',
            'body' => "<p>{$sentinel}. Далі йде основний текст статті.</p>",
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);

        // Excerpt = початок body → лід приховано, текст трапляється рівно раз (у тілі).
        $this->assertSame(1, substr_count($this->bodyRegion($news), $sentinel));
    }

    public function test_distinct_excerpt_still_shown_as_lead(): void
    {
        $news = News::create([
            'title' => 'Окремий анонс',
            'slug' => 'okremyy-news-test',
            'excerpt' => 'ЗОВСІМ інший короткий анонс.',
            'body' => '<p>Тіло статті починається абсолютно по-іншому.</p>',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);

        // Окремий за змістом анонс лишається лідом.
        $this->assertStringContainsString('ЗОВСІМ інший короткий анонс.', $this->bodyRegion($news));
    }
}
