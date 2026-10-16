<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_lists_published_posts_with_full_content(): void
    {
        Post::factory()->create(['slug' => 'kimcsi', 'title' => 'Kimcsi & só', 'locale' => 'hu', 'body' => 'Some **bold** text.']);
        Post::factory()->draft()->create(['title' => 'Draft post']);
        Post::factory()->scheduled()->create(['title' => 'Later post']);

        $response = $this->get('/feed.xml')->assertOk();
        $this->assertStringStartsWith('application/rss+xml', $response->headers->get('Content-Type'));

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'Feed must be valid XML');
        $this->assertCount(1, $xml->channel->item);
        $this->assertSame('Kimcsi & só', (string) $xml->channel->item[0]->title);
        $this->assertSame(url('/hu/blog/kimcsi'), (string) $xml->channel->item[0]->link);
        $this->assertStringContainsString('<strong>bold</strong>', (string) $xml->channel->item[0]->children('content', true)->encoded);
    }

    public function test_feed_is_valid_when_empty(): void
    {
        $xml = simplexml_load_string($this->get('/feed.xml')->assertOk()->getContent());

        $this->assertNotFalse($xml);
        $this->assertCount(0, $xml->channel->item);
    }

    public function test_pages_link_the_feed_once_something_is_published(): void
    {
        $this->get('/en')->assertDontSee(url('/feed.xml'));

        Post::factory()->create();

        $this->get('/en')
            ->assertSee('<link rel="alternate" type="application/rss+xml"', false)
            ->assertSee('href="'.url('/feed.xml').'">RSS</a>', false);
    }
}
