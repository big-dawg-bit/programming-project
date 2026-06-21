<div class="flex max-w-5xl flex-col gap-6">

    @if (session('weeklog-saved'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('weeklog-saved') }}
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-2">
        @foreach (array_keys(\App\Livewire\Weeklogs\MentorWeeklogList::FILTERS) as $pill)
            <button type="button" wire:click="$set('filter', '{{ $pill }}')"
                @class([
                    'inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-medium transition',
                    'bg-[#E2231A] text-white' => $filter === $pill,
                    'bg-white text-neutral-600 border border-neutral-200 hover:bg-neutral-50' => $filter !== $pill,
                ])>
                {{ ucfirst($pill) }}
                @if ($pill === 'te beoordelen' && $teBeoordelenCount > 0)
                    <span @class([
                        'inline-flex size-5 items-center justify-center rounded-full text-xs font-bold',
                        'bg-white text-[#E2231A]' => $filter === $pill,
                        'bg-[#E2231A] text-white' => $filter !== $pill,
                    ])>{{ $teBeoordelenCount }}</span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Lijst --}}
    @if (! $hasStages)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900">
            <p class="font-medium">Geen stagiair gevonden</p>
            <p class="mt-1 text-sm">Er is nog geen stage aan jou als mentor gekoppeld.</p>
        </div>
    @elseif ($weeklogs->isEmpty())
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center">
            <flux:heading size="lg">Geen weeklogs gevonden</flux:heading>
            <flux:subheading class="mt-1">Er zijn geen weeklogs met deze status.</flux:subheading>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white">
            <table class="w-full text-sm">
                <thead class="border-b border-neutral-200 text-xs font-medium text-neutral-500">
                <tr>
                    <th class="px-4 py-3 text-left">Student</th>
                    <th class="px-4 py-3 text-left">Week</th>
                    <th class="px-4 py-3 text-left">Datumrange</th>
                    <th class="px-4 py-3 text-left">Ingediend op</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                @foreach ($weeklogs as $weeklog)
                    @php
                        $periode = collect([$weeklog->period_start, $weeklog->period_end])
                            ->filter()
                            ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y'))
                            ->implode(' - ');
                        $ingediend = $weeklog->submitted_at
                            ? \Illuminate\Support\Carbon::parse($weeklog->submitted_at)->locale('nl')->translatedFormat('j M Y')
                            : '—';
                        $teBeoordelen = $weeklog->status === 'ingediend';
                    @endphp
                    <tr wire:key="{{ $weeklog->id }}" class="hover:bg-neutral-50">
                        <td class="px-4 py-3 font-medium">{{ $weeklog->stage?->student?->user?->name ?? '—' }}</td>
                        <td class="px-4 py-3">Week {{ $weeklog->week_number }}</td>
                        <td class="px-4 py-3 text-neutral-600">{{ $periode ?: '—' }}</td>
                        <td class="px-4 py-3 text-neutral-600">{{ $ingediend }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$weeklog->status" /></td>
                        <td class="px-4 py-3 text-right">
                            @if ($teBeoordelen)
                                <flux:button size="sm" variant="primary" wire:click="toggleComments({{ $weeklog->id }})">
                                    Reviewen
                                </flux:button>
                            @else
                                <flux:button size="sm" variant="outline" wire:click="toggleComments({{ $weeklog->id }})">
                                    Bekijken
                                </flux:button>
                            @endif
                        </td>
                    </tr>

                    @if ($openWeeklogId === $weeklog->id)
                        <tr wire:key="detail-{{ $weeklog->id }}">
                            <td colspan="6" class="bg-neutral-50 px-4 py-4">
                                <p class="text-sm text-neutral-700">{{ $weeklog->content }}</p>

                                <div class="mt-3 flex flex-wrap gap-3">
                                    <flux:button size="sm" variant="primary" wire:click="approve({{ $weeklog->id }})">Goedkeuren</flux:button>
                                    <flux:button size="sm" variant="ghost" wire:click="requestChanges({{ $weeklog->id }})">Aanpassing vragen</flux:button>
                                </div>

                                <div class="mt-4 space-y-3 border-t border-neutral-200 pt-3">
                                    @forelse ($weeklog->comments as $comment)
                                        <div class="rounded-lg bg-white p-3">
                                            <div class="text-xs text-neutral-500">
                                                {{ $comment->author?->name ?? 'Onbekend' }} · {{ $comment->created_at?->diffForHumans() }}
                                            </div>
                                            <div class="mt-1 text-sm">{{ $comment->comment }}</div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-neutral-500">Nog geen reacties.</p>
                                    @endforelse

                                    <form wire:submit="addComment({{ $weeklog->id }})" class="flex flex-col gap-2 sm:flex-row sm:items-start">
                                        <div class="flex-1">
                                            <flux:textarea wire:model="newComment" rows="2" placeholder="Schrijf een reactie…" />
                                            @error('newComment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
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
