<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Projects move from lang/{locale}/cv.php into their own table, so the
     * admin can add, remove and reorder them. Each translated field is a
     * JSON object keyed by locale: {"en": "...", "nl": "...", "hu": "..."}.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('position')->index();
            $table->json('title');
            $table->json('meta');
            $table->json('body');
            $table->timestamps();
        });

        $projects = $this->initialProjects();

        // Texts already edited in the admin (translations rows such as
        // "projects.1.body") are carried over, then removed.
        $edits = Schema::hasTable('translations')
            ? DB::table('translations')->where('group', 'cv')->where('key', 'like', 'projects.%')->get()
            : collect();

        foreach ($edits as $edit) {
            [, $index, $field] = array_pad(explode('.', $edit->key), 3, null);
            if (isset($projects[$index][$field])) {
                $projects[$index][$field][$edit->locale] = $edit->value;
            }
        }

        $now = now();
        foreach (array_values($projects) as $position => $project) {
            DB::table('projects')->insert([
                'position' => $position,
                'title' => json_encode($project['title'], JSON_UNESCAPED_UNICODE),
                'meta' => json_encode($project['meta'], JSON_UNESCAPED_UNICODE),
                'body' => json_encode($project['body'], JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if ($edits->isNotEmpty()) {
            DB::table('translations')->whereIn('id', $edits->pluck('id'))->delete();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }

    /** The projects as they were in the lang files on 2026-10-16. */
    private function initialProjects(): array
    {
        return [
            [
                'title' => [
                    'en' => 'Automated bug validation with Claude',
                    'nl' => 'Geautomatiseerde bugvalidatie met Claude',
                    'hu' => 'Automatikus hibavalidálás Claude-dal',
                ],
                'meta' => [
                    'en' => 'Optimy, 2026',
                    'nl' => 'Optimy, 2026',
                    'hu' => 'Optimy, 2026',
                ],
                'body' => [
                    'en' => 'Validating bug tickets was the support team\'s biggest time sink since I joined: reproduce the issue, read the code, check the logs, decide if it\'s real. I started by writing a Claude skill that could do this, then built the setup around it: Jira webhooks trigger a run, Claude has access to the repositories and logs, and the ticket comes back validated. It now runs without any human interaction.',
                    'nl' => 'Bugtickets valideren kostte het supportteam sinds mijn start de meeste tijd: het probleem reproduceren, de code lezen, de logs nakijken en beslissen of het echt een bug is. Ik begon met een Claude-skill die dit kon, en bouwde daarna de opzet eromheen: Jira-webhooks starten een run, Claude heeft toegang tot de repositories en logs, en het ticket komt gevalideerd terug. Het draait nu zonder menselijke tussenkomst.',
                    'hu' => 'A hibajegyek validálása a csatlakozásom óta a support csapat legnagyobb időrablója volt: reprodukálni a hibát, elolvasni a kódot, átnézni a logokat, eldönteni, hogy valós-e. Először írtam egy Claude skillt, ami ezt meg tudta csinálni, aztán felépítettem köré a rendszert: Jira webhookok indítanak egy futást, Claude hozzáfér a repókhoz és a logokhoz, a jegy pedig validálva jön vissza. Ma már emberi beavatkozás nélkül fut.',
                ],
            ],
            [
                'title' => [
                    'en' => 'Asset and contract management platform',
                    'nl' => 'Platform voor vastgoed- en contractbeheer',
                    'hu' => 'Eszköz- és szerződéskezelő platform',
                ],
                'meta' => [
                    'en' => 'freelance, 2023',
                    'nl' => 'freelance, 2023',
                    'hu' => 'szabadúszó, 2023',
                ],
                'body' => [
                    'en' => 'Contract follow-up, income and invoices for hundreds of properties, email communication with clients, an interactive map of parking spots at the depots, photos of utility meter readings, and dashboards. Laravel and Vue, built solo, in production since December 2023.',
                    'nl' => 'Opvolging van contracten, inkomsten en facturen voor honderden panden, e-mailcommunicatie met klanten, een interactieve kaart van de parkeerplaatsen bij de depots, foto\'s van meterstanden en dashboards. Laravel en Vue, alleen gebouwd, in productie sinds december 2023.',
                    'hu' => 'Szerződések követése, bevételek és számlák több száz ingatlanhoz, e-mailes kommunikáció az ügyfelekkel, a telephelyek parkolóhelyeinek interaktív térképe, közműóra-leolvasások fotói és dashboardok. Laravel és Vue, egyedül építettem, 2023 decembere óta éles.',
                ],
            ],
            [
                'title' => [
                    'en' => 'Worksheet manager for a car repair shop',
                    'nl' => 'Werkbonnensysteem voor een autogarage',
                    'hu' => 'Munkalapkezelő egy autószerviznek',
                ],
                'meta' => [
                    'en' => 'Creative Brand Solutions, 2022 – 23',
                    'nl' => 'Creative Brand Solutions, 2022 – 23',
                    'hu' => 'Creative Brand Solutions, 2022 – 23',
                ],
                'body' => [
                    'en' => 'A system for managing the shop\'s worksheets day to day. I led the project and delivered it before leaving the company. Still in daily use.',
                    'nl' => 'Een systeem om de werkbonnen van de garage dagelijks te beheren. Ik leidde het project en leverde het op voor ik het bedrijf verliet. Nog elke dag in gebruik.',
                    'hu' => 'Rendszer a szerviz munkalapjainak napi kezelésére. Én vezettem a projektet, és a cégtől való távozásom előtt átadtam. Ma is naponta használják.',
                ],
            ],
            [
                'title' => [
                    'en' => 'L2 process documentation and automation',
                    'nl' => 'Documentatie en automatisering van L2-processen',
                    'hu' => 'L2 folyamatok dokumentálása és automatizálása',
                ],
                'meta' => [
                    'en' => 'Optimy, 2024',
                    'nl' => 'Optimy, 2024',
                    'hu' => 'Optimy, 2024',
                ],
                'body' => [
                    'en' => 'Documented every manual support process, automated the repetitive ones, and moved several into the product. The team now spends much less time on routine work.',
                    'nl' => 'Elk handmatig supportproces gedocumenteerd, de repetitieve geautomatiseerd en een aantal in het product ingebouwd. Het team besteedt nu veel minder tijd aan routinewerk.',
                    'hu' => 'Minden kézi support folyamatot dokumentáltam, az ismétlődőket automatizáltam, többet pedig beépítettünk a termékbe. A csapat most sokkal kevesebb időt tölt rutinmunkával.',
                ],
            ],
        ];
    }
};
