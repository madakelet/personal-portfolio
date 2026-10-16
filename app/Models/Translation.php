<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * A text edited in the admin, overriding the lang file default.
 *
 * @property string $locale
 * @property string $group
 * @property string $key
 * @property string $value
 */
#[Fillable(['locale', 'group', 'key', 'value'])]
class Translation extends Model
{
    public static function cacheKey(string $locale, string $group): string
    {
        return "translations.{$locale}.{$group}";
    }

    /** @return array<string, string> dot-path key => value */
    public static function overridesFor(string $locale, string $group): array
    {
        return static::query()
            ->where('locale', $locale)
            ->where('group', $group)
            ->pluck('value', 'key')
            ->all();
    }

    protected static function booted(): void
    {
        $forget = fn (Translation $t) => Cache::forget(static::cacheKey($t->locale, $t->group));

        static::saved($forget);
        static::deleted($forget);
    }
}
