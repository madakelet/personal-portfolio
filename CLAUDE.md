# Personal portfolio: CV + blog

Personal website for a software developer / DevOps engineer. It has to work
as a CV (recruiters land here) and host a blog (a few articles, written
occasionally). Owner is the repo author; see "Content" for who they are.

Status (2026-10-16): design and content are settled in a single-file
prototype. The real site has not been scaffolded yet.

## Start here

- `prototype/prototype.html` is the approved design and copy. Open it in a
  browser. It is a plain HTML file with inline CSS/JS and hash routing; the
  CV content is real, the blog posts are samples. Note: it has no
  `<!doctype>`/`<html>`/`<head>`/`<body>` wrapper because it was written
  for a hosted preview. Add those when porting.
- Design notes and the build plan live in the owner's Obsidian vault, not in
  this repo: `~/dev/obsidian/madakelet/Portfolio/` ("Portfolio design notes",
  "Portfolio site implementation plan"). Do not add planning docs here.

## Design decisions (do not relitigate)

Arrived at through three rounds of feedback. The owner rejected the first
version as "too AI made" and asked for real engineer sites as reference.

- Look like a working engineer's site, not a portfolio template. References:
  mitchellh.com, leerob.com, antfu.me, overreacted.io, jvns.ca,
  jeffgeerling.com, brandur.org.
- One centered column, max 42rem. Top bar with name left, text links right,
  and a `[auto]`/`[light]`/`[dark]` theme toggle. Footer with text links.
- No sidebar, cards, chips, badges, icon rows, hover animations, section
  "eyebrow" labels, hero, or stats tiles. These were all tried and removed.
- Fonts: IBM Plex Mono for headings, nav, dates, meta, footer, code.
  IBM Plex Sans for body text. Owner asked for "something more code like".
- Colour: black/white/grey plus ONE accent (burnt orange, #c2410c light,
  #fb923c dark). Owner asked for "really just a small color". Accent is
  used only for links, `#` before h2, `//` before year headings, `~/`
  before the name, the callout border, and selection.
- Light and dark themes both designed; tokens on `:root`, dark under
  `prefers-color-scheme` and `[data-theme="dark"]`.

## Tone of the copy (important)

The CV copy went through three drafts. Draft 1 was flat ("sounds a bit
lame"). Draft 2 was writerly and the owner called it "too AI". Draft 3,
now in the prototype, is the approved middle: plain first-person
sentences, concrete outcomes, no metaphors, no clever openers, no
"not X but Y" framing, no em-dashes. When editing copy, match draft 3.
Prefer changing one sentence over rewriting a section.

## Content

Owner: Hungarian software developer / DevOps engineer living in Belgium.
- Optimy, Brussels, Jan 2024 - present. IT Ops / L2 support, transitioning
  to engineering as L3 / DevOps since Oct 2026. Documented and automated L2
  processes, migrated custom client developments to a standardised PHP 8
  framework, owns client SSL certs and custom email sending domains, DRPs,
  code reviews, feature impact feedback for product. Works with the DevOps
  team on Kubernetes, Helm, Grafana, Loki. AWS: RDS, SQS, SES, S3, IAM.
  In 2026 built an automated bug-validation pipeline: Jira webhooks trigger
  Claude with access to repos and logs, no human in the loop.
- Freelance, Mar - Dec 2023. Asset and contract management platform for a
  Hungarian holding with hundreds of depots, shops and flats (contracts,
  income, invoices, client email, interactive parking map, utility meter
  photos, dashboards). Laravel + Vue, solo, live since Dec 2023. Also
  hosted client sites on own physical server with a DevOps engineer friend;
  this is where the infra interest started.
- Creative Brand Solutions Kft., Feb 2020 - Feb 2023. WordPress, then
  full-stack Laravel + Vue (webshops, landing pages, back-office). Led a
  worksheet manager for a car repair shop; still in use.
- Education: ELTE (Eotvos Lorand University), computer science, 2017-2020,
  no degree.
- Side projects: invite-only NFL pick'em app for friends (`~/dev/nfl-game`,
  Laravel 10 + Vue 3 + Tailwind, since Dec 2023, active); portfolio site
  for their girlfriend, a photographer (2026).

Still placeholders in the prototype: the owner's name ("Your Name"), email,
GitHub and LinkedIn URLs, the photography site description and both side
project URLs, and all blog posts.

## Suggested next steps

1. Pick a static site generator. Astro (Markdown/MDX content collections,
   RSS, tag pages for free) or Hugo. Owner's stack is PHP/Laravel/Vue, so
   Astro with no framework components is the lightest fit.
2. Port the prototype: pages `/` (about, experience, education, projects,
   side projects, latest writing), `/writing/` (year-grouped), `/writing/<slug>/`,
   `/cv.pdf`. Keep the CSS hand-written; the prototype's is about 100 lines.
3. Fill in the placeholders listed above.
4. Deploy to Cloudflare Pages or GitHub Pages.
