<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\Specialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Редизайн спеціальностей: світла шапка з лічильником, картки з кодом замість обкладинки, CTA заявка/квіз. */
class SpecialtyPageTest extends TestCase
{
    use RefreshDatabase;

    /** Демо-спеціальності з сидера прибираємо — сторінки перевіряємо на власному наборі. */
    protected function setUp(): void
    {
        parent::setUp();

        Program::query()->delete();
        Specialty::query()->delete();
    }

    private function makeSpecialty(string $title, string $slug, string $code, int $sort = 0): Specialty
    {
        return Specialty::create([
            'title' => $title,
            'slug' => $slug,
            'code' => $code,
            'short_description' => "Короткий опис спеціальності {$title}.",
            'description' => "<p>Розгорнутий опис спеціальності {$title}.</p>",
            'degree' => 'Фаховий молодший бакалавр',
            'study_form' => 'Денна, заочна',
            'duration' => 'на основі 9 класів - 3 р. 10 міс.',
            'sort_order' => $sort,
            'is_published' => true,
        ]);
    }

    public function test_index_has_light_header_with_counter_and_code_cards(): void
    {
        $this->makeSpecialty('Інженерія програмного забезпечення', 'inzheneriya-pz', '121', 0);
        $this->makeSpecialty('Комп’ютерна інженерія', 'kompyuterna-inzheneriya', '123', 1);

        $this->get('/spetsialnosti')
            ->assertOk()
            // Світла шапка розділу замість navy-героя
            ->assertSee('bg-slate-50/80', false)
            ->assertSee('text-brand-950 sm:text-4xl lg:text-[2.75rem]">Наші спеціальності</h1>', false)
            // Лічильник з українським відмінюванням
            ->assertSee('спеціальності')
            // Код спеціальності — головний візуальний акцент картки замість фото
            ->assertSee('font-display text-4xl font-extrabold leading-none text-white sm:text-5xl">121', false)
            ->assertSee('Фаховий молодший бакалавр')
            ->assertSee('на основі 9 класів - 3 р. 10 міс.');
    }

    /** Лічильник відмінюється: 1 спеціальність / 2–4 спеціальності / 5+ спеціальностей. */
    public function test_index_counter_uses_ukrainian_plural_forms(): void
    {
        $this->makeSpecialty('Єдина спеціальність', 'yedyna', '121', 0);
        $this->get('/spetsialnosti')->assertOk()->assertSee('спеціальність');

        for ($i = 2; $i <= 5; $i++) {
            $this->makeSpecialty("Спеціальність {$i}", "spets-{$i}", "12{$i}", $i);
        }

        $this->get('/spetsialnosti')->assertOk()->assertSee('спеціальностей');
    }

    /** Іконка напряму — декоративна, підбирається за кодом і має безпечний відкат. */
    public function test_specialty_icon_falls_back_for_unknown_code(): void
    {
        $this->assertSame('code-bracket', $this->makeSpecialty('ІПЗ', 'ipz', '121')->icon_name);
        $this->assertSame('computer-desktop', $this->makeSpecialty('Інше ІТ', 'inshe-it', '126')->icon_name);
        $this->assertSame('academic-cap', $this->makeSpecialty('Без коду', 'bez-kodu', '')->icon_name);
    }

    public function test_index_shows_program_title_and_final_cta(): void
    {
        $specialty = $this->makeSpecialty('Харчові технології', 'kharchovi', '181', 0);

        Program::create([
            'specialty_id' => $specialty->id,
            'title' => 'Освітньо-професійна програма «Харчові технології»',
            'sort_order' => 0,
        ]);

        $this->get('/spetsialnosti')
            ->assertOk()
            ->assertSee('Освітньо-професійна програма «Харчові технології»')
            // Фінальний блок веде на заявку та квіз
            ->assertSee('Готові зробити перший крок до професії?')
            ->assertSee(route('applicants.create'), false)
            ->assertSee(route('quiz'), false);
    }

    public function test_index_empty_state(): void
    {
        $this->makeSpecialty('Чернетка', 'chernetka', '999')->update(['is_published' => false]);

        $this->get('/spetsialnosti')
            ->assertOk()
            ->assertSee('Перелік спеціальностей незабаром буде додано.')
            ->assertDontSee('Чернетка')
            ->assertDontSee('Готові зробити перший крок до професії?');
    }

    public function test_show_has_navy_header_with_giant_code_and_chips(): void
    {
        $specialty = $this->makeSpecialty('Інженерія програмного забезпечення', 'inzheneriya-pz', '121', 0);

        $this->get(route('specialties.show', $specialty))
            ->assertOk()
            // Гігантський код і іконка напряму замість фото-обкладинки
            ->assertSee('font-display text-[9rem] font-extrabold leading-none text-white/10 2xl:text-[11rem]">121', false)
            ->assertSee('Код спеціальності')
            // Чіпи характеристик навчання
            ->assertSee('Фаховий молодший бакалавр')
            ->assertSee('Денна, заочна')
            // Розмітка Course лишається
            ->assertSee('"@type":"Course"', false);
    }

    public function test_show_sidebar_links_to_prefilled_application_and_quiz(): void
    {
        $specialty = $this->makeSpecialty('Облік і оподаткування', 'oblik', '071', 0);

        $this->get(route('specialties.show', $specialty))
            ->assertOk()
            // Заявка відкривається з уже обраною спеціальністю
            ->assertSee(route('applicants.create', ['specialty_id' => $specialty->id]), false)
            ->assertSee('Пройти квіз на вибір спеціальності')
            ->assertSee(route('quiz'), false)
            // Смуга з виходом на приймальну комісію
            ->assertSee('Потрібна консультація?')
            ->assertSee(route('contacts'), false);
    }

    public function test_show_lists_other_specialties_as_cards(): void
    {
        $specialty = $this->makeSpecialty('Харчові технології', 'kharchovi', '181', 0);
        $this->makeSpecialty('Комп’ютерна інженерія', 'kompyuterna-inzheneriya', '123', 1);

        $this->get(route('specialties.show', $specialty))
            ->assertOk()
            ->assertSee('Інші спеціальності')
            ->assertSee('Комп’ютерна інженерія')
            ->assertSee(route('specialties.show', 'kompyuterna-inzheneriya'), false);
    }

    public function test_show_hides_other_specialties_block_when_alone(): void
    {
        $specialty = $this->makeSpecialty('Єдина спеціальність', 'yedyna', '121', 0);

        $this->get(route('specialties.show', $specialty))
            ->assertOk()
            ->assertDontSee('Інші спеціальності');
    }
}
