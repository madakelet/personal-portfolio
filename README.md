# personal-portfolio

Personal website: CV plus blog, in English, Dutch and Hungarian. Laravel 13
with Blade and Tailwind 4 for the public pages. The approved design and copy
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

```sh
php artisan test       # feature and unit tests
vendor/bin/pint        # PHP code style
```

## Where things live

- `config/site.php`: email and side project links.
- `.env`: `SITE_GITHUB_URL` and `SITE_LINKEDIN_URL`.
- `lang/{en,nl,hu}/cv.php`: the landing page copy, one file per language.
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
