<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- Kop met aanvraag-knop (altijd bereikbaar) --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold">Mijn stages</h2>
        <flux:button href="{{ route('applications.create') }}" wire:navigate variant="primary" icon="plus">
            Stage aanvragen
        </flux:button>
    </div>

    @if (session('stage-melding'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
            {{ session('stage-melding') }}
        </div>
    @endif

    @forelse ($applications as $application)
        @php
            $statusPill = match ($application->status) {
                'approved'          => ['Goedgekeurd', 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
                'rejected'          => ['Afgewezen', 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'],
                'changes_requested' => ['Aanpassing gevraagd', 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'],
                default             => ['In beoordeling', 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300'],
            };
        @endphp
        <a href="{{ route('student.stage.show', $application) }}" wire:navigate
           class="flex items-center justify-between gap-4 rounded-xl border border-neutral-200 bg-white px-5 py-4 transition hover:border-neutral-300 hover:shadow-sm dark:border-neutral-800 dark:bg-neutral-900 dark:hover:border-neutral-700">
            <div class="flex items-center gap-4">
                <span class="grid size-11 shrink-0 place-items-center rounded-lg bg-neutral-100 text-[#E2231A] dark:bg-neutral-800">
                    <flux:icon name="briefcase" class="size-5" />
                </span>
                <div>
                    <p class="font-semibold">{{ $application->company?->name ?? 'Onbekend bedrijf' }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $application->position_title ?? 'Stage' }}
                        @if ($application->submitted_at)
                            · ingediend {{ $application->submitted_at->locale('nl')->translatedFormat('j M Y') }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="shrink-0 rounded-full px-3 py-1 text-xs font-medium {{ $statusPill[1] }}">{{ $statusPill[0] }}</span>
                <flux:icon name="chevron-right" class="size-5 text-neutral-300 dark:text-neutral-600" />
            </div>
        </a>
    @empty
        {{-- Geen aanvraag: hier vraagt de student zijn stage aan --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:icon name="briefcase" class="mx-auto size-10 text-neutral-300 dark:text-neutral-600" />
            <flux:heading size="lg" class="mt-3">Nog geen stage aangevraagd</flux:heading>
            <flux:subheading class="mt-1">
                Dien je stageaanvraag in via de knop hierboven: kies een bedrijf, periode en je voorgestelde mentor.
            </flux:subheading>
        </div>
    @endforelse
</div>
