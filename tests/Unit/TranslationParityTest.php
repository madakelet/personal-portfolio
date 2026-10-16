<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Every locale must have exactly the same keys and list lengths as English,
 * so a missing translation shows up as a failing test, not as English text
 * quietly leaking into another language.
 */
class TranslationParityTest extends TestCase
{
    public static function files(): array
    {
        $cases = [];
        foreach (glob(__DIR__.'/../../lang/*', GLOB_ONLYDIR) as $dir) {
            $locale = basename($dir);
            if ($locale === 'en') {
                continue;
            }
            foreach (['cv', 'ui'] as $file) {
                $cases["{$locale}/{$file}"] = [$locale, $file];
            }
        }

        return $cases;
    }

    #[DataProvider('files')]
    public function test_locale_file_matches_english_structure(string $locale, string $file): void
    {
        $path = lang_path("{$locale}/{$file}.php");
        $this->assertFileExists($path);

        $this->assertSame(
            $this->shape(require lang_path("en/{$file}.php")),
            $this->shape(require $path),
        );
    }

    /** Keys and nesting only; leaf values become their type. */
    private function shape(array $data): array
    {
        return array_map(fn ($v) => is_array($v) ? $this->shape($v) : get_debug_type($v), $data);
    }
}
