<div class="mx-auto flex max-w-6xl flex-col gap-8">
    <div>
        <h2 class="text-2xl font-bold">Beheer</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">Overzicht van het platform.</p>
    </div>

    {{-- Kerncijfers --}}
    <div class="grid gap-4 sm:grid-cols-3">
        @foreach ([
            ['label' => 'Gebruikers', 'waarde' => $aantalGebruikers, 'icon' => 'users'],
            ['label' => 'Bedrijven', 'waarde' => $aantalBedrijven, 'icon' => 'building-office-2'],
            ['label' => 'Evaluatiekaders', 'waarde' => $aantalKaders, 'icon' => 'star'],
        ] as $kpi)
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                    <flux:icon :name="$kpi['icon']" class="size-4" /> {{ $kpi['label'] }}
                </div>
                <p class="mt-3 text-3xl font-semibold">{{ $kpi['waarde'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Gebruikers per rol --}}
    <section class="max-w-xl">
        <h3 class="mb-3 text-lg font-semibold">Gebruikers per rol</h3>
        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <ul class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @foreach ($rollen as $rol)
                    <li class="flex items-center justify-between px-5 py-3">
                        <span class="text-sm">{{ $rol['naam'] }}</span>
                        <span class="text-sm font-semibold">{{ $rol['aantal'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Snelkoppelingen --}}
        <div class="mt-4 flex flex-wrap gap-2">
            <flux:button href="{{ route('admin.users') }}" wire:navigate size="sm" variant="primary" icon="users">Gebruikers beheren</flux:button>
            <flux:button href="{{ route('admin.framework') }}" wire:navigate size="sm" variant="ghost" icon="star">Evaluatiekader</flux:button>
            <flux:button href="{{ route('admin.assignments') }}" wire:navigate size="sm" variant="ghost" icon="user-plus">Toewijzingen</flux:button>
        </div>
    </section>
</div>
