<!DOCTYPE html>
<html lang="nl" class="bg-neutral-50">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-neutral-50 text-neutral-900 antialiased">
    {{-- Gedeelde basistemplate (EhB Stage). Gebruik in een Livewire-component:
         #[Layout('layouts.portal')]
         en zet de pagina-inhoud gewoon in de view; die komt in {{ $slot }}. --}}
    <div class="flex min-h-screen">

        {{-- ===== Sidebar ===== --}}
        <aside class="hidden w-60 shrink-0 flex-col border-r border-neutral-200 bg-white md:flex">
            <div class="flex h-16 items-center gap-2 border-b border-neutral-100 px-6">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-neutral-900 text-sm font-bold text-white">E</span>
                <span class="font-semibold">EhB Stage</span>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-4">
                @php
                    // Navigatie-items. Bestaat de route nog niet, gebruik dan '#'.
                    $items = [
                        ['label' => 'Dashboard',   'icon' => 'squares-2x2',     'route' => 'student.dashboard'],
                        ['label' => 'Mijn stage',  'icon' => 'briefcase',       'route' => null],
                        ['label' => 'Weeklogs',    'icon' => 'book-open',       'route' => 'weeklogs.index'],
                        ['label' => 'Eindrapport', 'icon' => 'document-text',   'route' => 'final-report.edit'],
                        ['label' => 'Evaluaties',  'icon' => 'star',            'route' => null],
                        ['label' => 'Instellingen','icon' => 'cog-6-tooth',     'route' => 'profile.edit'],
                    ];
                @endphp

                @foreach ($items as $item)
                    @php
                        $url = $item['route'] ? route($item['route']) : '#';
                        $active = $item['route'] && request()->routeIs($item['route']);
                    @endphp
                    <a href="{{ $url }}" wire:navigate
                       @class([
                           'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                           'bg-neutral-100 text-neutral-900' => $active,
                           'text-neutral-600 hover:bg-neutral-50 hover:text-neutral-900' => ! $active,
                       ])>
                        <flux:icon :name="$item['icon']" class="size-5 {{ $active ? 'text-red-500' : 'text-neutral-400' }}" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        {{-- ===== Hoofdkolom ===== --}}
        <div class="flex flex-1 flex-col">
            {{-- Topbar --}}
            <header class="flex h-16 items-center justify-between border-b border-neutral-200 bg-white px-6">
                <h1 class="text-lg font-semibold">{{ $title ?? 'Dashboard' }}</h1>
                <div class="flex items-center gap-4">
                    <flux:icon name="bell" class="size-5 text-neutral-400" />
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-red-500 text-sm font-semibold text-white">
                        {{ auth()->user()?->initials() ?? 'U' }}
                    </span>
                </div>
            </header>

            {{-- Pagina-inhoud --}}
            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @fluxScripts
</body>
</html>
