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

## Deploying to Plesk

Plesk's Git extension pulls the repository and runs the build on the
server. The server needs PHP 8.3 or newer, Composer, Node.js 22 (Plesk's
Node.js extension) and Let's Encrypt.

### One-time setup

1. **PHP Settings:** PHP 8.3 or newer, FPM, OPcache on.
2. **Git:** add the repository, deploy mode "Automatic" or "Manual",
   deployment path `httpdocs`.
3. **Hosting Settings:** set the document root to `httpdocs/public`.
4. **SSL/TLS:** issue a Let's Encrypt certificate and redirect HTTP to
   HTTPS.
5. **nginx only:** if nginx serves PHP directly (no Apache), add this
   under Apache & nginx Settings, Additional nginx directives:
   ```nginx
   location / { try_files $uri $uri/ /index.php?$query_string; }
   ```
6. Create `httpdocs/.env` by hand in the File Manager. It is not in git:
   ```sh
   APP_NAME=Portfolio
   APP_ENV=production
   APP_KEY=        # php artisan key:generate --show
   APP_DEBUG=false
   APP_URL=https://<domain>
   LOG_CHANNEL=daily
   DB_CONNECTION=sqlite
   DB_DATABASE=/var/www/vhosts/<domain>/httpdocs/database/database.sqlite
   SESSION_DRIVER=database
   SESSION_SECURE_COOKIE=true
   CACHE_STORE=database
   ```
7. Create the empty database file once: `touch database/database.sqlite`.

### Deployment actions

Paste these into the Git settings under "Enable additional deployment
actions". Plesk runs them in the deployment path after each pull.
Binary paths differ per server, so check them first with
`ls /opt/plesk/php/ /opt/plesk/node/` and `ls /usr/lib*/plesk-9.0/composer.phar`.

```sh
/opt/plesk/php/8.3/bin/php /usr/lib/plesk-9.0/composer.phar install --no-dev --optimize-autoloader --no-interaction
/opt/plesk/node/22/bin/npm ci
/opt/plesk/node/22/bin/npm run build
/opt/plesk/php/8.3/bin/php artisan migrate --force
/opt/plesk/php/8.3/bin/php artisan optimize
```

Run `php artisan test` and `vendor/bin/pint --test` locally before
pushing; nothing runs them on the server.
