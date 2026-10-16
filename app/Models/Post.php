<?php

namespace App\Models;

use App\Support\Markdown;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $slug
 * @property string $title
 * @property string|null $excerpt
 * @property string $body Markdown
 * @property string $body_html rendered on save
 * @property string $locale
 * @property array<int, string>|null $tags
 * @property Carbon|null $published_at null = draft, future = scheduled
 */
#[Fillable(['slug', 'title', 'excerpt', 'body', 'locale', 'tags', 'published_at'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    public const SLUG_FORMAT = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post) {
            if ($post->isDirty('body') || $post->body_html === null) {
                $post->body_html = Markdown::render($post->body);
            }
        });
    }

    /** @param  Builder<Post>  $query */
    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /** @param  Builder<Post>  $query */
    public function scopeNewestFirst(Builder $query): void
    {
        $query->orderByDesc('published_at')->orderByDesc('id');
    }

    public function status(): string
    {
        return match (true) {
            $this->published_at === null => 'draft',
            $this->published_at->isFuture() => 'scheduled',
            default => 'published',
        };
    }

    /** The published post before this one (older), for the pager. */
    public function older(): ?Post
    {
        return static::query()->published()
            ->where(fn ($q) => $q->where('published_at', '<', $this->published_at)
                ->orWhere(fn ($q) => $q->where('published_at', $this->published_at)->where('id', '<', $this->id)))
            ->newestFirst()->first();
    }

    /** The published post after this one (newer), for the pager. */
    public function newer(): ?Post
    {
        return static::query()->published()
            ->where(fn ($q) => $q->where('published_at', '>', $this->published_at)
                ->orWhere(fn ($q) => $q->where('published_at', $this->published_at)->where('id', '>', $this->id)))
            ->orderBy('published_at')->orderBy('id')->first();
    }
}
