<?php

namespace Tests\Feature;

use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminTextsTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->create();
    }

    public function test_guests_cannot_see_or_change_texts(): void
    {
        $this->get('/admin/texts')->assertRedirect('/login');
        $this->put('/admin/texts', ['locale' => 'en', 'group' => 'ui', 'key' => 'nav.about', 'value' => 'x'])->assertRedirect('/login');

        $this->assertSame(0, Translation::count());
    }

    public function test_index_lists_texts_with_defaults_per_language(): void
    {
        Translation::create(['locale' => 'hu', 'group' => 'ui', 'key' => 'nav.about', 'value' => 'Rólam szól']);

        $this->actingAs($this->owner())->get('/admin/texts')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Texts')
                ->where('locales.nl', 'Nederlands')
                ->where('sections.0.name', 'ui')
            );

        $sections = collect($this->get('/admin/texts')->viewData('page')['props']['sections']);
        $nav = collect($sections->firstWhere('name', 'ui.nav')['rows'])->firstWhere('key', 'nav.about');

        $this->assertSame(['en' => 'About', 'nl' => 'Over mij', 'hu' => 'Rólam'], $nav['defaults']);
        $this->assertSame(['en' => null, 'nl' => null, 'hu' => 'Rólam szól'], $nav['overrides']);
    }

    public function test_technical_values_are_not_listed(): void
    {
        $keys = collect($this->actingAs($this->owner())->get('/admin/texts')->viewData('page')['props']['sections'])
            ->flatMap(fn ($s) => collect($s['rows'])->pluck('key'));

        $this->assertContains('experience.0.body', $keys);
        $this->assertNotContains('experience.0.from', $keys);
        $this->assertNotContains('dates.month', $keys);
        $this->assertNotContains('meta.og_locale', $keys);
    }

    public function test_list_items_carry_their_entry_title_as_context(): void
    {
        $rows = collect($this->actingAs($this->owner())->get('/admin/texts')->viewData('page')['props']['sections'])
            ->firstWhere('name', 'cv.experience')['rows'];

        $this->assertSame('Freelance Developer', collect($rows)->firstWhere('key', 'experience.1.body')['context']);
    }

    public function test_saving_creates_an_override_that_shows_on_the_site(): void
    {
        $this->actingAs($this->owner())
            ->from('/admin/texts')
            ->put('/admin/texts', ['locale' => 'nl', 'group' => 'ui', 'key' => 'sections.experience', 'value' => 'Werkervaring'])
            ->assertRedirect('/admin/texts')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('translations', ['locale' => 'nl', 'group' => 'ui', 'key' => 'sections.experience', 'value' => 'Werkervaring']);

        app('translator')->setLoaded([]);
        $this->get('/nl')->assertSee('Werkervaring');
    }

    public function test_saving_again_updates_the_same_row(): void
    {
        $this->actingAs($this->owner());
        $data = ['locale' => 'en', 'group' => 'cv', 'key' => 'bio.0'];

        $this->put('/admin/texts', [...$data, 'value' => 'First']);
        $this->put('/admin/texts', [...$data, 'value' => 'Second']);

        $this->assertSame(1, Translation::count());
        $this->assertSame('Second', Translation::first()->value);
    }

    public function test_saving_the_default_text_removes_the_override(): void
    {
        Translation::create(['locale' => 'en', 'group' => 'ui', 'key' => 'nav.about', 'value' => 'Me']);

        $this->actingAs($this->owner())->put('/admin/texts', ['locale' => 'en', 'group' => 'ui', 'key' => 'nav.about', 'value' => 'About']);

        $this->assertSame(0, Translation::count());
    }

    public function test_reset_deletes_the_override(): void
    {
        Translation::create(['locale' => 'hu', 'group' => 'cv', 'key' => 'bio.0', 'value' => 'Saját szöveg']);

        $this->actingAs($this->owner())
            ->delete('/admin/texts', ['locale' => 'hu', 'group' => 'cv', 'key' => 'bio.0'])
            ->assertSessionHas('status');

        $this->assertSame(0, Translation::count());
    }

    public function test_unknown_or_locked_keys_and_bad_input_are_rejected(): void
    {
        $this->actingAs($this->owner())->from('/admin/texts');

        $this->put('/admin/texts', ['locale' => 'en', 'group' => 'cv', 'key' => 'nope.key', 'value' => 'x'])->assertSessionHasErrors('key');
        $this->put('/admin/texts', ['locale' => 'en', 'group' => 'cv', 'key' => 'experience.0.from', 'value' => '1999'])->assertSessionHasErrors('key');
        $this->put('/admin/texts', ['locale' => 'de', 'group' => 'cv', 'key' => 'bio.0', 'value' => 'x'])->assertSessionHasErrors('locale');
        $this->put('/admin/texts', ['locale' => 'en', 'group' => 'auth', 'key' => 'failed', 'value' => 'x'])->assertSessionHasErrors('group');
        $this->put('/admin/texts', ['locale' => 'en', 'group' => 'cv', 'key' => 'bio.0', 'value' => ''])->assertSessionHasErrors('value');

        $this->assertSame(0, Translation::count());
    }
}
