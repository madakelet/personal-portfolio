<?php

namespace Tests\Feature;

use App\Models\Translation;
use App\Translation\DatabaseOverrideLoader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationOverrideTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_override_loader_is_in_use(): void
    {
        $this->assertInstanceOf(DatabaseOverrideLoader::class, app('translation.loader'));
    }

    public function test_an_override_replaces_the_file_default_on_the_page(): void
    {
        Translation::create(['locale' => 'nl', 'group' => 'ui', 'key' => 'sections.experience', 'value' => 'Werkervaring']);

        $this->get('/nl')->assertSee('Werkervaring')->assertDontSee('>Ervaring<', false);
        $this->get('/en')->assertSee('Experience');
    }

    public function test_an_override_inside_a_list_keeps_the_rest_of_the_list(): void
    {
        Translation::create(['locale' => 'en', 'group' => 'cv', 'key' => 'experience.1.body', 'value' => 'Edited freelance text.']);

        $experience = __('cv.experience');

        $this->assertCount(3, $experience);
        $this->assertSame('Edited freelance text.', $experience[1]['body']);
        $this->assertSame('Freelance Developer', $experience[1]['title']);
        $this->assertStringStartsWith('Optimy runs on AWS', $experience[0]['body']);
    }

    public function test_deleting_an_override_falls_back_to_the_file(): void
    {
        $row = Translation::create(['locale' => 'en', 'group' => 'ui', 'key' => 'nav.about', 'value' => 'Me']);
        $this->get('/en')->assertSee('>Me</a>', false);

        $row->delete();
        app('translator')->setLoaded([]);

        $this->get('/en')->assertSee('>About</a>', false);
    }

    public function test_saving_clears_the_cached_overrides(): void
    {
        $this->get('/en')->assertSee('>About</a>', false);

        Translation::create(['locale' => 'en', 'group' => 'ui', 'key' => 'nav.about', 'value' => 'Me']);
        app('translator')->setLoaded([]);

        $this->get('/en')->assertSee('>Me</a>', false);
    }
}
