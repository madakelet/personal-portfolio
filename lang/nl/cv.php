<?php

/*
| Dutch translation of lang/en/cv.php. Same tone: plain first-person
| sentences, concrete outcomes, no em-dashes.
*/

return [
    'bio' => [
        'Ik ben softwareontwikkelaar en DevOps-engineer uit Hongarije en woon in België. Ik werk bij <a href="https://www.optimy.com" target="_blank" rel="noopener">Optimy</a> in Brussel, een platform voor maatschappelijke impact dat door meer dan 330 organisaties in 25 landen wordt gebruikt. Ik begon er in 2024 in een IT Ops / L2-supportrol, en sinds oktober 2026 stap ik over naar het engineeringteam als L3 / DevOps.',
        'Daarvoor heb ik vier jaar in Hongarije systemen gebouwd met Laravel en Vue: webshops en landingspagina\'s bij een bureau, een werkbonnensysteem voor een autogarage en, als freelancer, een platform voor vastgoed- en contractbeheer voor een holding met honderden panden. Ze zijn allemaal nog steeds in gebruik. Doordat ik de websites van mijn freelanceklanten op mijn eigen server hostte, raakte ik geïnteresseerd in infrastructuur.',
        'Aan de codekant werk ik met PHP 8, Laravel en Vue, aan de infrastructuurkant met Kubernetes, Helm, Grafana, Loki en AWS (RDS, SQS, SES, S3, IAM). Ik voel me thuis in codebases die ik niet zelf heb geschreven, omdat ik veel bugtickets heb gevalideerd. Ik documenteer en automatiseer een proces liever dan het elke week met de hand te doen. Mijn meest recente project op het werk is een pipeline voor bugvalidatie waarin Claude de validatie doet, gestart door Jira-webhooks, met toegang tot onze repositories en logs.',
        'Buiten het werk bouw ik graag dingen voor mensen om me heen. Onlangs maakte ik een portfoliowebsite voor mijn vriendin, die fotografe is, en ik beheer een NFL pick\'em-site die mijn vrienden en ik tijdens het seizoen elke week gebruiken.',
    ],

    'contact' => 'Mijn code staat op <a href=":github" target="_blank" rel="noopener">GitHub</a>, ik zit op <a href=":linkedin" target="_blank" rel="noopener">LinkedIn</a> en je kunt me mailen op <a href="mailto::email">:email</a>.',
    'cv_pdf' => 'Er is ook een <a href=":url">pdf-versie van mijn cv</a>.',

    'experience' => [
        [
            'title' => 'IT Ops / L2-support, daarna L3 / DevOps, Optimy (Brussel)',
            'from' => '2024',
            'to' => null,
            'body' => 'Optimy draait op AWS en Kubernetes en bedient honderden klantorganisaties. Toen ik begon, had het supportteam veel handmatig werk en heel weinig documentatie. Ik heb eerst elk proces gedocumenteerd, daarna de repetitieve taken geautomatiseerd en later een aantal ervan in het product zelf ingebouwd. Ik heb ook alle maatwerkontwikkelingen voor klanten gemigreerd naar een gestandaardiseerd PHP 8-framework en het beheer van de SSL-certificaten en eigen verzenddomeinen voor e-mail van klanten overgenomen. Daarnaast neem ik deel aan disaster-recovery-oefeningen, doe ik code reviews en geef ik het productteam feedback over de impact van nieuwe functies. De laatste tijd werk ik vooral met het DevOps-team aan de stack met Kubernetes, Helm en Grafana/Loki, en aan de geautomatiseerde pipeline voor bugvalidatie die bij de projecten staat. Sinds oktober 2026 maak ik de overstap naar engineering als L3 / DevOps.',
        ],
        [
            'title' => 'Freelance ontwikkelaar',
            'from' => '2023-03',
            'to' => '2023-12',
            'body' => 'Eén groot project: een platform voor vastgoed- en contractbeheer voor een Hongaarse holding met honderden depots, winkels en appartementen. Ik heb het alleen ontworpen en gebouwd in Laravel en Vue en in december 2023 opgeleverd. Ze gebruiken het nog elke dag. Ik hostte ook de websites van mijn klanten op mijn eigen fysieke server, die ik samen met een bevriende DevOps-engineer had opgezet. Daar begon mijn interesse in de infrastructuurkant.',
        ],
        [
            'title' => 'Junior, daarna full-stack ontwikkelaar, Creative Brand Solutions Kft.',
            'from' => '2020-02',
            'to' => '2023-02',
            'body' => 'Ik begon met kleine WordPress-projecten en groeide door naar full-stack werk in Laravel en Vue aan webshops, landingspagina\'s en de systemen erachter. Mijn laatste grote project daar was een werkbonnensysteem voor een autogarage. Ik leidde het project en rondde het af voor mijn vertrek, en de garage werkt er nog steeds mee.',
        ],
    ],

    'education' => [
        [
            'title' => 'Informatica, Eötvös Loránd-universiteit (ELTE), Boedapest',
            'from' => '2017',
            'to' => '2020',
            'body' => 'Drie jaar gestudeerd. Ik vertrok voor een voltijdse baan als ontwikkelaar en heb het diploma niet afgemaakt.',
        ],
    ],

    'projects' => [
        [
            'title' => 'Geautomatiseerde bugvalidatie met Claude',
            'meta' => 'Optimy, 2026',
            'body' => 'Bugtickets valideren kostte het supportteam sinds mijn start de meeste tijd: het probleem reproduceren, de code lezen, de logs nakijken en beslissen of het echt een bug is. Ik begon met een Claude-skill die dit kon, en bouwde daarna de opzet eromheen: Jira-webhooks starten een run, Claude heeft toegang tot de repositories en logs, en het ticket komt gevalideerd terug. Het draait nu zonder menselijke tussenkomst.',
        ],
        [
            'title' => 'Platform voor vastgoed- en contractbeheer',
            'meta' => 'freelance, 2023',
            'body' => 'Opvolging van contracten, inkomsten en facturen voor honderden panden, e-mailcommunicatie met klanten, een interactieve kaart van de parkeerplaatsen bij de depots, foto\'s van meterstanden en dashboards. Laravel en Vue, alleen gebouwd, in productie sinds december 2023.',
        ],
        [
            'title' => 'Werkbonnensysteem voor een autogarage',
            'meta' => 'Creative Brand Solutions, 2022 – 23',
            'body' => 'Een systeem om de werkbonnen van de garage dagelijks te beheren. Ik leidde het project en leverde het op voor ik het bedrijf verliet. Nog elke dag in gebruik.',
        ],
        [
            'title' => 'Documentatie en automatisering van L2-processen',
            'meta' => 'Optimy, 2024',
            'body' => 'Elk handmatig supportproces gedocumenteerd, de repetitieve geautomatiseerd en een aantal in het product ingebouwd. Het team besteedt nu veel minder tijd aan routinewerk.',
        ],
    ],

    'side_projects' => [
        'nfl' => [
            'title' => 'NFL pick\'em',
            'meta' => '2023 – heden',
            'body' => 'Een site op uitnodiging waar mijn vrienden en ik elke week de winnaar van elke NFL-wedstrijd voorspellen en over het hele seizoen strijden in een klassement. Wekelijkse formulieren, uitslagen, een scorebord en beheertools om het seizoen op te zetten. Laravel, Vue en Tailwind. Gestart in december 2023 en elk seizoen komen er nieuwe functies bij.',
        ],
        'photography' => [
            'title' => 'Fotografieportfolio',
            'meta' => '2026',
            'body' => 'Een website voor het fotowerk van mijn vriendin: galerijen, een over-mijpagina en een manier voor klanten om contact op te nemen.',
        ],
    ],
];
