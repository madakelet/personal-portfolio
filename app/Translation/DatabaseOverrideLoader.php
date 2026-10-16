<?php

namespace App\Translation;

use App\Models\Translation;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * Wraps Laravel's file loader: lang files stay the defaults, and texts
 * edited in the admin (the translations table) are laid on top.
 */
class DatabaseOverrideLoader implements Loader
{
    public function __construct(private Loader $files) {}

    public function load($locale, $group, $namespace = null)
    {
        $lines = $this->files->load($locale, $group, $namespace);

        // Only the app's own PHP groups (cv, ui); not JSON or package translations.
        if ($group === '*' || ($namespace !== null && $namespace !== '*')) {
            return $lines;
        }

        foreach ($this->overrides($locale, $group) as $key => $value) {
            Arr::set($lines, $key, $value);
        }

        return $lines;
    }

    /** @return array<string, string> */
    private function overrides(string $locale, string $group): array
    {
        try {
            return Cache::rememberForever(
                Translation::cacheKey($locale, $group),
                fn () => Translation::overridesFor($locale, $group),
            );
        } catch (QueryException) {
            // Not migrated yet (fresh install, tests without a database): files only.
            return [];
        }
    }

    public function addNamespace($namespace, $hint)
    {
        $this->files->addNamespace($namespace, $hint);
    }

    public function addJsonPath($path)
    {
        $this->files->addJsonPath($path);
    }

    public function namespaces()
    {
        return $this->files->namespaces();
    }
}
