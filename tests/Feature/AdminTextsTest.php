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
        $this->assertContains('experience.0.from', $keys);
        $this->assertContains('experience.0.to', $keys);
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
        $this->put('/admin/texts', ['locale' => 'en', 'group' => 'ui', 'key' => 'dates.month', 'value' => 'MM'])->assertSessionHasErrors('key');
        $this->put('/admin/texts', ['locale' => 'de', 'group' => 'cv', 'key' => 'bio.0', 'value' => 'x'])->assertSessionHasErrors('locale');
        $this->put('/admin/texts', ['locale' => 'en', 'group' => 'auth', 'key' => 'failed', 'value' => 'x'])->assertSessionHasErrors('group');
        $this->put('/admin/texts', ['locale' => 'en', 'group' => 'cv', 'key' => 'bio.0', 'value' => ''])->assertSessionHasErrors('value');

        $this->assertSame(0, Translation::count());
    }

    public function test_a_date_is_saved_for_every_language(): void
    {
        $this->actingAs($this->owner())
            ->put('/admin/texts', ['locale' => 'en', 'group' => 'cv', 'key' => 'experience.1.from', 'value' => '2023-04'])
            ->assertSessionHasNoErrors();

        $this->assertSame(['en', 'hu', 'nl'], Translation::where('key', 'experience.1.from')->orderBy('locale')->pluck('locale')->all());

        app('translator')->setLoaded([]);
        $this->get('/en')->assertSee('Apr 2023 – Dec 2023');
        $this->get('/hu')->assertSee('2023. ápr. – 2023. dec.');
    }

    public function test_an_empty_end_date_means_present(): void
    {
        $this->actingAs($this->owner())
            ->put('/admin/texts', ['locale' => 'en', 'group' => 'cv', 'key' => 'experience.1.to', 'value' => ''])
            ->assertSessionHasNoErrors();

        app('translator')->setLoaded([]);
        $this->get('/en')->assertSee('Mar 2023 – present');
        $this->get('/nl')->assertSee('mrt. 2023 – heden');
    }

    public function test_clearing_an_open_end_date_keeps_no_override(): void
    {
        // The Optimy entry already ends in "present" (null in the file).
        $this->actingAs($this->owner())->put('/admin/texts', ['locale' => 'en', 'group' => 'cv', 'key' => 'experience.0.to', 'value' => '']);

        $this->assertSame(0, Translation::count());
    }

    public function test_dates_must_be_year_or_year_and_month(): void
    {
        $this->actingAs($this->owner())->from('/admin/texts');

        foreach (['March 2023', '2023-13', '23', '2023-3', ''] as $bad) {
            $this->put('/admin/texts', ['locale' => 'en', 'group' => 'cv', 'key' => 'experience.1.from', 'value' => $bad])
                ->assertSessionHasErrors('value');
        }

        $this->assertSame(0, Translation::count());
    }

    public function test_resetting_a_date_clears_it_in_every_language(): void
    {
        $this->actingAs($this->owner());
        $this->put('/admin/texts', ['locale' => 'en', 'group' => 'cv', 'key' => 'education.0.from', 'value' => '2016']);
        $this->assertSame(3, Translation::count());

        $this->delete('/admin/texts', ['locale' => 'en', 'group' => 'cv', 'key' => 'education.0.from']);

        $this->assertSame(0, Translation::count());
    }
}
