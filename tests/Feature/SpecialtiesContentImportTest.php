<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\Specialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Імпорт описів спеціальностей зі старого сайту (міграція
 * 2026_08_28_210000_import_specialties_content): 9 спеціальностей
 * з реальними описами замість заглушок і 15 записів Program (ОПП).
 */
class SpecialtiesContentImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_seeds_nine_specialties_with_letter_codes(): void
    {
        $expected = [
            'inzheneriya-programnoho-zabezpechennya' => 'F2',
            'kompyuterna-inzheneriya' => 'F7',
            'kharchovi-tekhnolohiyi' => 'G13',
            'finansy-bankivska-sprava' => 'D2',
            'menedzhment' => 'D3',
            'marketynh' => 'D5',
            'torhivlya' => 'D7',
            'enerhovyrobnytstvo' => 'G4',
            'tekhnolohiyi-lehkoyi-promyslovosti' => 'G15',
        ];

        foreach ($expected as $slug => $code) {
            $specialty = Specialty::where('slug', $slug)->first();

            $this->assertNotNull($specialty, "Немає спеціальності {$slug}");
            $this->assertSame($code, $specialty->code);
            $this->assertTrue($specialty->is_published);
            $this->assertStringNotContainsString('наповнюється', strip_tags($specialty->description));
            $this->assertNotEmpty($specialty->short_description);
            $this->assertGreaterThan(0, $specialty->programs()->count(), "У {$slug} немає жодної ОПП");
        }

        $imported = Program::whereIn('specialty_id', Specialty::whereIn('slug', array_keys($expected))->pluck('id'));
        $this->assertSame(15, $imported->count());
    }

    public function test_specialty_page_shows_imported_description_and_programs(): void
    {
        $this->get('/spetsialnosti/enerhovyrobnytstvo')
            ->assertOk()
            ->assertSee('Енерговиробництво')
            ->assertSee('Холодильні і кліматичні технології')
            ->assertSee('Монтаж і обслуговування холодильно-компресорних машин та установок')
            ->assertSee('Освітньо-професійні програми');
    }

    public function test_imported_specialties_have_no_demo_programs(): void
    {
        // Демо-запис сидера допустимий лише для спеціальностей без реального
        // контенту (071); у 9 імпортованих його бути не повинно.
        $importedIds = Specialty::where('slug', '!=', 'oblik-i-opodatkuvannya')->pluck('id');

        $this->assertSame(0, Program::whereIn('specialty_id', $importedIds)
            ->where('description', 'like', 'Демонстраційний запис%')
            ->count());
    }
}
