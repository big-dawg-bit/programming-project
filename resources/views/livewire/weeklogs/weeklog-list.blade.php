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

        @if ($stage)
            <flux:button variant="primary" icon="plus" wire:click="$set('showForm', true)">
                Nieuw logboek
            </flux:button>
        @endif
    </div>

    @if (session('weeklog-saved'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-700 dark:bg-green-950 dark:text-green-200">
            {{ session('weeklog-saved') }}
        </div>
    @endif

    {{-- Invulformulier --}}
    @if ($stage && $showForm)
        <form wire:submit="save" class="space-y-4 rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
            <flux:heading size="lg">Nieuw weeklogboek</flux:heading>

            <div class="grid gap-4 sm:grid-cols-3">
                <flux:input type="number" wire:model="week_number" label="Weeknummer" min="1" max="52" />
                <flux:input type="date" wire:model="period_start" label="Van" />
                <flux:input type="date" wire:model="period_end" label="Tot" />
            </div>

            <flux:textarea
                wire:model="content"
                label="Wat heb je deze week gedaan en geleerd?"
                rows="5"
                placeholder="Beschrijf je taken, reflectie en eventuele problemen…" />

            <flux:input type="number" step="0.5" wire:model="hours_worked" label="Gewerkte uren" min="0" max="80" />

            <div class="flex gap-3">
                <flux:button type="submit" variant="primary">Opslaan</flux:button>
                <flux:button type="button" variant="ghost" wire:click="$set('showForm', false)">Annuleren</flux:button>
            </div>
        </form>
    @endif

    {{-- Lijst --}}
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
                Klik op "Nieuw logboek" om je eerste week toe te voegen.
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
