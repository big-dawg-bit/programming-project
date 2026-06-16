<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- Filterpills + nieuwe weeklog --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            @foreach (array_keys(\App\Livewire\Weeklogs\WeeklogList::FILTERS) as $pill)
                <button type="button" wire:click="$set('filter', '{{ $pill }}')"
                    @class([
                        'rounded-full px-4 py-1.5 text-sm font-medium transition',
                        'bg-[#E2231A] text-white' => $filter === $pill,
                        'bg-white text-neutral-600 border border-neutral-200 hover:bg-neutral-50' => $filter !== $pill,
                    ])>
                    {{ ucfirst($pill) }}
                </button>
            @endforeach
        </div>

        @if ($stage)
            <flux:button variant="primary" icon="plus" wire:click="$set('showForm', true)">
                Nieuwe weeklog
            </flux:button>
        @endif
    </div>

    @if (session('weeklog-saved'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('weeklog-saved') }}
        </div>
    @endif

    {{-- Invulformulier --}}
    @if ($stage && $showForm)
        <form wire:submit="save" class="space-y-4 rounded-xl border border-neutral-200 bg-white p-6">
            <flux:heading size="lg">Nieuwe weeklog</flux:heading>

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
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900">
            <p class="font-medium">Geen stage gevonden</p>
            <p class="mt-1 text-sm">
                Draai eerst de seeders:
                <code>php artisan migrate:fresh --seed</code> en daarna
                <code>php artisan db:seed --class=StageSeeder</code>.
            </p>
        </div>
    @elseif ($weeklogs->isEmpty())
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center">
            <flux:heading size="lg">Geen weeklogs gevonden</flux:heading>
            <flux:subheading class="mt-1">
                @if ($filter === 'alle')
                    Klik op "Nieuwe weeklog" om je eerste week toe te voegen.
                @else
                    Geen weeklogs met status "{{ $filter }}".
                @endif
            </flux:subheading>
        </div>
    @else
        <div class="flex flex-col gap-4">
            @foreach ($weeklogs as $weeklog)
                @php
                    $periode = collect([$weeklog->period_start, $weeklog->period_end])
                        ->filter()
                        ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y'))
                        ->implode(' – ');
                @endphp

                <div class="rounded-xl border border-neutral-200 bg-white p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold">Weeklog week {{ $weeklog->week_number }}</h3>
                            <p class="mt-0.5 text-sm text-neutral-500">{{ $periode ?: 'Geen periode opgegeven' }}</p>
                        </div>
                        <x-status-badge :status="$weeklog->status" />
                    </div>

                    <p class="mt-3 line-clamp-2 text-sm text-neutral-600">{{ $weeklog->content }}</p>

                    {{-- Reacties --}}
                    <button type="button" wire:click="toggleComments({{ $weeklog->id }})"
                        class="mt-3 flex items-center gap-1.5 text-sm text-neutral-400 hover:text-neutral-600">
                        <flux:icon name="chat-bubble-left-right" class="size-4" />
                        {{ $weeklog->comments->count() }} {{ $weeklog->comments->count() === 1 ? 'reactie' : 'reacties' }}
                    </button>

                    @if ($openWeeklogId === $weeklog->id)
                        <div class="mt-3 space-y-3 border-t border-neutral-100 pt-3">
                            @forelse ($weeklog->comments as $comment)
                                <div class="rounded-lg bg-neutral-50 p-3">
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
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
