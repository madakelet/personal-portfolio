<?php

return [
    // Page heading, footer, browser title.
    'name' => 'Adam Telek',
    // Top bar, after the ~/ prefix.
    'header_name' => 'madakelet',

    'meta' => [
        'tagline' => 'Softwareontwikkelaar en DevOps-engineer',
        'description' => 'Softwareontwikkelaar en DevOps-engineer uit Hongarije, woonachtig in België. Laravel, Vue, Kubernetes en AWS.',
        'og_locale' => 'nl_BE',
    ],
    'nav' => [
        'label' => 'Hoofdmenu',
        'about' => 'Over mij',
        'blog' => 'Blog',
        'cv' => 'CV (pdf)',
        'theme' => 'Kleurthema wisselen',
        'language' => 'Taal',
    ],
    'sections' => [
        'experience' => 'Ervaring',
        'education' => 'Opleiding',
        'projects' => 'Projecten',
        'blog' => 'Blog',
    ],
    'dates' => [
        'month' => 'MMM YYYY',
        // Carbon isoFormat patterns for post dates, with and without the year.
        'day' => 'D MMM YYYY',
        'day_short' => 'D MMM',
        'present' => 'heden',
    ],
    'blog' => [
        'intro' => 'Notities over dingen die ik bouw, meningen over software en af en toe een fermentatie-experiment.',
        'all_posts' => 'Alle berichten',
        'empty' => 'Nog geen berichten.',
        // Shown after the date when a post is in another language than the page.
        'written_in' => 'in het :language',
        'rss' => 'RSS',
    ],
    'languages' => [
        'en' => 'Engels',
        'nl' => 'Nederlands',
        'hu' => 'Hongaars',
    ],
];
