<div class="mx-auto flex max-w-6xl flex-col gap-8">
    {{-- Begroeting --}}
    <div>
        <h2 class="text-2xl font-bold">Hallo {{ auth()->user()->first_name ?? auth()->user()->name }}</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            {{ $stages->count() }} begeleide {{ \Illuminate\Support\Str::plural('stage', $stages->count()) }}
        </p>
    </div>

    {{-- Statuskaarten --}}
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="users" class="size-4" /> Begeleide stages
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $stages->count() }}</p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="clipboard-document-check" class="size-4" /> Nog te evalueren
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $teEvalueren }}</p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="document-text" class="size-4" /> Totaal weeklogs
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $stages->sum('weeklogs_count') }}</p>
        </div>
    </div>

    {{-- Lijst begeleide stages --}}
    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
            <h3 class="font-semibold">Mijn stages</h3>
        </div>

        @forelse ($stages as $stage)
            @php
                // Leesbare status van de overeenkomst (de docent tekent niet mee).
                $overeenkomstChip = match ($stage->application?->agreement?->status) {
                    'te_ondertekenen' => ['Wacht op ondertekening', 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300'],
                    'ingediend' => ['Bij de stagecommissie', 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300'],
                    'bevestigd' => ['Overeenkomst bevestigd', 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
                    default => null,
                };
            @endphp
            <div class="flex items-center justify-between gap-3 border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                <div>
                    <p class="font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $stage->company?->name ?? '—' }} · {{ $stage->weeklogs_count }} weeklogs
                    </p>
                </div>
                @if ($overeenkomstChip)
                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $overeenkomstChip[1] }}">{{ $overeenkomstChip[0] }}</span>
                @endif
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-neutral-500 dark:text-neutral-400">
                Nog geen stages aan jou toegewezen.
            </p>
        @endforelse
    </div>
</div>
