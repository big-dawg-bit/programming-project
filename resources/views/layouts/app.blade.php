<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Stage Monitor' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-800 antialiased">
<flux:sidebar sticky collapsible="mobile"
              class="bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">

    <flux:sidebar.header>
        <flux:sidebar.brand href="/" name="Stage Monitor" />
        <flux:sidebar.collapse class="lg:hidden" />
    </flux:sidebar.header>

    <flux:sidebar.nav>
        <flux:sidebar.item icon="home" href="/dashboard/student" current>Dashboard</flux:sidebar.item>
        <flux:sidebar.item icon="briefcase" href="/stage-aanvraag">Stage aanvragen</flux:sidebar.item>
        <flux:sidebar.item icon="document-text" href="/logboeken">Logboeken</flux:sidebar.item>
        <flux:sidebar.item icon="clipboard-document-check" href="/evaluaties">Evaluaties</flux:sidebar.item>
        <flux:sidebar.item icon="question-mark-circle" href="/faq">FAQ</flux:sidebar.item>
    </flux:sidebar.nav>

    <flux:sidebar.spacer />

    <flux:dropdown position="top" align="start">
        <flux:sidebar.profile name="Arnaud Raspé" />
        <flux:menu>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button"
                >
                    Uitloggen
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
</flux:header>

<flux:main>
    {{ $slot }}
</flux:main>

@fluxScripts
</body>
</html>
