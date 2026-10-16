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

GitHub Actions builds everything (Composer without dev packages, Vite
assets) and uploads a ready release over SSH. The server only needs PHP
8.3 or newer, SSH access and Let's Encrypt. No Node or Composer on the
server.

Layout on the server, under a base directory such as
`/var/www/vhosts/<domain>/portfolio`:

```
releases/<git sha>/   one directory per deploy, newest 3 kept
shared/.env           created once by hand, never deployed
shared/storage/       logs, sessions, cache
shared/database/      database.sqlite
current -> releases/<git sha>
```

`scripts/activate-release.sh` runs on the server after each upload. It
links `shared/` into the release, runs migrations and `artisan optimize`,
then switches `current` to the new release. To roll back, point
`current` at an older release directory.

### One-time setup in Plesk

1. **PHP Settings:** PHP 8.3 or newer, FPM, OPcache on.
2. **Hosting Settings:** set the document root to
   `portfolio/current/public`.
3. **SSH access:** enable it for the subscription user and add the
   public half of a new deploy key to `~/.ssh/authorized_keys`.
4. **SSL/TLS:** issue a Let's Encrypt certificate and redirect HTTP to
   HTTPS.
5. **nginx only:** if nginx serves PHP directly (no Apache), add this
   under Apache & nginx Settings, Additional nginx directives:
   ```nginx
   location / { try_files $uri $uri/ /index.php?$query_string; }
   ```
6. Create `shared/.env`:
   ```sh
   APP_NAME=Portfolio
   APP_ENV=production
   APP_KEY=        # php artisan key:generate --show
   APP_DEBUG=false
   APP_URL=https://<domain>
   LOG_CHANNEL=daily
   DB_CONNECTION=sqlite
   DB_DATABASE=/var/www/vhosts/<domain>/portfolio/shared/database/database.sqlite
   SESSION_DRIVER=database
   SESSION_SECURE_COOKIE=true
   CACHE_STORE=database
   ```

### GitHub configuration

Repository secrets:

| Secret | Value |
|---|---|
| `DEPLOY_HOST` | server hostname |
| `DEPLOY_USER` | Plesk subscription SSH user |
| `DEPLOY_PATH` | base directory, e.g. `/var/www/vhosts/<domain>/portfolio` |
| `DEPLOY_SSH_KEY` | private half of the deploy key |
| `DEPLOY_KNOWN_HOSTS` | output of `ssh-keyscan <host>` |
| `DEPLOY_PHP_BIN` | optional, e.g. `/opt/plesk/php/8.3/bin/php` |

Then set the repository variable `DEPLOY_ENABLED` to `true`. Every push
to `main` that passes the tests is deployed.

After a deploy, PHP may serve the previous release for up to two
minutes because of its realpath cache. Restarting PHP-FPM from Plesk
clears it right away.
