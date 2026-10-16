<?php

namespace Tests\Feature;

use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ExportTextsTest extends TestCase
{
    use RefreshDatabase;

    private string $langCopy;

    protected function setUp(): void
    {
        parent::setUp();

        // Work on a copy so the real lang files are never touched.
        $this->langCopy = sys_get_temp_dir().'/lang-export-test-'.uniqid();
        File::copyDirectory(lang_path(), $this->langCopy);
        $this->app->useLangPath($this->langCopy);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->langCopy);
        parent::tearDown();
    }

    public function test_export_writes_overrides_into_the_lang_file(): void
    {
        $before = require $this->langCopy.'/hu/cv.php';
        Translation::create(['locale' => 'hu', 'group' => 'cv', 'key' => 'experience.1.body', 'value' => "Új szöveg, 'idézőjellel'."]);

        $this->artisan('texts:export')->assertSuccessful();

        $after = require $this->langCopy.'/hu/cv.php';
        $this->assertSame("Új szöveg, 'idézőjellel'.", $after['experience'][1]['body']);

        // Everything else is unchanged, including list shape and null end dates.
        $before['experience'][1]['body'] = $after['experience'][1]['body'];
        $this->assertSame($before, $after);
        $this->assertStringContainsString('Hungarian translation of lang/en/cv.php', File::get($this->langCopy.'/hu/cv.php'));
        $this->assertSame(1, Translation::count());
    }

    public function test_clear_removes_the_exported_overrides(): void
    {
        Translation::create(['locale' => 'en', 'group' => 'ui', 'key' => 'nav.about', 'value' => 'About me']);

        $this->artisan('texts:export', ['--clear' => true])->assertSuccessful();

        $this->assertSame('About me', (require $this->langCopy.'/en/ui.php')['nav']['about']);
        $this->assertSame(0, Translation::count());
    }

    public function test_edits_to_removed_texts_are_not_written_back(): void
    {
        Translation::create(['locale' => 'hu', 'group' => 'ui', 'key' => 'nav.removed_link', 'value' => 'Régi']);

        $this->artisan('texts:export')->assertSuccessful();

        $this->assertArrayNotHasKey('removed_link', (require $this->langCopy.'/hu/ui.php')['nav']);
    }

    public function test_nothing_to_export(): void
    {
        $this->artisan('texts:export')->expectsOutput('No edited texts to export.')->assertSuccessful();
    }
}
