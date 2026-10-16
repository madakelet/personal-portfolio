<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminPostsTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return [
            'title' => 'Fermenting hot sauce',
            'slug' => 'fermenting-hot-sauce',
            'locale' => 'en',
            'excerpt' => 'Three weeks in a jar.',
            'body' => "## Ingredients\n\nChillies, salt, water.",
            'published_at' => null,
            ...$overrides,
        ];
    }

    public function test_guests_cannot_reach_the_posts_admin(): void
    {
        $post = Post::factory()->create();

        $this->get('/admin/posts')->assertRedirect('/login');
        $this->post('/admin/posts', $this->payload())->assertRedirect('/login');
        $this->put("/admin/posts/{$post->id}", $this->payload())->assertRedirect('/login');
        $this->delete("/admin/posts/{$post->id}")->assertRedirect('/login');
        $this->post('/admin/posts/preview', ['body' => '# x'])->assertRedirect('/login');

        $this->assertSame(1, Post::count());
    }

    public function test_index_lists_drafts_first_with_status(): void
    {
        Post::factory()->create(['title' => 'Old post']);
        Post::factory()->draft()->create(['title' => 'Draft post']);
        Post::factory()->scheduled()->create(['title' => 'Later post']);

        $this->actingAs(User::factory()->create())->get('/admin/posts')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Posts/Index')
                ->has('posts', 3)
                ->where('posts.0.title', 'Draft post')
                ->where('posts.0.status', 'draft')
                ->where('posts.0.url', null)
                ->where('posts.1.status', 'scheduled')
                ->where('posts.2.status', 'published'));
    }

    public function test_a_draft_is_saved_but_not_public(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/posts', $this->payload())
            ->assertRedirect('/admin/posts/'.Post::first()->id.'/edit');

        $this->assertSame('draft', Post::first()->status());
        $this->get('/en/blog/fermenting-hot-sauce')->assertNotFound();
    }

    public function test_a_post_with_a_past_date_is_published(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/posts', $this->payload(['published_at' => now()->subMinute()->toIso8601String()]));

        $this->get('/en/blog/fermenting-hot-sauce')->assertOk()->assertSee('<h2>Ingredients</h2>', false);
    }

    public function test_the_browser_offset_is_respected(): void
    {
        // 10:00 in Brussels (summer time) is 08:00 UTC.
        $this->actingAs(User::factory()->create())
            ->post('/admin/posts', $this->payload(['published_at' => '2026-07-01T10:00:00+02:00']));

        $this->assertSame('2026-07-01 08:00:00', Post::first()->published_at->utc()->format('Y-m-d H:i:s'));
    }

    public function test_slug_must_be_unique_and_url_safe(): void
    {
        Post::factory()->create(['slug' => 'taken']);
        $this->actingAs(User::factory()->create())->from('/admin/posts/create');

        $this->post('/admin/posts', $this->payload(['slug' => 'taken']))->assertSessionHasErrors('slug');
        $this->post('/admin/posts', $this->payload(['slug' => 'Not OK!']))->assertSessionHasErrors('slug');
        $this->post('/admin/posts', $this->payload(['slug' => 'double--dash']))->assertSessionHasErrors('slug');
        $this->post('/admin/posts', $this->payload(['title' => '', 'body' => '', 'locale' => 'de']))
            ->assertSessionHasErrors(['title', 'body', 'locale']);

        $this->assertSame(1, Post::count());
    }

    public function test_a_post_can_be_edited_keeping_its_own_slug(): void
    {
        $post = Post::factory()->create(['slug' => 'helm-tips']);

        $this->actingAs(User::factory()->create())
            ->put("/admin/posts/{$post->id}", $this->payload(['slug' => 'helm-tips', 'title' => 'Helm tips, revised', 'body' => 'New *text*.']))
            ->assertSessionHasNoErrors();

        $post->refresh();
        $this->assertSame('Helm tips, revised', $post->title);
        $this->assertStringContainsString('<em>text</em>', $post->body_html);
        $this->assertNull($post->published_at); // payload sent no date: back to draft
    }

    public function test_a_post_can_be_deleted(): void
    {
        $post = Post::factory()->create();

        $this->actingAs(User::factory()->create())->delete("/admin/posts/{$post->id}")->assertRedirect('/admin/posts');

        $this->assertSame(0, Post::count());
    }

    public function test_preview_renders_markdown_like_the_site(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/admin/posts/preview', ['body' => "**bold**\n\n```php\n\$a = 1;\n```"])
            ->assertOk()
            ->assertJsonPath('html', fn ($html) => str_contains($html, '<strong>bold</strong>') && str_contains($html, 'hl-variable'));
    }

    public function test_edit_page_receives_the_post(): void
    {
        $post = Post::factory()->create(['title' => 'Kimcsi', 'locale' => 'hu']);

        $this->actingAs(User::factory()->create())->get("/admin/posts/{$post->id}/edit")
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Posts/Edit')
                ->where('post.title', 'Kimcsi')
                ->where('post.locale', 'hu')
                ->where('post.status', 'published')
                ->where('locales.hu', 'Magyar'));
    }
}
