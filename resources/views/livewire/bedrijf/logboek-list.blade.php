<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- Kop --}}
    <div>
        <flux:heading size="xl">Logboeken</flux:heading>
        <flux:subheading>Alle weeklogs van onze stagiairs (alleen-lezen).</flux:subheading>
    </div>

    @if (! $company)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300">
            <p class="font-medium">Geen bedrijf gekoppeld</p>
        </div>
    @elseif ($weeklogs->isEmpty())
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:heading size="lg">Geen logboeken</flux:heading>
            <flux:subheading class="mt-1">Er zijn nog geen weeklogs ingediend.</flux:subheading>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <table class="w-full text-sm">
                <thead class="border-b border-neutral-200 text-xs font-medium text-neutral-500 dark:border-neutral-800 dark:text-neutral-400">
                <tr>
                    <th class="px-4 py-3 text-left">Student</th>
                    <th class="px-4 py-3 text-left">Week</th>
                    <th class="px-4 py-3 text-left">Periode</th>
                    <th class="px-4 py-3 text-left">Ingediend op</th>
                    <th class="px-4 py-3 text-left">Status</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @foreach ($weeklogs as $weeklog)
                    @php
                        $periode = collect([$weeklog->period_start, $weeklog->period_end])
                            ->filter()
                            ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y'))
                            ->implode(' - ');
                        $ingediend = $weeklog->submitted_at
                            ? \Illuminate\Support\Carbon::parse($weeklog->submitted_at)->locale('nl')->translatedFormat('j M Y')
                            : '—';
                    @endphp
                    <tr wire:key="{{ $weeklog->id }}" class="hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                        <td class="px-4 py-3 font-medium">{{ $weeklog->stage?->student?->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3">Week {{ $weeklog->week_number }}</td>
                        <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $periode ?: '—' }}</td>
                        <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $ingediend }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$weeklog->status" /></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
