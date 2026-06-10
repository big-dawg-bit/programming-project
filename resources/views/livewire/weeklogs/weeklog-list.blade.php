<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Weeklogboeken</flux:heading>
            @if ($stage)
                <flux:subheading>
                    Stage van {{ $stage->student?->user?->name ?? 'onbekende student' }}
                </flux:subheading>
            @endif
        </div>
    </div>

    @if (! $stage)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-200">
            <p class="font-medium">Geen stage gevonden</p>
            <p class="mt-1 text-sm">
                Draai eerst de seeders:
                <code>php artisan migrate:fresh --seed</code> en daarna
                <code>php artisan db:seed --class=StageSeeder</code>.
            </p>
        </div>
    @elseif ($weeklogs->isEmpty())
        <div class="rounded-xl border border-neutral-200 p-10 text-center dark:border-neutral-700">
            <flux:heading size="lg">Nog geen logboeken</flux:heading>
            <flux:subheading class="mt-1">
                Er zijn nog geen weeklogboeken voor deze stage. (Het formulier volgt in stap 3.)
            </flux:subheading>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 font-medium">Week</th>
                        <th class="px-4 py-3 font-medium">Periode</th>
                        <th class="px-4 py-3 font-medium">Uren</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach ($weeklogs as $weeklog)
                        <tr>
                            <td class="px-4 py-3 font-medium">Week {{ $weeklog->week_number }}</td>
                            <td class="px-4 py-3">
                                {{ $weeklog->period_start ?? '—' }} – {{ $weeklog->period_end ?? '—' }}
                            </td>
                            <td class="px-4 py-3">{{ $weeklog->hours_worked ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <flux:badge size="sm">{{ ucfirst($weeklog->status) }}</flux:badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
