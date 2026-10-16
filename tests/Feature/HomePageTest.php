<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_shows_every_cv_section(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSeeInOrder(['Experience', 'Education', 'Projects', 'Side projects'])
            ->assertSee('Optimy (Brussels)')
            ->assertSee('2024 – present')
            ->assertSee('Mar 2023 – Dec 2023');
    }

    public function test_contact_links_come_from_config(): void
    {
        config(['site.email' => 'me@example.test', 'site.github' => 'https://github.com/example']);

        $this->get('/en')
            ->assertSee('href="mailto:me@example.test"', false)
            ->assertSee('href="https://github.com/example"', false);
    }

    public function test_name_is_translated_per_locale(): void
    {
        app('translator')->addLines(['ui.name' => 'Test Name'], 'en');
        app('translator')->addLines(['ui.name' => 'Név Teszt'], 'hu');

        $this->get('/en')->assertSee('<h1>Test Name</h1>', false)->assertSee('class="name" href="'.url('/en').'">Test Name</a>', false);
        $this->get('/hu')->assertSee('<h1>Név Teszt</h1>', false)->assertSee('© '.date('Y').' Név Teszt', false);
    }

    public function test_footer_hides_profile_links_that_are_not_set(): void
    {
        config(['site.github' => null, 'site.linkedin' => 'https://www.linkedin.com/in/example']);

        $this->get('/en')
            ->assertDontSee('rel="noopener" target="_blank">GitHub</a>', false)
            ->assertSee('<a href="https://www.linkedin.com/in/example" rel="noopener" target="_blank">LinkedIn</a>', false);
    }

    public function test_cv_link_is_hidden_until_the_pdf_exists(): void
    {
        config(['site.cv_pdf' => 'test-cv.pdf']);
        $path = public_path('test-cv.pdf');

        $this->get('/en')->assertDontSee('test-cv.pdf');

        File::put($path, '%PDF-1.4');
        try {
            $this->get('/en')
                ->assertSee('CV (PDF)')
                ->assertSee('PDF version of my CV');
        } finally {
            File::delete($path);
        }
    }

    public function test_side_project_title_links_only_when_a_url_is_set(): void
    {
        config(['site.side_project_urls.nfl' => 'https://picks.example.test']);

        $this->get('/en')
            ->assertSee('href="https://picks.example.test"', false)
            ->assertSee('<strong>Photography portfolio</strong>', false);
    }

    public function test_dutch_page_is_translated(): void
    {
        $this->get('/nl')
            ->assertOk()
            ->assertSeeInOrder(['Ervaring', 'Opleiding', 'Projecten', 'Nevenprojecten'])
            ->assertSee('2024 – heden')
            ->assertSee('mrt. 2023 – dec. 2023')
            ->assertSee('Over mij');
    }

    public function test_hungarian_page_is_translated(): void
    {
        $this->get('/hu')
            ->assertOk()
            ->assertSeeInOrder(['Tapasztalat', 'Tanulmányok', 'Projektek', 'Hobbiprojektek'])
            ->assertSee('2024 – jelenleg')
            ->assertSee('2023. márc. – 2023. dec.')
            ->assertSee('Rólam');
    }
}
