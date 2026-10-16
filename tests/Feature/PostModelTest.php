<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_markdown_is_rendered_on_save_with_highlighted_code(): void
    {
        $post = Post::factory()->create(['body' => "Intro with `inline` code.\n\n```php\n\$x = 1;\n```\n\n> A callout."]);

        $this->assertStringContainsString('<code>inline</code>', $post->body_html);
        $this->assertStringContainsString('hl-variable', $post->body_html);
        $this->assertStringContainsString('<blockquote>', $post->body_html);

        $post->update(['body' => '# Changed']);
        $this->assertStringContainsString('<h1>Changed</h1>', $post->fresh()->body_html);
    }

    public function test_unsafe_links_are_dropped(): void
    {
        $post = Post::factory()->create(['body' => '[click](javascript:alert(1))']);

        $this->assertStringNotContainsString('javascript:', $post->body_html);
    }

    public function test_only_past_publish_dates_count_as_published(): void
    {
        $published = Post::factory()->create();
        Post::factory()->draft()->create();
        Post::factory()->scheduled()->create();

        $this->assertSame([$published->id], Post::published()->pluck('id')->all());
    }

    public function test_status_reflects_the_publish_date(): void
    {
        $this->assertSame('draft', Post::factory()->draft()->make()->status());
        $this->assertSame('scheduled', Post::factory()->scheduled()->make()->status());
        $this->assertSame('published', Post::factory()->make()->status());
    }

    public function test_older_and_newer_skip_drafts_and_scheduled_posts(): void
    {
        $old = Post::factory()->create(['published_at' => now()->subDays(30)]);
        Post::factory()->draft()->create();
        $mid = Post::factory()->create(['published_at' => now()->subDays(20)]);
        Post::factory()->scheduled()->create();
        $new = Post::factory()->create(['published_at' => now()->subDays(10)]);

        $this->assertTrue($mid->older()->is($old));
        $this->assertTrue($mid->newer()->is($new));
        $this->assertNull($old->older());
        $this->assertNull($new->newer());
    }
}
