<div class="mx-auto flex max-w-6xl flex-col gap-8">
    {{-- Begroeting --}}
    <div>
        <h2 class="text-2xl font-bold">Hallo {{ auth()->user()->first_name ?? auth()->user()->name }}</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            {{ $mentor?->company?->name ?? 'Mentor' }} · {{ $stages->count() }} {{ \Illuminate\Support\Str::plural('stagiair', $stages->count()) }}
        </p>
    </div>

    @if (session('mentor-approved'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
            {{ session('mentor-approved') }}
        </div>
    @endif

    {{-- Statuskaarten --}}
    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="academic-cap" class="size-4" /> Mijn stagiairs
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $stages->count() }}</p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="document-text" class="size-4" /> Weeklogs totaal
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $stages->sum('weeklogs_count') }}</p>
        </div>
    </div>

    {{-- Lijst stagiairs --}}
    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
            <h3 class="font-semibold">Mijn stagiairs</h3>
        </div>

        @forelse ($stages as $stage)
            @php
                $agreement = $stage->application?->agreement;
                $overeenkomst = match ($agreement?->status) {
                    'te_ondertekenen' => ['Wacht op handtekeningen', 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300'],
                    'ingediend'       => ['Getekend — wacht op commissie', 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300'],
                    'bevestigd'       => ['Overeenkomst bevestigd', 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
                    default           => null,
                };
            @endphp
            <div class="flex items-center justify-between gap-3 border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                <div>
                    <p class="font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $stage->weeklogs_count }} weeklogs ingediend
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if ($overeenkomst)
                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $overeenkomst[1] }}">{{ $overeenkomst[0] }}</span>
                    @elseif ($stage->application_id)
                        <flux:button size="sm" variant="primary" icon="check"
                            wire:click="approve({{ $stage->id }})"
                            wire:confirm="Akkoord geven op deze stage? Hiermee ontstaat de stageovereenkomst.">
                            Akkoord geven
                        </flux:button>
                    @else
                        <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                            {{ ucfirst($stage->status ?? 'active') }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-neutral-500 dark:text-neutral-400">
                Nog geen stagiairs toegewezen.
            </p>
        @endforelse
    </div>
</div>
