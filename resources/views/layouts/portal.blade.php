<!DOCTYPE html>
<html lang="nl" class="bg-neutral-50">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-neutral-50 text-neutral-900 antialiased">
    {{-- Gedeelde basistemplate (EhB Stage student-portal). Gebruik in een Livewire-component:
         #[Layout('layouts.portal')]
         en zet de pagina-inhoud gewoon in de view; die komt in {{ $slot }}. --}}
    <div class="flex min-h-screen">

        {{-- ===== Sidebar ===== --}}
        <aside class="hidden w-64 shrink-0 flex-col border-r border-neutral-200 bg-white md:flex">
            <div class="flex h-16 items-center border-b border-neutral-100 px-6">
                <span class="text-lg font-bold tracking-tight">EhB Stage</span>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-4">
                @php
                    // Navigatie van de studenten-portal. Routes die nog niet bestaan
                    // vallen netjes terug op '#' tot de bijhorende pagina gebouwd is.
                    $items = [
                        ['label' => 'Dashboard',    'icon' => 'squares-2x2',   'route' => 'student.dashboard'],
                        ['label' => 'Mijn stage',   'icon' => 'briefcase',     'route' => 'student.stage'],
                        ['label' => 'Weeklogs',     'icon' => 'document-text', 'route' => 'weeklogs.index'],
                        ['label' => 'Evaluaties',   'icon' => 'star',          'route' => 'student.evaluaties'],
                        ['label' => 'Documenten',   'icon' => 'folder',        'route' => 'student.documenten'],
                        ['label' => 'Instellingen', 'icon' => 'cog-6-tooth',   'route' => 'profile.edit'],
                    ];
                @endphp

                @foreach ($items as $item)
                    @php
                        $exists = $item['route'] && \Illuminate\Support\Facades\Route::has($item['route']);
                        $url = $exists ? route($item['route']) : '#';
                        $active = $exists && request()->routeIs($item['route']);
                    @endphp
                    <a href="{{ $url }}" @if ($exists) wire:navigate @endif
                       @class([
                           'relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
                           'bg-neutral-100 text-neutral-900' => $active,
                           'text-neutral-600 hover:bg-neutral-50 hover:text-neutral-900' => ! $active,
                       ])>
                        @if ($active)
                            <span class="absolute left-0 top-1/2 h-6 w-[3px] -translate-y-1/2 rounded-r-full bg-[#E2231A]"></span>
                        @endif
                        <flux:icon :name="$item['icon']" class="size-5 {{ $active ? 'text-[#E2231A]' : 'text-neutral-400' }}" />
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
                    <flux:dropdown position="bottom" align="end">
                        <button type="button" class="grid h-9 w-9 place-items-center rounded-full bg-[#E2231A] text-white">
                            <flux:icon name="user" class="size-5" />
                        </button>
                        <flux:menu>
                            <flux:menu.item :href="route('profile.edit')" icon="cog-6-tooth" wire:navigate>
                                Instellingen
                            </flux:menu.item>
                            <flux:menu.separator />
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
