# Stage Monitoring Tool – EHB

Een webapplicatie om het volledige stage-traject van studenten aan Erasmushogeschool Brussel digitaal te beheren: van aanvraag tot finale evaluatie.

---

## 📖 Over het project

De Stage Monitoring Tool ondersteunt het volledige stage-proces binnen EHB en vervangt de versnipperde workflow door één centraal platform. Het systeem dekt:

- **Stage-aanvraag** door de student
- **Beoordeling en goedkeuring** door de stagecommissie
- **Toewijzing van docent en bedrijfsmentor**
- **Ondertekening van de stageovereenkomst** (student, bedrijf en docent)
- **Wekelijkse logboeken** met opmerkingen van de mentor
- **Competentiegerichte evaluatie** (mid-term en finaal), inclusief zelfevaluatie van de student en de eindconclusie van de docent

Een centrale eis van het project is dat de **evaluatiestructuur volledig configureerbaar** is: competenties kunnen worden aangepast in aantal, inhoud en gewicht, zonder dat de evaluatielogica hardcoded is. Zo blijft het systeem flexibel voor toekomstige beleidswijzigingen.

---

## 👥 Team

| Naam | GitHub | Rol |
|------|--------|-----|
| Arnaud Raspe | [@big-dawg-bit](https://github.com/big-dawg-bit) | Backend, datamodel, evaluatiemodule & deployment |
| Phillipe Wilangi-Otongi | [@PhilippeAI2025](https://github.com/PhilippeAI2025) | Front-end & GitHub-beheer |
| Nail Azam | [@Nail439](https://github.com/Nail439) | Authenticatie & login |
| Thomas Kongole | [@thomaskongolo11](https://github.com/thomaskongolo11) | Stage-aanvragen, student-portaal & seeding |
| Maxime Dekoster | [@maxime207](https://github.com/maxime207) | Front-end & componenten |

---

## 🎯 Gebruikersrollen

Het systeem ondersteunt zes rollen, elk met een eigen portaal en rechten (afgedwongen via de `role`-middleware):

1. **Student** – stage aanvragen, overeenkomst ondertekenen/uploaden, weeklogs bijhouden, eindrapport indienen, evaluaties en zelfevaluatie invullen
2. **Stagecommissie** – aanvragen beoordelen en goedkeuren, overeenkomsten opvolgen
3. **EHB-docent** – toegewezen studenten opvolgen, weeklogs en rapporten bekijken, evaluaties en de eindbeoordeling invullen
4. **Bedrijfsmentor** – logboeken valideren, documenten raadplegen, evaluaties invullen
5. **Bedrijf** – inkomende aanvragen behandelen, mentor toewijzen, overeenkomsten ondertekenen, logboeken opvolgen
6. **Administratie** – gebruikersbeheer, rollen en toewijzingen, beheer van het configureerbare competentieframework

---

## 🛠️ Tech stack

| Categorie | Technologie |
|-----------|-------------|
| **Backend** | Laravel 13 (PHP 8.3+) |
| **Frontend** | Blade + Livewire 4 + Flux UI 2 + Tailwind CSS 4 |
| **Database** | MySQL 8 |
| **Authenticatie** | Laravel Fortify (twee-factor + passkeys) |
| **Testing** | Pest 4 |
| **Build tooling** | Vite 8 |
| **Code style** | Laravel Pint |
| **CI/CD** | GitHub Actions (lint + tests) |
| **Deployment** | Docker (nginx + php-fpm + MySQL) |
| **Versiebeheer** | Git + GitHub |

---

## 📁 Projectstructuur

```
app/
├── Http/Middleware/      # o.a. EnsureUserHasRole (rol-gebaseerde toegang, alias 'role')
├── Livewire/             # Full-page Livewire-componenten per domein
│   ├── Admin/            #   gebruikers-, toewijzings- en frameworkbeheer
│   ├── Applications/     #   aanvraag indienen + beoordelingswachtrij + overeenkomsten
│   ├── Bedrijf/          #   aanvragen, mentor toewijzen, overeenkomsten, logboeken
│   ├── Docent/           #   dashboard, studenten, weeklogs, evaluaties, eindbeoordeling
│   ├── Evaluations/      #   gedeeld evaluatieformulier (mid-term/final)
│   ├── Mentor/           #   dashboard, studenten, documenten, evaluaties
│   ├── Student/          #   dashboard, mijn stage, documenten, evaluaties, zelfevaluatie
│   └── Weeklogs/         #   weeklogs + eindrapport
└── Models/               # 20 Eloquent-modellen (Stage, Weeklog, Evaluation, ...)

database/
├── migrations/           # 39 migraties (schema afgeleid van het ERD)
├── factories/            # Modelfactories voor tests en seeding
└── seeders/              # DatabaseSeeder, StageSeeder, CompetencyFrameworkSeeder

resources/views/
├── layouts/              # portal- en auth-layouts
└── livewire/             # Blade-views bij de Livewire-componenten

docker/                   # nginx-config + entrypoint
routes/web.php            # Rol-gescopede routes
tests/                    # 49 Pest feature- en unit-tests
docs/                     # ERD (DBML + PDF) en projectdocumentatie
```

---

## 🚀 Lokale installatie

Vereist: PHP 8.3+, Composer, Node.js + npm, en een MySQL-server. (Op Windows is [Laravel Herd](https://herd.laravel.com) de eenvoudigste manier voor PHP + Composer.)

```bash
# 1. Repository klonen
git clone https://github.com/big-dawg-bit/programming-project.git
cd programming-project

# 2. Afhankelijkheden installeren
composer install
npm install

# 3. Omgeving instellen
cp .env.example .env
php artisan key:generate
```

Maak vervolgens een lege MySQL-database `programming_project` aan en vul de gegevens in `.env` in:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=programming_project
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 4. Database opbouwen + demo-data vullen
php artisan migrate:fresh --seed

# 5. Front-end bouwen (of in dev: npm run dev in een apart venster)
npm run build

# 6. App starten
php artisan serve
```

De applicatie draait nu op **http://127.0.0.1:8000**.

> Tip: tijdens het ontwikkelen laat je `npm run dev` in een apart terminalvenster open voor live herladen. Voor een stabiele demo gebruik je `npm run build`. Met `composer dev` start je server, queue en Vite in één commando.

---

## 🔑 Demo-accounts

Na `php artisan migrate:fresh --seed` zijn deze accounts beschikbaar (wachtwoord telkens **`password`**):

| Rol | E-mail |
|-----|--------|
| Student | `student@ehb.be` |
| Student (extra demo) | `sam@ehb.be`, `yusuf@ehb.be`, `marie@ehb.be`, `tom@ehb.be` |
| Docent | `docent@ehb.be` |
| Mentor | `mentor@easi.net`, `mentor-bedrijf@bedrijf.be` |
| Bedrijf | `bedrijf@easi.net` |
| Stagecommissie | `commissie@ehb.be` |
| Administratie | `admin@ehb.be` |

De seed vult elk portaal met realistische data: aanvragen in elke status, actieve stages, verspreide weeklogs en mid-term/final evaluaties.

---

## 🧩 Configureerbaar competentieframework

De evaluatie draait op een **runtime-configureerbaar framework**: de administratie/stagecommissie beheert de competenties (aantal, titel, niveaubeschrijvingen en gewicht) via het admin-portaal. De gewichten van een framework tellen samen op tot **100**. Bij elke evaluatie wordt een `weight_snapshot` bewaard, zodat een latere wijziging van de gewichten bestaande evaluaties niet verandert.

Het standaard geseede framework *"Stage-evaluatie Toegepaste Informatica"* bevat:

| Code | Competentie | Gewicht |
|------|-------------|---------|
| TC | Technische competentie | 30 |
| COM | Communicatie | 20 |
| SAM | Samenwerken | 20 |
| ZEL | Zelfstandigheid | 15 |
| ATT | Professionele attitude | 15 |

Elke competentie wordt gescoord op een schaal **0–5**. De docent legt in de eindconclusie een gewogen score op **/100** vast met een **Geslaagd / Niet geslaagd**-badge.

---

## ✅ Tests & code style

De testsuite draait op Pest (49 tests):

```bash
php artisan test          # alle tests
composer test             # config clear + Pint check + tests (zoals in CI)
composer lint             # Pint automatisch toepassen
composer lint:check       # Pint enkel controleren
```

GitHub Actions draait bij elke push/PR automatisch de **lint**- en **tests**-workflows.

---

## 🐳 Deployment (Docker)

De applicatie is volledig containerized met drie services: **nginx** (`web`), **php-fpm** (`app`) en **MySQL 8.4** (`db`). De entrypoint genereert een `APP_KEY`, wacht op de database en draait `migrate --force --seed` automatisch.

```bash
# Bouwen en starten
docker compose up -d --build

# Logs volgen
docker compose logs -f app

# Stoppen
docker compose down
```

De app is daarna bereikbaar op **http://localhost:8080**.

> De productie-deployment draait op de Ubuntu-VM van de school (bereikbaar via VPN). De containers worden daar via `docker compose` opgezet.

---

## 🌳 Git-workflow

Er wordt gewerkt met een `develop`-branch als integratiebranch. Features verlopen via aparte branches en pull requests:

```bash
git fetch origin
git switch -c feature/naam origin/develop
# ... wijzigingen + conventional commit message ...
git push -u origin feature/naam
# vervolgens PR naar develop
```
