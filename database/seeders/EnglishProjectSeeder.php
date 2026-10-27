<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * The English title, meta and body of each project, matched by position.
 * Dutch and Hungarian values already in the table are kept.
 */
class EnglishProjectSeeder extends Seeder
{
    /** @var list<array{position: int, url: string|null, title: string, meta: string, body: string}> */
    public const PROJECTS = [
        [
            'position' => 1,
            'url' => null,
            'title' => 'NFL pick\'em',
            'meta' => '2023 – present',
            'body' => 'An invite-only site where my friends and I pick the winners of every NFL game each week and compete on a leaderboard over the season. Weekly pick forms, results, a scoreboard and admin tools for setting up the season. Laravel, Vue and Tailwind. Started in December 2023 and still getting new features every season.',
        ],
        [
            'position' => 2,
            'url' => null,
            'title' => 'Photography portfolio',
            'meta' => '2026',
            'body' => 'A website for my girlfriend\'s photography work: galleries, an about page and a way for clients to get in touch.',
        ],
        [
            'position' => 3,
            'url' => null,
            'title' => 'This website',
            'meta' => '2026',
            'body' => 'My CV in English, Dutch and Hungarian, with an admin where I edit every text and project myself. Laravel, Blade and Tailwind for the public pages, Vue for the admin.',
        ],
    ];

    public function run(): void
    {
        foreach (self::PROJECTS as $data) {
            $project = Project::firstOrNew(['position' => $data['position']]);

            $project->url = $data['url'];
            foreach (Project::FIELDS as $field) {
                $project->{$field} = [...($project->{$field} ?? []), 'en' => $data[$field]];
            }

            $project->save();
        }
    }
}
