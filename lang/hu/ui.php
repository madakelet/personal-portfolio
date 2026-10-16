<?php

return [
    // Page heading, footer, browser title.
    'name' => 'Telek Ádám',
    // Top bar, after the ~/ prefix.
    'header_name' => 'madakelet',

    'meta' => [
        'tagline' => 'Szoftverfejlesztő és DevOps mérnök',
        'description' => 'Magyar szoftverfejlesztő és DevOps mérnök Belgiumban. Laravel, Vue, Kubernetes és AWS.',
        'og_locale' => 'hu_HU',
    ],
    'nav' => [
        'label' => 'Főmenü',
        'about' => 'Rólam',
        'blog' => 'Blog',
        'cv' => 'Önéletrajz (PDF)',
        'theme' => 'Színtéma váltása',
        'language' => 'Nyelv',
    ],
    'sections' => [
        'experience' => 'Tapasztalat',
        'education' => 'Tanulmányok',
        'projects' => 'Projektek',
        'blog' => 'Blog',
    ],
    'dates' => [
        'month' => 'YYYY. MMM',
        // Carbon isoFormat patterns for post dates, with and without the year.
        'day' => 'YYYY. MMM D.',
        'day_short' => 'MMM D.',
        'present' => 'jelenleg',
    ],
    'blog' => [
        'intro' => 'Jegyzetek arról, amit építek, véleményem szoftverekről, és néha egy-egy fermentálási kísérlet.',
        'all_posts' => 'Összes bejegyzés',
        'empty' => 'Még nincs bejegyzés.',
        // Shown after the date when a post is in another language than the page.
        'written_in' => ':language nyelven',
        'rss' => 'RSS',
    ],
    'languages' => [
        'en' => 'angol',
        'nl' => 'holland',
        'hu' => 'magyar',
    ],
];
