<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectsOnHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_migration_brings_over_the_four_projects_in_order(): void
    {
        $this->assertSame(4, Project::count());

        $this->get('/en')->assertSeeInOrder([
            'Automated bug validation with Claude',
            'Asset and contract management platform',
            'Worksheet manager for a car repair shop',
            'L2 process documentation and automation',
        ]);
        $this->get('/hu')->assertSee('Automatikus hibavalidálás Claude-dal');
    }

    public function test_projects_follow_their_position(): void
    {
        Project::query()->ordered()->first()->update(['position' => 99]);

        $this->get('/en')->assertSeeInOrder(['Asset and contract management platform', 'Automated bug validation with Claude']);
    }

    public function test_an_empty_translation_falls_back_to_english(): void
    {
        Project::query()->delete();
        Project::create([
            'position' => 0,
            'title' => ['en' => 'Only in English', 'nl' => '', 'hu' => null],
            'meta' => ['en' => '2026'],
            'body' => ['en' => 'Body.', 'nl' => 'Inhoud.'],
        ]);

        $this->get('/nl')->assertSee('<strong>Only in English</strong>', false)->assertSee('Inhoud.');
    }

    public function test_the_section_is_hidden_without_projects(): void
    {
        Project::query()->delete();

        $this->get('/en')->assertOk()->assertDontSee('id="projects"', false);
    }
}
