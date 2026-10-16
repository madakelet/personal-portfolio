<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminProjectsTest extends TestCase
{
    use RefreshDatabase;

    private function payload(string $title = 'New thing', array $extra = []): array
    {
        return array_replace_recursive([
            'title' => ['en' => $title, 'nl' => '', 'hu' => ''],
            'meta' => ['en' => '2026', 'nl' => '', 'hu' => ''],
            'body' => ['en' => 'What it does.', 'nl' => '', 'hu' => ''],
        ], $extra);
    }

    private function titles(): array
    {
        return Project::query()->ordered()->get()->map(fn ($p) => $p->title['en'])->all();
    }

    public function test_guests_cannot_manage_projects(): void
    {
        $first = Project::query()->ordered()->first();

        $this->get('/admin/projects')->assertRedirect('/login');
        $this->post('/admin/projects', $this->payload())->assertRedirect('/login');
        $this->delete("/admin/projects/{$first->id}")->assertRedirect('/login');

        $this->assertSame(3, Project::count());
    }

    public function test_index_lists_projects_in_order(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/projects')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Projects')
                ->has('projects', 3)
                ->where('projects.0.title.en', "NFL pick'em")
                ->where('projects.0.title.hu', 'NFL tippjáték'));
    }

    public function test_a_project_can_be_added_at_the_end(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/projects', $this->payload('Portfolio site', ['title' => ['hu' => 'Portfólió oldal']]))
            ->assertSessionHasNoErrors();

        $this->assertSame('Portfolio site', $this->titles()[3]);
        $this->get('/hu')->assertSee('Portfólió oldal');
        $this->get('/nl')->assertSee('Portfolio site'); // empty Dutch falls back to English
    }

    public function test_a_link_can_be_set_and_must_be_a_web_address(): void
    {
        $this->actingAs(User::factory()->create())->from('/admin/projects');
        $project = Project::query()->ordered()->first();

        $this->put("/admin/projects/{$project->id}", [...$this->payload(), 'url' => 'not a link'])->assertSessionHasErrors('url');
        $this->put("/admin/projects/{$project->id}", [...$this->payload(), 'url' => 'javascript:alert(1)'])->assertSessionHasErrors('url');
        $this->put("/admin/projects/{$project->id}", [...$this->payload(), 'url' => 'https://example.test'])->assertSessionHasNoErrors();

        $this->assertSame('https://example.test', $project->fresh()->url);
    }

    public function test_english_title_and_text_are_required(): void
    {
        $this->actingAs(User::factory()->create())
            ->from('/admin/projects')
            ->post('/admin/projects', $this->payload('', ['body' => ['en' => '']]))
            ->assertSessionHasErrors(['title.en', 'body.en']);

        $this->assertSame(3, Project::count());
    }

    public function test_a_project_can_be_edited(): void
    {
        $project = Project::query()->ordered()->first();

        $this->actingAs(User::factory()->create())
            ->put("/admin/projects/{$project->id}", $this->payload('Bug validation pipeline'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Bug validation pipeline', $project->fresh()->title['en']);
        $this->get('/en')->assertSee('Bug validation pipeline')->assertDontSee('<strong>NFL pick&#039;em</strong>', false);
    }

    public function test_removing_the_first_project_takes_it_off_the_site(): void
    {
        $first = Project::query()->ordered()->first();

        $this->actingAs(User::factory()->create())->delete("/admin/projects/{$first->id}")->assertSessionHas('status');

        $this->assertSame(2, Project::count());
        $this->assertSame([0, 1], Project::query()->ordered()->pluck('position')->all());
        $this->get('/en')->assertDontSee('<strong>NFL pick&#039;em</strong>', false);
    }

    public function test_projects_can_be_moved_up_and_down(): void
    {
        $this->actingAs(User::factory()->create());
        [$first, $second] = Project::query()->ordered()->get();

        $this->post("/admin/projects/{$first->id}/move", ['direction' => 'down']);
        $this->assertSame('Photography portfolio', $this->titles()[0]);

        $this->post("/admin/projects/{$first->id}/move", ['direction' => 'up']);
        $this->assertSame("NFL pick'em", $this->titles()[0]);

        // Already at the top: nothing changes.
        $this->post("/admin/projects/{$first->id}/move", ['direction' => 'up']);
        $this->assertSame("NFL pick'em", $this->titles()[0]);
        $this->assertSame($second->id, Project::query()->ordered()->skip(1)->first()->id);
    }
}
