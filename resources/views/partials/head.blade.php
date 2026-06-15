<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

{{--
    De EhB-views (login, wachtwoord-flows en de studenten-portal) zijn volledig
    in light-mode ontworpen; er bestaat geen dark-variant in het Figma-ontwerp.
    Flux zet op basis van een opgeslagen voorkeur (of de systeeminstelling) de
    .dark-class op <html>, waardoor Flux-componenten donker renderen op onze
    lichte achtergrond en tekst onleesbaar wordt. We strippen die class daarom
    altijd. Dit staat ná @fluxAppearance en dus vóór de eerste paint (geen flash),
    en wordt herhaald na elke Livewire-navigatie. De opgeslagen voorkeur laten we
    ongemoeid; we negeren ze enkel voor deze light-only views.
--}}
<script>
    document.documentElement.classList.remove('dark');
    document.addEventListener('livewire:navigated', () => document.documentElement.classList.remove('dark'));
</script>
