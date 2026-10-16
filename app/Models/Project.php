<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A project on the landing page. Title, meta and body are stored per
 * locale: ['en' => '...', 'nl' => '...', 'hu' => '...'].
 *
 * @property int $position
 * @property array<string, string|null> $title
 * @property array<string, string|null> $meta
 * @property array<string, string|null> $body
 */
#[Fillable(['position', 'title', 'meta', 'body'])]
class Project extends Model
{
    public const FIELDS = ['title', 'meta', 'body'];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'meta' => 'array',
            'body' => 'array',
        ];
    }

    /** @param  Builder<Project>  $query */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('position')->orderBy('id');
    }

    /** The field in the current locale, falling back to English when empty. */
    public function translated(string $field, ?string $locale = null): string
    {
        $values = $this->{$field} ?? [];
        $locale ??= app()->getLocale();

        return (string) (($values[$locale] ?? '') !== '' ? $values[$locale] : ($values['en'] ?? ''));
    }
}
