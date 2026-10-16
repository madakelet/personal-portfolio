<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_english_by_default(): void
    {
        $this->get('/')->assertRedirect('/en');
    }

    public function test_root_follows_accept_language(): void
    {
        $this->get('/', ['Accept-Language' => 'nl-BE,nl;q=0.9,en;q=0.8'])->assertRedirect('/nl');
        $this->get('/', ['Accept-Language' => 'hu-HU,hu;q=0.9'])->assertRedirect('/hu');
    }

    public function test_root_falls_back_to_english_for_unsupported_languages(): void
    {
        $this->get('/', ['Accept-Language' => 'de-DE,de;q=0.9'])->assertRedirect('/en');
    }

    public function test_cookie_beats_accept_language(): void
    {
        $this->withUnencryptedCookie('locale', 'hu')
            ->get('/', ['Accept-Language' => 'nl'])
            ->assertRedirect('/hu');
    }

    public function test_invalid_cookie_is_ignored(): void
    {
        $this->withUnencryptedCookie('locale', 'xx')->get('/')->assertRedirect('/en');
    }

    public function test_each_locale_renders_with_matching_html_lang(): void
    {
        foreach (['en', 'nl', 'hu'] as $locale) {
            $this->get("/{$locale}")
                ->assertOk()
                ->assertSee("<html lang=\"{$locale}\">", false)
                ->assertCookie('locale', $locale, encrypted: false);
        }
    }

    public function test_unsupported_locale_prefix_is_not_found(): void
    {
        $this->get('/de')->assertNotFound();
    }

    public function test_switcher_links_to_the_same_page_in_every_locale(): void
    {
        $this->get('/nl')
            ->assertOk()
            ->assertSee('href="'.url('/en').'"', false)
            ->assertSee('href="'.url('/hu').'"', false)
            ->assertSee('aria-current="true">nl</a>', false);
    }

    public function test_head_lists_every_language_version(): void
    {
        $this->get('/hu')
            ->assertSee('<link rel="canonical" href="'.url('/hu').'">', false)
            ->assertSee('<link rel="alternate" hreflang="en" href="'.url('/en').'">', false)
            ->assertSee('<link rel="alternate" hreflang="nl" href="'.url('/nl').'">', false)
            ->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/').'">', false)
            ->assertSee('<meta property="og:locale" content="hu_HU">', false);
    }
}
