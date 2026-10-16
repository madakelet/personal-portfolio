<?php

/*
| Owner details shown across the site. Values marked TODO are still
| placeholders from the prototype.
*/

return [

    'name' => 'Your Name', // TODO

    'email' => 'hello@yourdomain.dev', // TODO

    'github' => 'https://github.com', // TODO

    'linkedin' => 'https://linkedin.com', // TODO

    // Path under public/. The CV link is hidden until this file exists.
    'cv_pdf' => 'cv.pdf',

    // Optional links for the side projects; the title is plain text while null.
    'side_project_urls' => [
        'nfl' => null, // TODO
        'photography' => null, // TODO
    ],

    // Supported locales, in switcher order. The first one is the fallback.
    'locales' => [
        'en' => 'English',
        'nl' => 'Nederlands',
        'hu' => 'Magyar',
    ],

];
