<div class="flex max-w-4xl flex-col gap-6">
    {{-- Tabs --}}
    <div class="border-b border-neutral-200 dark:border-neutral-800">
        <nav class="-mb-px flex gap-8">
            <button type="button" wire:click="$set('tab', 'te doen')"
                @class([
                    'inline-flex items-center gap-2 border-b-2 pb-3 text-sm font-medium transition',
                    'border-[#E2231A] text-neutral-900 dark:text-neutral-100' => $tab === 'te doen',
                    'border-transparent text-neutral-500 hover:text-neutral-700' => $tab !== 'te doen',
                ])>
                Te doen
                @if ($teDoenCount > 0)
                    <span class="inline-flex size-5 items-center justify-center rounded-full bg-[#E2231A] text-xs font-bold text-white">{{ $teDoenCount }}</span>
                @endif
            </button>
            <button type="button" wire:click="$set('tab', 'ingediend')"
                @class([
                    'border-b-2 pb-3 text-sm font-medium transition',
                    'border-[#E2231A] text-neutral-900 dark:text-neutral-100' => $tab === 'ingediend',
                    'border-transparent text-neutral-500 hover:text-neutral-700' => $tab !== 'ingediend',
                ])>
                Ingediend
            </button>
        </nav>
    </div>

    @if (! $stage)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900">
            <p class="font-medium">Geen stagiair gevonden</p>
            <p class="mt-1 text-sm">Er is nog geen stage aan jou als mentor gekoppeld.</p>
        </div>
    @elseif ($tab === 'te doen')
        @php $student = $stage->student?->user?->name ?? 'Onbekende student'; @endphp
        @forelse ($teDoen as $item)
            <div wire:key="todo-{{ $item['type'] }}" class="rounded-xl border border-neutral-200 bg-white p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="font-semibold">{{ $student }}</h3>
                        <p class="text-sm text-neutral-500">{{ $stage->company?->name ?? '—' }}</p>
                        <p class="mt-1 text-sm text-neutral-700">{{ $item['label'] }}</p>
                    </div>
                    <a href="{{ route('mentor.evaluatie.invullen', ['stage' => $stage->id, 'type' => $item['type']]) }}" wire:navigate
                       class="shrink-0 rounded-lg bg-[#E2231A] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#c41e16]">
                        Invullen
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center">
                <flux:heading size="lg">Niets te doen</flux:heading>
                <flux:subheading class="mt-1">Alle evaluaties zijn ingediend.</flux:subheading>
            </div>
        @endforelse
    @else
        @php $student = $stage->student?->user?->name ?? 'Onbekende student'; @endphp
        @forelse ($ingediend as $evaluatie)
            <div wire:key="done-{{ $evaluatie->id }}" class="rounded-xl border border-neutral-200 bg-white p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="font-semibold">{{ $student }}</h3>
                        <p class="text-sm text-neutral-500">{{ $stage->company?->name ?? '—' }}</p>
                        <p class="mt-1 text-sm text-neutral-700">{{ \App\Livewire\Mentor\EvaluationList::TYPES[$evaluatie->type] ?? $evaluatie->type }}</p>
                        <p class="mt-1 text-xs text-neutral-400">
                            Ingediend op {{ $evaluatie->submitted_at?->locale('nl')->translatedFormat('j F Y') ?? '—' }}
                        </p>
                    </div>
                    <a href="{{ route('mentor.evaluatie.bekijken', ['evaluation' => $evaluatie->id]) }}" wire:navigate
                       class="shrink-0 rounded-lg border border-neutral-200 px-4 py-2 text-sm font-medium text-[#E2231A] transition hover:bg-neutral-50">
                        Bekijken
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center">
                <flux:heading size="lg">Nog niets ingediend</flux:heading>
                <flux:subheading class="mt-1">Ingediende evaluaties verschijnen hier.</flux:subheading>
            </div>
        @endforelse
    @endif
</div>
