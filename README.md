# Stage Monitoring Tool – EHB

Een webapplicatie om het volledige stage-traject van studenten aan Erasmushogeschool Brussel digitaal te beheren: van aanvraag tot finale evaluatie.

---

## 📖 Over het project

De Stage Monitoring Tool ondersteunt het volledige stage-proces binnen EHB en vervangt de huidige versnipperde workflow met één centraal platform. Het systeem dekt:

- **Stage-aanvraag** door de student
- **Goedkeuring** door de stagecommissie
- **Upload van de stageovereenkomst**
- **Wekelijkse logboeken** tijdens de stageperiode
- **Competentiegerichte evaluatie** (mid-term en finaal)

Een centrale eis van het project is dat de **evaluatiestructuur volledig configureerbaar** is: competenties kunnen worden aangepast in aantal, inhoud en gewicht, zonder dat de evaluatielogica hardcoded is. Zo blijft het systeem flexibel voor toekomstige beleidswijzigingen.

---

## 👥 Team

| Naam | GitHub | Rol |
|------|--------|-----|
| Arnaud Raspe | [@big-dawg-bit](https://github.com/big-dawg-bit) | Backend, datamodel & evaluatiemodule |
| Phillipe Wilangi-Otongi | [@PhilippeAI2025](https://github.com/PhilippeAI2025) | Front-end & GitHub-beheer |
| Nail Azam | [@Nail439](https://github.com/Nail439) | Authenticatie & login |
| Thomas Kongole | [@thomaskongolo11](https://github.com/thomaskongolo11) | Stage-aanvragen, student-portaal & seeding |
| Maxime Dekoster | [@maxime207](https://github.com/maxime207) | Nader te bepalen |

**Begeleidende docenten:** nader te bepalen.

---

## 🎯 Gebruikersrollen

Het systeem ondersteunt vijf rollen, elk met eigen rechten en functionaliteit:

1. **Student** – stage aanvragen, overeenkomst uploaden, logboeken bijhouden, evaluaties bekijken
2. **Stagecommissie** – stage-aanvragen beoordelen en goedkeuren
3. **EHB-docent** – toegewezen studenten opvolgen, evaluaties invullen
4. **Bedrijfsmentor** – logboeken valideren, evaluaties invullen
5. **Administratie** – gebruikersbeheer, rollen toewijzen, configureerbare competenties beheren

---

## 🛠️ Tech stack

| Categorie | Technologie |
|-----------|-------------|
| **Frontend** | Blade + Livewire 4 + Flux UI 2 + Tailwind CSS 4 |
| **Backend** | Laravel 13 (PHP 8.3+) |
| **Database** | MySQL |
| **Authenticatie** | Laravel Fortify (twee-factor + passkeys) |
| **Testing** | Pest 4 |
| **Build tooling** | Vite 8 |
| **Versiebeheer** | Git + GitHub |
| **Projectmanagement** | Trello (Kanban) |
| **Communicatie** | Microsoft Teams |
| **IDE** | Visual Studio Code / PhpStorm |

---

## 📁 Projectstructuur

```
app/
├── Http/Middleware/      # o.a. EnsureUserHasRole (rol-gebaseerde toegang)
├── Livewire/             # Full-page Livewire-componenten per domein
│   ├── Admin/            #   gebruikers- en framework-beheer
│   ├── Applications/     #   aanvraag indienen + beoordelingswachtrij
│   ├── Evaluations/      #   evaluatieformulier (mid-term/final)
│   ├── Student/          #   dashboard, stageoverzicht, documenten, evaluaties
│   └── Weeklogs/         #   weeklogs + eindrapport
└── Models/               # Eloquent-modellen (Stage, Weeklog, Evaluation, ...)

database/
├── migrations/           # Schema (afgeleid van het ERD)
└── seeders/              # DatabaseSeeder, StageSeeder, CompetencyFrameworkSeeder

resources/views/
├── layouts/              # portal- (student) en auth-layouts
└── livewire/             # Blade-views bij de Livewire-componenten

routes/web.php            # Rol-gescopede routes
tests/                    # Pest feature- en unit-tests
docs/                     # ERD en projectdocumentatie
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

Maak vervolgens een lege MySQL-database aan en vul de gegevens in `.env` in:

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

> Tip: tijdens het ontwikkelen laat je `npm run dev` in een apart terminalvenster open staan voor live herladen. Voor een stabiele demo gebruik je `npm run build`.

---

## 🔑 Demo-accounts

Na `php artisan migrate:fresh --seed` zijn deze accounts beschikbaar (wachtwoord telkens **`password`**):

| Rol | E-mail |
|-----|--------|
| Student | `student@ehb.be` |
| Student (extra demo) | `sam@ehb.be`, `yusuf@ehb.be`, `marie@ehb.be`, `tom@ehb.be` |
| Docent | `docent@ehb.be` |
| Mentor | `mentor@easi.net` |
| Stagecommissie | `commissie@ehb.be` |
| Administratie | `admin@ehb.be` |

De seed vult elk portaal met realistische data: aanvragen in elke status, actieve stages, verspreide weeklogs en mid-term/final evaluaties.

---

## ✅ Tests

De testsuite draait op Pest:

```bash
php artisan test
```

---

## 🐳 Deployment (Docker)

> ⚠️ De Docker- en deployment-stappen worden aangeleverd door **Arnaud** (verantwoordelijke deployment) en hier toegevoegd zodra ze klaar zijn. Voor lokaal ontwikkelen volstaat de installatie hierboven.
