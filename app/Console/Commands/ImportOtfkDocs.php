<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportOtfkDocs extends Command
{
    protected $signature = 'otfk:import-docs {--dry-run : Лише показати знайдені PDF, без завантаження}';

    protected $description = 'Імпорт PDF-документів зі старого сайту otfk.od.ua у розділ «Публічна інформація»';

    private string $base = 'https://otfk.od.ua';

    /** Сторінка старого сайту => slug нашої категорії документів. */
    private array $map = [
        '/public_information/provision/' => 'normatyvna-baza',
        '/public_information/contracts/' => 'dohovory',
        '/public_information/reports/' => 'zvity',
        '/public_information/work_plan/' => 'plan-roboty',
        '/public_information/to_discuss/' => 'do-obhovorennya',
        '/public_information/student_survey/' => 'rezultaty-opytuvannya',
        '/public_information/monitoring_of_edu_quality_indicators/' => 'monitorynh-yakosti-osvity',
        '/public_information/election_of_director_otk/' => 'vybory-dyrektora',
        '/public_information/election_of_rector/' => 'vybory-rektora',
        '/public_information/public_organization/' => 'hromadska-orhanizatsiya',
        '/public_information/classroom_foundation/' => 'audytornyy-fond',
        '/public_information/budget_of_the_college/' => 'byudzhet-koledzhu',
        '/public_information/financial_report/' => 'finansovyy-zvit',
        '/public_information/information_about_received_charitable/' => 'blahodiyna-dopomoha',
        '/public_information/conditions_of_inclusiveness/' => 'vysnovok-inklyuzyvnist',
        '/public_information/vacant_positions/' => 'vakantni-posady',
        '/public_information/civil_control_labor_protection/' => 'tsyvilnyy-zahyst-ohorona-pratsi',
    ];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $found = 0;
        $done = 0;

        foreach ($this->map as $path => $catSlug) {
            $category = DocumentCategory::where('slug', $catSlug)->first();
            if (! $category) {
                $this->warn("Категорія не знайдена: {$catSlug}");
                continue;
            }

            $this->line("\n<info>{$category->title}</info>  <- {$path}");

            try {
                $html = Http::withoutVerifying()->timeout(30)->get($this->base . $path)->body();
            } catch (\Throwable $e) {
                $this->error('  Сторінка недоступна: ' . $e->getMessage());
                continue;
            }

            // <a href="...pdf">Текст</a>
            preg_match_all('/<a[^>]+href=["\']([^"\']+\.pdf)["\'][^>]*>(.*?)<\/a>/is', $html, $m, PREG_SET_ORDER);

            if (empty($m)) {
                $this->line('  (PDF не знайдено)');
                continue;
            }

            $order = (int) $category->documents()->max('sort_order');
            $seen = [];

            foreach ($m as $row) {
                $url = $this->absolute(html_entity_decode($row[1]), $path);
                if (isset($seen[$url])) {
                    continue;
                }
                $seen[$url] = true;

                $title = Str::of(strip_tags($row[2]))->squish()->limit(240)->value();
                if ($title === '') {
                    $title = Str::of(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_FILENAME))
                        ->replace(['_', '-'], ' ')->squish()->ucfirst()->value();
                }
                $found++;

                if (Document::where('document_category_id', $category->id)->where('title', $title)->exists()) {
                    $this->line("  = вже є: {$title}");
                    continue;
                }

                if ($dry) {
                    $this->line("  + (dry) {$title}");
                    $done++;
                    continue;
                }

                try {
                    $bytes = Http::withoutVerifying()->timeout(90)->get($url)->body();
                    if (strlen($bytes) < 200) {
                        $this->warn("  ! порожній/недоступний: {$url}");
                        continue;
                    }
                    $stored = 'documents/' . $category->slug . '/' . Str::random(8) . '.pdf';
                    Storage::disk('public')->put($stored, $bytes);

                    Document::create([
                        'document_category_id' => $category->id,
                        'title' => $title,
                        'file_path' => $stored,
                        'published_at' => now(),
                        'sort_order' => ++$order,
                        'is_published' => true,
                    ]);
                    $this->info("  + {$title}");
                    $done++;
                } catch (\Throwable $e) {
                    $this->error("  ! помилка {$url}: " . $e->getMessage());
                }
            }
        }

        $this->newLine();
        $this->info("Знайдено PDF: {$found}. " . ($dry ? 'Буде імпортовано' : 'Імпортовано') . ": {$done}.");

        return self::SUCCESS;
    }

    /** Перетворити посилання на абсолютний URL. */
    private function absolute(string $link, string $pagePath): string
    {
        if (Str::startsWith($link, 'http')) {
            return $link;
        }
        if (Str::startsWith($link, '//')) {
            return 'https:' . $link;
        }
        if (Str::startsWith($link, '/')) {
            return $this->base . $link;
        }
        $dir = rtrim($pagePath, '/');

        return $this->base . $dir . '/' . ltrim($link, './');
    }
}
