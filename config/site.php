<?php

/*
| Owner details shown across the site. Values marked TODO are still
| placeholders from the prototype. The name is translated, see
| lang/{locale}/ui.php.
*/

return [

    'email' => 'telekadam@telekadam.com', // TODO

    // Profile links come from .env. The footer hides an empty one, but the
    // contact sentence on the home page expects both to be set.
    'github' => env('SITE_GITHUB_URL'),

    'linkedin' => env('SITE_LINKEDIN_URL'),

    // Path under public/. The CV link is hidden until this file exists.
    'cv_pdf' => 'cv.pdf',

    // Supported locales, in switcher order. The first one is the fallback.
    'locales' => [
        'en' => 'English',
        'nl' => 'Nederlands',
        'hu' => 'Magyar',
    ],

];
