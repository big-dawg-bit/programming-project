<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- Kop --}}
    <div>
        <flux:heading size="xl">Logboeken</flux:heading>
        <flux:subheading>Klik op een stagiair om hun weeklogs te lezen (alleen-lezen).</flux:subheading>
    </div>

    @if (! $company)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300">
            <p class="font-medium">Geen bedrijf gekoppeld</p>
        </div>
    @elseif ($stages->isEmpty())
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:heading size="lg">Geen stagiairs</flux:heading>
        </div>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($stages as $stage)
                <div wire:key="stage-{{ $stage->id }}" class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                    {{-- Stagiair-rij (klikbaar) --}}
                    <button type="button" wire:click="toggle({{ $stage->id }})"
                            class="flex w-full items-center justify-between px-5 py-4 text-left hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                        <div>
                            <p class="font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</p>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $stage->weeklogs->count() }} weeklogs</p>
                        </div>
                        <flux:icon :name="$openStageId === $stage->id ? 'chevron-up' : 'chevron-down'" class="size-5 text-neutral-400" />
                    </button>

                    {{-- Weeklogs van deze stagiair --}}
                    @if ($openStageId === $stage->id)
                        <div class="border-t border-neutral-200 dark:border-neutral-800">
                            @forelse ($stage->weeklogs as $weeklog)
                                @php
                                    $periode = collect([$weeklog->period_start, $weeklog->period_end])
                                        ->filter()
                                        ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y'))
                                        ->implode(' - ');
                                @endphp
                                <div wire:key="wl-{{ $weeklog->id }}" class="border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-medium">Week {{ $weeklog->week_number }}</p>
                                        <x-status-badge :status="$weeklog->status" />
                                    </div>
                                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ $periode ?: '—' }}</p>
                                    <p class="mt-2 whitespace-pre-line text-sm text-neutral-700 dark:text-neutral-300">{{ $weeklog->content ?: 'Geen inhoud.' }}</p>
                                </div>
                            @empty
                                <p class="px-5 py-4 text-sm text-neutral-500 dark:text-neutral-400">Nog geen weeklogs.</p>
                            @endforelse
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
