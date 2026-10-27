<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * The English texts edited in the admin, as overrides of lang/en.
 * Running it again updates the rows in place.
 */
class EnglishTextSeeder extends Seeder
{
    /** @var array<string, array<string, string>> group => [key => value] */
    public const TEXTS = [
        'cv' => [
            'bio.0' => 'I\'m a software developer and DevOps engineer from Hungary, living in Belgium. I work at <a href="https://www.optimy.com" target="_blank" rel="noopener">Optimy</a> in Brussels, a social impact platform used by 330+ organisations in 25 countries. I joined in 2024 in an IT Ops / L2 support role and in October 2026 I moved into the engineering team as L3 / DevOps.',
            'bio.1' => 'Before that I spent four years in Hungary building Laravel and Vue applications: landing pages, webshops and custom back-office systems for businesses (first at n agency, later as a freelancer). Hosting my freelance clients\' sites on my own server is what got me interested in infrastructure in the first place.',
            'bio.2' => 'I started out writing PHP with Laravel and Vue, but over the last few years I have moved closer to where the code runs. This is how I got to know Kubernetes, Helm, Grafana and Loki well. I have also worked a lot with AWS (RDS, SQS, SES, S3..). I gained a lot of experience in reading code I did not write. For the last 3 years I spent much time working out why other people\'s code breaks.',
            'contact' => 'I have a <a href=":github" target="_blank" rel="noopener">profile on GitHub</a>, but most of my repos are private. You can reach out to me on <a href=":linkedin" target="_blank" rel="noopener">LinkedIn</a> or email me at <a href="mailto::email">:email</a>.',
            'experience.0.body' => 'When I joined, the support team had a lot of manual tasks and very little documentation. I documented every process first, then automated the repetitive ones. Later, with the help from colleagues, we moved several of them into the product itself. I also migrated all custom client developments onto a standardized PHP 8 framework (it almost took a whole year, alongside my other work). I take part in disaster recovery exercises, do code reviews, and give the product team feedback on the impact of new features. Lately I have been working with the DevOps team on the Kubernetes, Helm and Grafana/Loki stack. One of my biggest achievements this year was building an AI pipeline that now handles bug validation fully automatically.',
            'experience.1.from' => '2023-01',
            'experience.2.title' => 'Full-stack Developer, Creative Brand Solutions',
        ],
        'ui' => [
            'blog.intro' => 'Notes on things I build, challenges I face, experiments with new cooking recipes and fermentation or just anything that I feel like writing about c:',
        ],
    ];

    public function run(): void
    {
        foreach (self::TEXTS as $group => $texts) {
            foreach ($texts as $key => $value) {
                Translation::updateOrCreate(
                    ['locale' => 'en', 'group' => $group, 'key' => $key],
                    ['value' => $value],
                );
            }

            // Model events may be muted while seeding, so clear the cache here too.
            Cache::forget(Translation::cacheKey('en', $group));
        }
    }
}
