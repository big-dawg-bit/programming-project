@props(['application'])

@php
    $statusMap = [
        'submitted' => ['Ingediend', 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300'],
        'approved' => ['Goedgekeurd', 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
        'rejected' => ['Afgewezen', 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'],
        'changes_requested' => ['Aanpassing gevraagd', 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'],
    ];
    [$statusLabel, $statusKleur] = $statusMap[$application->status] ?? [ucfirst((string) $application->status), 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300'];
    $datum = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y') : null;
    $periode = collect([$application->start_date, $application->end_date])->filter()->map($datum)->implode(' – ');
@endphp

<div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
    <div class="flex items-center justify-between border-b border-neutral-100 px-6 py-4 dark:border-neutral-800">
        <div class="flex items-center gap-2">
            <flux:icon name="document-text" class="size-5 text-[#E2231A]" />
            <h3 class="font-semibold">Stageaanvraag</h3>
        </div>
        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusKleur }}">{{ $statusLabel }}</span>
    </div>

    <dl class="divide-y divide-neutral-100 px-6 text-sm dark:divide-neutral-800">
        @foreach ([
            'Bedrijf' => $application->company?->name,
            'Functie' => $application->position_title,
            'Periode' => $periode ?: null,
            'Voorgestelde mentor' => $application->proposed_mentor_name,
        ] as $label => $waarde)
            <div class="flex items-start justify-between gap-4 py-3">
                <dt class="text-neutral-500 dark:text-neutral-400">{{ $label }}</dt>
                <dd class="text-right font-medium {{ $waarde ? 'text-neutral-900 dark:text-neutral-100' : 'text-neutral-300 dark:text-neutral-600' }}">{{ $waarde ?: '—' }}</dd>
            </div>
        @endforeach
    </dl>

    @if ($application->description)
        <div class="border-t border-neutral-100 px-6 py-4 dark:border-neutral-800">
            <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Beschrijving</p>
            <p class="whitespace-pre-line text-sm leading-relaxed text-neutral-700 dark:text-neutral-300">{{ $application->description }}</p>
        </div>
    @endif
</div>
