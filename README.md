# personal-portfolio

Personal website: CV plus blog, in English, Dutch and Hungarian. Laravel 13
with Blade and Tailwind 4 for the public pages, and an admin under `/admin`
(Inertia + Vue) for editing the site's texts. The approved design and copy
live in `prototype/prototype.html`, and the project brief for Claude Code is
in `CLAUDE.md`.

## Local development

```sh
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev    # or: php artisan serve + npm run dev
```

Open http://localhost:8000. It redirects to `/en`, `/nl` or `/hu`.

## Admin

```sh
php artisan make:owner      # create the one admin account, or reset its password
```

Log in at `/login`, then edit texts at `/admin/texts`. Each text shows
English, Dutch and Hungarian side by side. Saving changes the live site
immediately.

The lang files are the defaults. An edit is stored in the `translations`
table and laid over the file value; reset deletes it. Edits made on the
server live in its database, not in git. To bring them into the repo:

```sh
php artisan texts:export            # write edits into lang/{locale}/{group}.php
php artisan texts:export --clear    # same, then delete the exported rows
```

The export rewrites the arrays, so comments inside them are dropped.
Check the diff before committing.

Projects have their own page, `/admin/projects`, where they can be added,
edited, reordered and removed. They live only in the `projects` table, not
in the lang files, so `texts:export` does not cover them.

Adding or removing a job, education entry, side project or bio paragraph
still means changing the English lang file (and the others, which a test
checks).

```sh
php artisan test       # feature and unit tests
vendor/bin/pint        # PHP code style
```

## Where things live

- `config/site.php`: email and side project links.
- `.env`: `SITE_GITHUB_URL` and `SITE_LINKEDIN_URL`.
- `lang/{en,nl,hu}/cv.php`: the landing page copy, one file per language.
  These are the defaults; edits from the admin override them.
- `lang/{en,nl,hu}/ui.php`: your name (Hungarian puts the family name
  first), navigation, headings, date formats, meta tags.
- `resources/css/app.css`: design tokens and the hand-written styles.
- `public/cv.pdf`: drop the CV here and the links to it appear.

## Hosting

The site runs on a Plesk server. There is no deploy pipeline; deploys are
done by hand. What the server needs:

- PHP 8.3 or newer (Composer's platform is pinned to 8.3).
- The document root pointing at the `public/` directory.
- A `.env` created on the server (`APP_ENV=production`, `APP_DEBUG=false`,
  `APP_KEY`, `APP_URL`, database settings). It is not in git.
- `vendor/` from `composer install --no-dev` and `public/build/` from
  `npm run build`, either built on the server or uploaded.
- After each deploy: `php artisan migrate --force` and
  `php artisan optimize`.
- Once, after the first deploy: `php artisan make:owner`.
- Back up the database: edited texts and all projects live there.
