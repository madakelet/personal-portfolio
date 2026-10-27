<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Translation;
use App\Translation\TextCatalog;
use Database\Seeders\EnglishProjectSeeder;
use Database\Seeders\EnglishTextSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_seeded_text_is_editable_in_the_admin(): void
    {
        $catalog = app(TextCatalog::class);

        foreach (EnglishTextSeeder::TEXTS as $group => $texts) {
            foreach (array_keys($texts) as $key) {
                $this->assertTrue($catalog->isEditable($group, $key), "{$group}.{$key} is not editable");
            }
        }
    }

    public function test_seeding_twice_leaves_one_row_per_text_and_project(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(
            collect(EnglishTextSeeder::TEXTS)->flatten()->count(),
            Translation::where('locale', 'en')->count(),
        );
        $this->assertSame(count(EnglishProjectSeeder::PROJECTS), Project::count());
    }

    public function test_seeded_english_overrides_show_on_the_home_page(): void
    {
        $this->get('/en')->assertOk(); // warm the translation cache before seeding

        $this->seed();

        $this->get('/en')
            ->assertSee('Full-stack Developer, Creative Brand Solutions')
            ->assertSee('NFL pick&#039;em', false);
    }

    public function test_project_seeder_keeps_other_locales(): void
    {
        $this->seed(EnglishProjectSeeder::class);

        $project = Project::where('position', 1)->first();
        $this->assertSame('NFL tippjáték', $project->title['hu']);
        $this->assertSame('NFL pick\'em', $project->title['en']);
    }
}
