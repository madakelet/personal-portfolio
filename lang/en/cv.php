<?php

/*
| Landing page copy. Approved draft 3: plain first-person sentences,
| concrete outcomes, no metaphors, no em-dashes. Inline HTML is allowed.
| Dates are "YYYY" or "YYYY-MM"; a null end date means "present".
*/

return [
    'bio' => [
        'I\'m a software developer and DevOps engineer from Hungary, living in Belgium. I work at <a href="https://www.optimy.com" target="_blank" rel="noopener">Optimy</a> in Brussels, a social impact platform used by 330+ organisations in 25 countries. I joined in 2024 in an IT Ops / L2 support role, and since October 2026 I\'m moving into the engineering team as L3 / DevOps.',
        'Before that I spent four years in Hungary building Laravel and Vue systems: webshops and landing pages at an agency, a worksheet manager for a car repair shop, and, as a freelancer, an asset and contract management platform for a holding with hundreds of properties. All of them are still in use today. Hosting my freelance clients\' sites on my own server is what got me interested in infrastructure in the first place.',
        'I work with PHP 8, Laravel and Vue on the code side, and Kubernetes, Helm, Grafana, Loki and AWS (RDS, SQS, SES, S3, IAM) on the infrastructure side. I\'m comfortable in codebases I didn\'t write, which comes from validating a lot of bug tickets, and I prefer documenting and automating a process over doing it by hand every week. My most recent project at work is a bug-validation pipeline where Claude does the validation, triggered by Jira webhooks, with access to our repositories and logs.',
        'Outside work I like building things for people around me. I recently made a portfolio website for my girlfriend, who is a photographer, and I run an NFL pick\'em site that my friends and I use every week during the season.',
    ],

    // Placeholders filled from config/site.php.
    'contact' => 'My code is on <a href=":github" target="_blank" rel="noopener">GitHub</a>, I\'m on <a href=":linkedin" target="_blank" rel="noopener">LinkedIn</a>, and you can email me at <a href="mailto::email">:email</a>.',
    'cv_pdf' => 'There\'s also a <a href=":url">PDF version of my CV</a>.',

    'experience' => [
        [
            'title' => 'IT Ops / L2 Support, then L3 / DevOps, Optimy (Brussels)',
            'from' => '2024',
            'to' => null,
            'body' => 'Optimy runs on AWS and Kubernetes and serves hundreds of client organisations. When I joined, the support team had a lot of manual tasks and very little documentation. I documented every process first, then automated the repetitive ones, and later moved several of them into the product itself. I also migrated all custom client developments onto a standardised PHP 8 framework, took over client SSL certificates and custom email sending domains, take part in disaster recovery exercises, do code reviews, and give the product team feedback on the impact of new features. Lately I work mostly with the DevOps team on the Kubernetes, Helm and Grafana/Loki stack. I also built a pipeline where Claude validates bug tickets on its own, triggered by Jira webhooks, with access to our repositories and logs. Since October 2026 I\'m transitioning into engineering as L3 / DevOps.',
        ],
        [
            'title' => 'Freelance Developer',
            'from' => '2023-03',
            'to' => '2023-12',
            'body' => 'One major project: an asset and contract management platform for a Hungarian holding with hundreds of depots, shops and flats. I designed and built it on my own in Laravel and Vue and shipped it in December 2023. They still use it daily. I also hosted my clients\' websites on my own physical server, set up together with a DevOps engineer friend. That is where my interest in the infrastructure side started.',
        ],
        [
            'title' => 'Junior, then Full-stack Developer, Creative Brand Solutions Kft.',
            'from' => '2020-02',
            'to' => '2023-02',
            'body' => 'Started with small WordPress projects and grew into full-stack Laravel and Vue work on webshops, landing pages and the systems behind them. My last big project there was a worksheet management system for a car repair shop. I led it and finished it before leaving, and the shop still runs on it.',
        ],
    ],

    'education' => [
        [
            'title' => 'Computer Science, Eötvös Loránd University (ELTE), Budapest',
            'from' => '2017',
            'to' => '2020',
            'body' => 'Three years of study. I left for a full-time developer job and didn\'t finish the degree.',
        ],
    ],

    // Keys match config('site.side_project_urls') for optional links.
    'side_projects' => [
        'nfl' => [
            'title' => 'NFL pick\'em',
            'meta' => '2023 – present',
            'body' => 'An invite-only site where my friends and I pick the winners of every NFL game each week and compete on a leaderboard over the season. Weekly pick forms, results, a scoreboard and admin tools for setting up the season. Laravel, Vue and Tailwind. Started in December 2023 and still getting new features every season.',
        ],
        'photography' => [
            'title' => 'Photography portfolio',
            'meta' => '2026',
            'body' => 'A website for my girlfriend\'s photography work: galleries, an about page and a way for clients to get in touch.',
        ],
    ],
];
