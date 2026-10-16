<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** English titles of the work projects that moved into the Experience texts. */
    private const WORK_PROJECTS = [
        'Automated bug validation with Claude',
        'Asset and contract management platform',
        'Worksheet manager for a car repair shop',
        'L2 process documentation and automation',
    ];

    /**
     * Projects become personal projects only: work projects are already
     * covered under Experience. The former "side projects" (lang files)
     * move into the table, plus this site, and projects get a link.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('url')->nullable()->after('position');
        });

        DB::table('projects')->get()
            ->filter(fn ($row) => in_array(json_decode($row->title, true)['en'] ?? null, self::WORK_PROJECTS, true))
            ->each(fn ($row) => DB::table('projects')->where('id', $row->id)->delete());

        $position = (int) DB::table('projects')->max('position') + 1;
        $now = now();

        foreach ($this->personalProjects() as $project) {
            DB::table('projects')->insert([
                'position' => $position++,
                'url' => null,
                'title' => json_encode($project['title'], JSON_UNESCAPED_UNICODE),
                'meta' => json_encode($project['meta'], JSON_UNESCAPED_UNICODE),
                'body' => json_encode($project['body'], JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Admin edits to the old side_projects texts no longer point anywhere.
        if (Schema::hasTable('translations')) {
            DB::table('translations')->where('group', 'cv')->where('key', 'like', 'side_projects.%')->delete();
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }

    private function personalProjects(): array
    {
        return [
            [
                'title' => [
                    'en' => 'NFL pick\'em',
                    'nl' => 'NFL pick\'em',
                    'hu' => 'NFL tippjáték',
                ],
                'meta' => [
                    'en' => '2023 – present',
                    'nl' => '2023 – heden',
                    'hu' => '2023 – jelenleg',
                ],
                'body' => [
                    'en' => 'An invite-only site where my friends and I pick the winners of every NFL game each week and compete on a leaderboard over the season. Weekly pick forms, results, a scoreboard and admin tools for setting up the season. Laravel, Vue and Tailwind. Started in December 2023 and still getting new features every season.',
                    'nl' => 'Een site op uitnodiging waar mijn vrienden en ik elke week de winnaar van elke NFL-wedstrijd voorspellen en over het hele seizoen strijden in een klassement. Wekelijkse formulieren, uitslagen, een scorebord en beheertools om het seizoen op te zetten. Laravel, Vue en Tailwind. Gestart in december 2023 en elk seizoen komen er nieuwe functies bij.',
                    'hu' => 'Meghívásos oldal, ahol a barátaimmal minden héten megtippeljük az összes NFL-meccs győztesét, és a szezon során ranglistán versenyzünk. Heti tippűrlapok, eredmények, pontozótábla és adminfelület a szezon beállításához. Laravel, Vue és Tailwind. 2023 decemberében indult, és minden szezonban kap új funkciókat.',
                ],
            ],
            [
                'title' => [
                    'en' => 'Photography portfolio',
                    'nl' => 'Fotografieportfolio',
                    'hu' => 'Fotós portfólió',
                ],
                'meta' => [
                    'en' => '2026',
                    'nl' => '2026',
                    'hu' => '2026',
                ],
                'body' => [
                    'en' => 'A website for my girlfriend\'s photography work: galleries, an about page and a way for clients to get in touch.',
                    'nl' => 'Een website voor het fotowerk van mijn vriendin: galerijen, een over-mijpagina en een manier voor klanten om contact op te nemen.',
                    'hu' => 'Weboldal a barátnőm fotós munkáihoz: galériák, bemutatkozó oldal, és egy lehetőség, hogy az ügyfelek felvegyék vele a kapcsolatot.',
                ],
            ],
            [
                'title' => [
                    'en' => 'This website',
                    'nl' => 'Deze website',
                    'hu' => 'Ez a weboldal',
                ],
                'meta' => [
                    'en' => '2026',
                    'nl' => '2026',
                    'hu' => '2026',
                ],
                'body' => [
                    'en' => 'My CV in English, Dutch and Hungarian, with an admin where I edit every text and project myself. Laravel, Blade and Tailwind for the public pages, Vue for the admin.',
                    'nl' => 'Mijn cv in het Engels, Nederlands en Hongaars, met een beheeromgeving waarin ik zelf elke tekst en elk project aanpas. Laravel, Blade en Tailwind voor de publieke pagina\'s, Vue voor het beheer.',
                    'hu' => 'Az önéletrajzom angolul, hollandul és magyarul, adminfelülettel, ahol minden szöveget és projektet magam szerkesztek. Laravel, Blade és Tailwind a nyilvános oldalakhoz, Vue az adminhoz.',
                ],
            ],
        ];
    }
};
