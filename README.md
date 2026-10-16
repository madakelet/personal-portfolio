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

- `config/site.php`: name, email, GitHub, LinkedIn and side project links.
- `lang/{en,nl,hu}/cv.php`: the landing page copy, one file per language.
- `lang/{en,nl,hu}/ui.php`: navigation, headings, date formats, meta tags.
- `resources/css/app.css`: design tokens and the hand-written styles.
- `public/cv.pdf`: drop the CV here and the links to it appear.
