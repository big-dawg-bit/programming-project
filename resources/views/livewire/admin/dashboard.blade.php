<div class="mx-auto flex max-w-6xl flex-col gap-8">
    <div>
        <h2 class="text-2xl font-bold">Beheer</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">Overzicht van het platform.</p>
    </div>

    {{-- Kerncijfers --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['label' => 'Gebruikers', 'waarde' => $aantalGebruikers, 'icon' => 'users'],
            ['label' => 'Bedrijven', 'waarde' => $aantalBedrijven, 'icon' => 'building-office-2'],
            ['label' => 'Evaluatiekaders', 'waarde' => $aantalKaders, 'icon' => 'star'],
            ['label' => 'Logboek-items', 'waarde' => $aantalLogs, 'icon' => 'document-text'],
        ] as $kpi)
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                    <flux:icon :name="$kpi['icon']" class="size-4" /> {{ $kpi['label'] }}
                </div>
                <p class="mt-3 text-3xl font-semibold">{{ $kpi['waarde'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        {{-- Gebruikers per rol --}}
        <section>
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
                <flux:button href="{{ route('admin.framework') }}" wire:navigate size="sm" variant="ghost" icon="star">Evaluatiekader</flux:button>
                <flux:button href="{{ route('admin.audit') }}" wire:navigate size="sm" variant="ghost" icon="document-text">Logboek</flux:button>
            </div>
        </section>

        {{-- Recente beheeractiviteit --}}
        <section>
            <h3 class="mb-3 text-lg font-semibold">Recente beheeractiviteit</h3>
            <div class="space-y-4">
                @forelse ($recenteLogs as $log)
                    <div class="flex gap-3">
                        <flux:icon name="clock" class="mt-0.5 size-5 shrink-0 text-neutral-400 dark:text-neutral-500" />
                        <div>
                            <p class="text-sm font-medium">{{ $log->action }}</p>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $log->user?->name ?? 'Systeem' }}</p>
                            <p class="mt-0.5 text-xs text-neutral-400 dark:text-neutral-500">{{ $log->created_at?->locale('nl')->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Nog geen beheeractiviteit.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
