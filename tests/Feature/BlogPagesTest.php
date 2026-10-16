<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_blog_link_is_always_in_the_header(): void
    {
        $link = '#<a href="'.preg_quote(url('/en/blog'), '#').'"\s*(aria-current="page")?\s*>Blog</a>#';

        $this->assertMatchesRegularExpression($link, $this->get('/en')->getContent());
        preg_match($link, $this->get('/en/blog')->getContent(), $match);
        $this->assertSame('aria-current="page"', $match[1] ?? null);
    }

    public function test_without_published_posts_the_home_page_has_no_blog_section(): void
    {
        Post::factory()->draft()->create(['title' => 'Secret draft']);
        Post::factory()->scheduled()->create(['title' => 'Coming soon']);

        $this->get('/en')
            ->assertDontSee('id="blog"', false)
            ->assertDontSee('Secret draft')
            ->assertDontSee('Coming soon');
    }

    public function test_home_lists_the_four_newest_posts_and_links_the_blog(): void
    {
        foreach (range(1, 5) as $i) {
            Post::factory()->create(['title' => "Post number {$i}", 'published_at' => now()->subDays(10 - $i)]);
        }

        $this->get('/en')
            ->assertSee('href="'.url('/en/blog').'"', false)
            ->assertSeeInOrder(['Post number 5', 'Post number 4', 'Post number 3', 'Post number 2'])
            ->assertDontSee('Post number 1')
            ->assertSee('All posts');
    }

    public function test_index_groups_posts_by_year_with_excerpts(): void
    {
        Post::factory()->create(['title' => 'Fermenting hot sauce', 'excerpt' => 'Three weeks in a jar.', 'published_at' => '2026-09-28 10:00']);
        Post::factory()->create(['title' => 'Helm tips', 'published_at' => '2025-11-03 10:00']);
        Post::factory()->draft()->create(['title' => 'Unfinished']);

        $this->get('/en/blog')
            ->assertOk()
            ->assertSeeInOrder(['2026', 'Sep 28', 'Fermenting hot sauce', 'Three weeks in a jar.', '2025', 'Nov 3', 'Helm tips'])
            ->assertDontSee('Unfinished');

        $this->get('/hu/blog')->assertSee('szept. 28.');
    }

    public function test_index_shows_a_message_when_empty(): void
    {
        $this->get('/nl/blog')->assertOk()->assertSee('Nog geen berichten.');
    }

    public function test_a_post_page_renders_markdown_and_marks_its_language(): void
    {
        Post::factory()->create([
            'slug' => 'kovaszos-kenyer',
            'title' => 'Kovászos kenyér',
            'locale' => 'hu',
            'body' => "## Hozzávalók\n\n- liszt\n- víz",
            'published_at' => '2026-10-01 09:00',
        ]);

        $this->get('/en/blog/kovaszos-kenyer')
            ->assertOk()
            ->assertSee('<article lang="hu">', false)
            ->assertSee('<h2>Hozzávalók</h2>', false)
            ->assertSee('Oct 1, 2026')
            ->assertSee('in Hungarian')
            ->assertSee('<link rel="canonical" href="'.url('/hu/blog/kovaszos-kenyer').'">', false)
            ->assertDontSee('hreflang="nl" href', false)
            ->assertSee('<meta property="og:type" content="article">', false);

        $this->get('/hu/blog/kovaszos-kenyer')->assertDontSee('magyar nyelven');
    }

    public function test_the_switcher_keeps_you_on_the_same_post(): void
    {
        Post::factory()->create(['slug' => 'helm-tips']);

        $this->get('/en/blog/helm-tips')
            ->assertSee('href="'.url('/nl/blog/helm-tips').'"', false)
            ->assertSee('href="'.url('/hu/blog/helm-tips').'"', false);
    }

    public function test_drafts_scheduled_and_unknown_posts_are_not_found(): void
    {
        Post::factory()->draft()->create(['slug' => 'draft']);
        Post::factory()->scheduled()->create(['slug' => 'later']);

        $this->get('/en/blog/draft')->assertNotFound();
        $this->get('/en/blog/later')->assertNotFound();
        $this->get('/en/blog/nope')->assertNotFound();
    }

    public function test_pager_links_older_and_newer_posts(): void
    {
        Post::factory()->create(['slug' => 'first', 'title' => 'First post', 'published_at' => now()->subDays(3)]);
        Post::factory()->create(['slug' => 'second', 'title' => 'Second post', 'published_at' => now()->subDays(2)]);
        Post::factory()->create(['slug' => 'third', 'title' => 'Third post', 'published_at' => now()->subDay()]);

        $this->get('/en/blog/second')
            ->assertSeeInOrder(['← ', 'First post', 'Third post', ' →'], false);
        $this->get('/en/blog/first')->assertDontSee('←', false);
    }
}
