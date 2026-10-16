<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectsOnHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_migrations_leave_the_personal_projects_in_order(): void
    {
        $this->assertSame(3, Project::count());

        $this->get('/en')
            ->assertSeeInOrder(["NFL pick'em", 'Photography portfolio', 'This website'], false)
            ->assertDontSee('Automated bug validation with Claude')
            ->assertDontSee('Side projects');
        $this->get('/hu')->assertSeeInOrder(['NFL tippjáték', 'Fotós portfólió', 'Ez a weboldal']);
    }

    public function test_projects_follow_their_position(): void
    {
        Project::query()->ordered()->first()->update(['position' => 99]);

        $this->get('/en')->assertSeeInOrder(['Photography portfolio', "NFL pick'em"], false);
    }

    public function test_the_title_links_only_when_a_url_is_set(): void
    {
        Project::query()->ordered()->first()->update(['url' => 'https://picks.example.test']);

        $this->get('/en')
            ->assertSee('<a href="https://picks.example.test" target="_blank" rel="noopener">NFL pick&#039;em</a>', false)
            ->assertSee('<strong>Photography portfolio</strong>', false);
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
