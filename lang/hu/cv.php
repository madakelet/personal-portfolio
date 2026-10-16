<?php

/*
| Hungarian translation of lang/en/cv.php. Same tone: plain first-person
| sentences, concrete outcomes, no em-dashes.
*/

return [
    'bio' => [
        'Magyar szoftverfejlesztő és DevOps mérnök vagyok, Belgiumban élek. Brüsszelben dolgozom az <a href="https://www.optimy.com" target="_blank" rel="noopener">Optimy</a> csapatában. Az Optimy egy társadalmi hatásra fókuszáló platform, amelyet 25 országban több mint 330 szervezet használ. 2024-ben IT Ops / L2 support szerepkörben kezdtem, 2026 októbere óta pedig L3 / DevOps szerepben a fejlesztőcsapatba kerülök át.',
        'Előtte négy évig Magyarországon építettem Laravel és Vue rendszereket: webshopokat és landing oldalakat egy ügynökségnél, munkalapkezelő rendszert egy autószerviznek, szabadúszóként pedig eszköz- és szerződéskezelő platformot egy több száz ingatlannal rendelkező holdingnak. Mindegyik a mai napig használatban van. Az infrastruktúra akkor kezdett érdekelni, amikor a szabadúszó ügyfeleim oldalait a saját szerveremen hosztoltam.',
        'Kódoldalon PHP 8-cal, Laravellel és Vue-val dolgozom, infrastruktúraoldalon Kubernetesszel, Helmmel, Grafanával, Lokival és AWS-sel (RDS, SQS, SES, S3, IAM). Jól elboldogulok olyan kódbázisokban is, amelyeket nem én írtam, mert rengeteg hibajegyet validáltam. Egy folyamatot inkább dokumentálok és automatizálok, mint hogy minden héten kézzel csináljam. A legutóbbi munkahelyi projektem egy hibavalidáló pipeline, amelyben Claude végzi a validálást. Jira webhookok indítják, és hozzáfér a repóinkhoz és a logjainkhoz.',
        'Munkán kívül szívesen építek dolgokat a körülöttem lévő embereknek. Nemrég portfólióoldalt készítettem a barátnőmnek, aki fotós, és üzemeltetek egy NFL tippjáték oldalt, amit a barátaimmal a szezonban minden héten használunk.',
    ],

    'contact' => 'A kódjaim a <a href=":github" target="_blank" rel="noopener">GitHubon</a> vannak, megtalálsz a <a href=":linkedin" target="_blank" rel="noopener">LinkedInen</a>, e-mailt pedig a <a href="mailto::email">:email</a> címre írhatsz.',
    'cv_pdf' => 'Az önéletrajzom <a href=":url">PDF-ben</a> is elérhető.',

    'experience' => [
        [
            'title' => 'IT Ops / L2 support, majd L3 / DevOps, Optimy (Brüsszel)',
            'from' => '2024',
            'to' => null,
            'body' => 'Az Optimy AWS-en és Kubernetesen fut, és több száz ügyfélszervezetet szolgál ki. Amikor csatlakoztam, a support csapatnak sok kézi feladata és nagyon kevés dokumentációja volt. Először minden folyamatot dokumentáltam, aztán automatizáltam az ismétlődőket, később pedig többet közülük beépítettünk magába a termékbe. Az összes egyedi ügyfélfejlesztést átköltöztettem egy egységes PHP 8 keretrendszerre, és átvettem az ügyfelek SSL-tanúsítványainak és egyedi e-mail-küldő domainjeinek kezelését. Emellett részt veszek a katasztrófa-helyreállítási gyakorlatokban, code review-t végzek, és visszajelzést adok a termékcsapatnak az új funkciók hatásáról. Mostanában főleg a DevOps csapattal dolgozom a Kubernetes, Helm és Grafana/Loki stacken, valamint a projekteknél leírt automatikus hibavalidáló pipeline-on. 2026 októbere óta L3 / DevOps szerepben állok át a fejlesztésre.',
        ],
        [
            'title' => 'Szabadúszó fejlesztő',
            'from' => '2023-03',
            'to' => '2023-12',
            'body' => 'Egy nagy projekt: eszköz- és szerződéskezelő platform egy magyar holdingnak, amelynek több száz telephelye, üzlete és lakása van. Egyedül terveztem és építettem meg Laravelben és Vue-ban, és 2023 decemberében adtam át. Azóta is naponta használják. Az ügyfeleim weboldalait a saját fizikai szerveremen hosztoltam, amit egy DevOps mérnök barátommal közösen állítottunk be. Itt kezdett érdekelni az infrastruktúra.',
        ],
        [
            'title' => 'Junior, majd full-stack fejlesztő, Creative Brand Solutions Kft.',
            'from' => '2020-02',
            'to' => '2023-02',
            'body' => 'Kisebb WordPress projektekkel kezdtem, aztán full-stack Laravel és Vue fejlesztésig jutottam webshopokon, landing oldalakon és a mögöttük álló rendszereken. Az utolsó nagy projektem ott egy munkalapkezelő rendszer volt egy autószerviznek. Én vezettem, a távozásom előtt befejeztem, és a szerviz azóta is ezt használja.',
        ],
    ],

    'education' => [
        [
            'title' => 'Programtervező informatikus, Eötvös Loránd Tudományegyetem (ELTE), Budapest',
            'from' => '2017',
            'to' => '2020',
            'body' => 'Három évig tanultam. Egy teljes munkaidős fejlesztői állás miatt abbahagytam, a diplomát nem szereztem meg.',
        ],
    ],

    'side_projects' => [
        'nfl' => [
            'title' => 'NFL tippjáték',
            'meta' => '2023 – jelenleg',
            'body' => 'Meghívásos oldal, ahol a barátaimmal minden héten megtippeljük az összes NFL-meccs győztesét, és a szezon során ranglistán versenyzünk. Heti tippűrlapok, eredmények, pontozótábla és adminfelület a szezon beállításához. Laravel, Vue és Tailwind. 2023 decemberében indult, és minden szezonban kap új funkciókat.',
        ],
        'photography' => [
            'title' => 'Fotós portfólió',
            'meta' => '2026',
            'body' => 'Weboldal a barátnőm fotós munkáihoz: galériák, bemutatkozó oldal, és egy lehetőség, hogy az ügyfelek felvegyék vele a kapcsolatot.',
        ],
    ],
];
