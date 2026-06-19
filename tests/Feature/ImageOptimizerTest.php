<?php

namespace Tests\Feature;

use App\Models\News;
use App\Support\ImageOptimizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    use RefreshDatabase;

    public function test_optimizer_is_safe_on_missing_or_empty_paths(): void
    {
        // Жоден із цих викликів не має кидати винятків (оптимізація — допоміжна).
        $this->assertNull(ImageOptimizer::toWebp(null));
        $this->assertNull(ImageOptimizer::toWebp('news/does-not-exist.jpg'));
        $this->assertNull(ImageOptimizer::webpPath(null));

        ImageOptimizer::deleteVariants(null);
        ImageOptimizer::deleteVariants('news/nope.jpg');

        $this->assertIsBool(ImageOptimizer::canOptimize());
    }

    public function test_saving_model_with_missing_cover_does_not_break_save(): void
    {
        // Обкладинка вказує на відсутній файл — збереження новини все одно проходить
        // (важка конвертація винесена в afterResponse і обгорнута try/catch).
        News::create([
            'title' => 'Новина без файлу',
            'slug' => 'novyna-bez-faylu',
            'body' => '<p>Текст</p>',
            'cover_image' => 'news/missing.jpg',
            'published_at' => now()->subHour(),
            'is_published' => true,
        ]);

        $this->assertDatabaseHas('news', ['slug' => 'novyna-bez-faylu']);
    }
}
