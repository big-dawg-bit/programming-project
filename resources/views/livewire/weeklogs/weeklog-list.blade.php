<x-layouts.portal title="Weeklogboeken">
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

            <div x-data="{ count: @js(strlen($content)) }" x-on:input="count = $event.target.value.length">
                <flux:textarea
                    wire:model="content"
                    label="Wat heb je deze week gedaan en geleerd?"
                    rows="5"
                    placeholder="Beschrijf je taken, reflectie en eventuele problemen…" />
                <p class="mt-1 text-xs text-neutral-500">
                    <span x-text="count">0</span> tekens (minimaal 5)
                </p>
            </div>

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
        <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-4 py-3 font-medium">Week</th>
                        <th class="px-4 py-3 font-medium">Periode</th>
                        <th class="px-4 py-3 font-medium">Uren</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Reacties</th>
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
                                <flux:badge size="sm"
                                            :color="$weeklog->status === 'approved' ? 'green' : ($weeklog->status === 'rejected' ? 'red' : 'yellow')">
                                    {{ ucfirst($weeklog->status) }}
                                </flux:badge>
                            </td>
                            <td class="px-4 py-3">
                                <flux:button size="sm" variant="ghost" icon="chat-bubble-left-right"
                                    wire:click="toggleComments({{ $weeklog->id }})">
                                    {{ $weeklog->comments->count() }}
                                </flux:button>
                            </td>
                        </tr>

                        {{-- Uitklapbare comment-thread --}}
                        @if ($openWeeklogId === $weeklog->id)
                            <tr class="bg-neutral-50 dark:bg-neutral-900/40">
                                <td colspan="5" class="px-4 py-4">
                                    <div class="space-y-3">
                                        @forelse ($weeklog->comments as $comment)
                                            <div class="rounded-lg border border-neutral-200 bg-white p-3 dark:border-neutral-700 dark:bg-neutral-800">
                                                <div class="text-xs text-neutral-500">
                                                    {{ $comment->author?->name ?? 'Onbekend' }}
                                                    · {{ $comment->created_at?->diffForHumans() }}
                                                </div>
                                                <div class="mt-1 text-sm">{{ $comment->comment }}</div>
                                            </div>
                                        @empty
                                            <p class="text-sm text-neutral-500">Nog geen reacties.</p>
                                        @endforelse

                                        <form wire:submit="addComment({{ $weeklog->id }})"
                                            class="flex flex-col gap-2 sm:flex-row sm:items-start">
                                            <div class="flex-1">
                                                <flux:textarea wire:model="newComment" rows="2"
                                                    placeholder="Schrijf een reactie…" />
                                            </div>
                                            <flux:button type="submit" variant="primary">Reageren</flux:button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

</x-layouts.portal>
