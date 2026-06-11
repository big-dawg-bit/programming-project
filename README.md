# programming-project
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
| Arnaud Raspe | [@big-dawg-bit](https://github.com/big-dawg-bit) | [Bv. Backend / Database] |
| Phillipe Wilangi-Otongi | [@PhilippeAI2025](https://github.com/PhilippeAI2025) | [Bv. Front-end / Github] |
| Nail Azam | [@Nail439](https://github.com/Nail439) | [Front-end / UI] |
| Thomas Kongole | [@thomaskongolo11](https://github.com/thomaskongolo11) | [Rol] |
| Maxime Dekoster | [@Maxime207](https://github.com/maxime207) 

**Begeleidende docenten:** [Naam docent 1], [Naam docent 2]

---

## 🎯 Gebruikersrollen

Het systeem ondersteunt vijf rollen, elk met eigen rechten en functionaliteit:M

1. **Student** – stage aanvragen, agreement uploaden, logboeken bijhouden, evaluaties bekijken
2. **Stagecommissie** – stage-aanvragen beoordelen en goedkeuren
3. **EHB-docent** – toegewezen studenten opvolgen, evaluaties invullen
4. **Bedrijfsmentor** – logboeken valideren, evaluaties invullen
5. **Administratie** – gebruikersbeheer, rollen toewijzen, configureerbare competenties beheren

---

## 🛠️ Tech stack

| Categorie | Technologie |
|-----------|-------------|
| **Frontend** | [Blade + Tailwind CSS + Flux UI (Livewire Flux)] |
| **Backend** | [Laravel 13 (PHP 8.3) ] |
| **Database** | [MySQL] |
| **Authenticatie** | [Laravel Authentication (Flux/Breeze starter kit)] |
| **Versiebeheer** | Git + GitHub |
| **Projectmanagement** | Trello (Kanban) |
| **Communicatie** | Microsoft Teams + Whatsapp |
| **IDE** | PhpStorm |

---

## 📁 Projectstructuur

```
