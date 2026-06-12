<x-layouts.portal title="Dashboard">
<div class="mx-auto flex max-w-5xl flex-col gap-6">
    @if (! $stage)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-sm text-amber-900">
            Geen stage gevonden. Draai de seeders (<code>php artisan migrate --seed</code> en
            <code>php artisan db:seed --class=StageSeeder</code>).
        </div>
    @else
        {{-- Welkom --}}
        <div>
            <h2 class="text-2xl font-bold">Welkom {{ $stage->student?->user?->first_name ?? $stage->student?->user?->name ?? 'student' }}</h2>
            <p class="text-sm text-neutral-500">
                Stage bij {{ $stage->company?->name ?? 'onbekend bedrijf' }}
            </p>
        </div>

        {{-- Statistiek-kaarten --}}
        <div class="grid gap-4 sm:grid-cols-3">
            {{-- Ingediende weeklogs --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-5">
                <div class="flex items-center gap-2 text-sm text-neutral-500">
                    <flux:icon name="book-open" class="size-4" /> Ingediende weeklogs
                </div>
                <p class="mt-3 text-3xl font-semibold">{{ $stage->weeklogs_count }}</p>
            </div>

            {{-- Stage week --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-5">
                <div class="flex items-center gap-2 text-sm text-neutral-500">
                    <flux:icon name="clock" class="size-4" /> Stage week
                </div>
                @if ($currentWeek && $totalWeeks)
                    <p class="mt-3 text-3xl font-semibold">{{ $currentWeek }}/{{ $totalWeeks }}</p>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-neutral-100">
                        <div class="h-full rounded-full bg-red-500"
                             style="width: {{ round($currentWeek / $totalWeeks * 100) }}%"></div>
                    </div>
                @else
                    <p class="mt-3 text-3xl font-semibold">—</p>
                @endif
            </div>

            {{-- Eindrapport --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-5">
                <div class="flex items-center gap-2 text-sm text-neutral-500">
                    <flux:icon name="document-text" class="size-4" /> Eindrapport
                </div>
                <p class="mt-3">
                    @if ($stage->finalReport)
                        <span class="rounded-full bg-green-100 px-2.5 py-1 text-sm font-medium text-green-700">Ingediend</span>
                    @else
                        <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-sm font-medium text-neutral-600">Nog niet ingediend</span>
                    @endif
                </p>
                <a href="{{ route('final-report.edit') }}" wire:navigate class="mt-3 inline-block text-sm text-red-600 hover:underline">
                    Naar eindrapport →
                </a>
            </div>
        </div>

        {{-- Recente weeklogs --}}
        <div class="rounded-xl border border-neutral-200 bg-white">
            <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4">
                <h3 class="font-semibold">Recente weeklogs</h3>
                <a href="{{ route('weeklogs.index') }}" wire:navigate class="text-sm text-red-600 hover:underline">
                    Alle weeklogs →
                </a>
            </div>

            @if ($weeklogs->isEmpty())
                <p class="px-5 py-6 text-sm text-neutral-500">Nog geen weeklogs ingediend.</p>
            @else
                <table class="w-full text-left text-sm">
                    <thead class="text-neutral-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Week</th>
                            <th class="px-5 py-3 font-medium">Periode</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach ($weeklogs as $weeklog)
                            <tr>
                                <td class="px-5 py-3 font-medium">Week {{ $weeklog->week_number }}</td>
                                <td class="px-5 py-3 text-neutral-600">
                                    {{ $weeklog->period_start ?? '—' }} – {{ $weeklog->period_end ?? '—' }}
                                </td>
                                <td class="px-5 py-3">
                                    <flux:badge size="sm"
                                                :color="in_array($weeklog->status, ['gevalideerd', 'goedgekeurd']) ? 'green' : ($weeklog->status === 'ingediend' ? 'yellow' : 'zinc')">
                                        {{ ucfirst($weeklog->status) }}
                                    </flux:badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif
</div>

</x-layouts.portal>
