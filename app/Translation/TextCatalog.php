<?php

namespace App\Translation;

use App\Models\Translation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * The texts the admin can edit: every string in lang/en/{cv,ui}.php,
 * minus a few technical values (CV dates, date format, OG locale).
 */
class TextCatalog
{
    public const GROUPS = ['ui', 'cv'];

    /** Keys that hold data, not copy. Editing them can break formatting. */
    private const LOCKED = ['/(^|\.)(from|to)$/', '/^dates\.month$/', '/^meta\.og_locale$/'];

    /** @return list<string> */
    public function locales(): array
    {
        return array_keys(config('site.locales'));
    }

    /** The lang file values for one locale and group, without any overrides. */
    public function defaults(string $locale, string $group): array
    {
        $path = lang_path("{$locale}/{$group}.php");

        return is_file($path) ? Arr::dot(require $path) : [];
    }

    /** @return list<string> */
    public function editableKeys(string $group): array
    {
        return collect($this->defaults('en', $group))
            ->filter(fn ($value, $key) => is_string($value) && ! $this->isLocked($key))
            ->keys()
            ->all();
    }

    public function isEditable(string $group, string $key): bool
    {
        return in_array($group, self::GROUPS, true) && in_array($key, $this->editableKeys($group), true);
    }

    /**
     * Rows for the admin, grouped into sections such as "cv.bio" or "ui.nav".
     *
     * @return list<array{name: string, rows: list<array<string, mixed>>}>
     */
    public function sections(): array
    {
        $sections = [];

        foreach (self::GROUPS as $group) {
            $defaults = collect($this->locales())->mapWithKeys(fn ($l) => [$l => $this->defaults($l, $group)]);
            $overrides = Translation::query()->where('group', $group)->get()->groupBy('locale')
                ->map(fn ($rows) => $rows->pluck('value', 'key'));

            foreach ($this->editableKeys($group) as $key) {
                // "bio.0" goes under "cv.bio"; single texts like "name" share a "ui" section.
                $section = str_contains($key, '.') ? $group.'.'.Str::before($key, '.') : $group;
                $sections[$section][] = [
                    'group' => $group,
                    'key' => $key,
                    'context' => $this->context($defaults['en'], $key),
                    'defaults' => $defaults->map(fn ($d) => $d[$key] ?? '')->all(),
                    'overrides' => collect($this->locales())->mapWithKeys(fn ($l) => [$l => $overrides[$l][$key] ?? null])->all(),
                ];
            }
        }

        return collect($sections)->map(fn ($rows, $name) => ['name' => $name, 'rows' => $rows])->values()->all();
    }

    /** For list items like "experience.1.body", the English title of that entry. */
    private function context(array $enDefaults, string $key): ?string
    {
        if (! preg_match('/^(.+\.\w+)\.(\w+)$/', $key, $m) || $m[2] === 'title') {
            return null;
        }

        return $enDefaults[$m[1].'.title'] ?? null;
    }

    private function isLocked(string $key): bool
    {
        foreach (self::LOCKED as $pattern) {
            if (preg_match($pattern, $key)) {
                return true;
            }
        }

        return false;
    }
}
