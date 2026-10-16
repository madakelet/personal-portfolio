<?php

return [
    // Page heading, footer, browser title.
    'name' => 'Adam Telek',
    // Top bar, after the ~/ prefix.
    'header_name' => 'madakelet',

    'meta' => [
        'tagline' => 'Software developer and DevOps engineer',
        'description' => 'Software developer and DevOps engineer from Hungary, living in Belgium. Laravel, Vue, Kubernetes and AWS.',
        'og_locale' => 'en_GB',
    ],
    'nav' => [
        'label' => 'Main',
        'about' => 'About',
        'blog' => 'Blog',
        'cv' => 'CV (PDF)',
        'theme' => 'Switch colour theme',
        'language' => 'Language',
    ],
    'sections' => [
        'experience' => 'Experience',
        'education' => 'Education',
        'projects' => 'Projects',
        'blog' => 'Blog',
    ],
    'dates' => [
        // Carbon isoFormat pattern for "YYYY-MM" dates.
        'month' => 'MMM YYYY',
        // Carbon isoFormat patterns for post dates, with and without the year.
        'day' => 'MMM D, YYYY',
        'day_short' => 'MMM D',
        'present' => 'present',
    ],
    'blog' => [
        'intro' => 'Notes on things I build, opinions on software, and the occasional fermentation experiment.',
        'all_posts' => 'All posts',
        'empty' => 'No posts yet.',
        // Shown after the date when a post is in another language than the page.
        'written_in' => 'in :language',
        'rss' => 'RSS',
    ],
    'languages' => [
        'en' => 'English',
        'nl' => 'Dutch',
        'hu' => 'Hungarian',
    ],
];
