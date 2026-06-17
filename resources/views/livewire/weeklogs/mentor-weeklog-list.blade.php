<div class="mx-auto flex max-w-5xl flex-col gap-6">
    <div>
        <flux:heading size="xl">Weeklogs van je stagiair</flux:heading>
        @if ($stage)
            <flux:subheading>{{ $stage->student?->user?->name ?? 'Onbekende student' }}</flux:subheading>
        @endif
    </div>

    {{-- Filterpills --}}
    <div class="flex flex-wrap gap-2">
        @foreach (array_keys(\App\Livewire\Weeklogs\MentorWeeklogList::FILTERS) as $pill)
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

    @if (session('weeklog-saved'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('weeklog-saved') }}
        </div>
    @endif

    {{-- Lijst --}}
    @if (! $stage)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900">
            <p class="font-medium">Geen stagiair gevonden</p>
            <p class="mt-1 text-sm">Er is nog geen stage aan jou als mentor gekoppeld.</p>
        </div>
    @elseif ($weeklogs->isEmpty())
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center">
            <flux:heading size="lg">Geen weeklogs gevonden</flux:heading>
            <flux:subheading class="mt-1">Er zijn nog geen weeklogs met deze status.</flux:subheading>
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

                <div wire:key="{{ $weeklog->id }}" class="rounded-xl border border-neutral-200 bg-white p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold">Weeklog week {{ $weeklog->week_number }}</h3>
                            <p class="mt-0.5 text-sm text-neutral-500">{{ $periode ?: 'Geen periode opgegeven' }}</p>
                        </div>
                        <x-status-badge :status="$weeklog->status" />
                    </div>

                    <p class="mt-3 text-sm text-neutral-600">{{ $weeklog->content }}</p>

                    {{-- Beoordelingsknoppen --}}
                    <div class="mt-4 flex flex-wrap gap-3 border-t border-neutral-100 pt-4">
                        <flux:button size="sm" variant="primary" wire:click="approve({{ $weeklog->id }})">
                            Goedkeuren
                        </flux:button>
                        <flux:button size="sm" variant="ghost" wire:click="requestChanges({{ $weeklog->id }})">
                            Aanpassing vragen
                        </flux:button>
                    </div>

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
                                    @error('newComment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
