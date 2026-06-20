<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- Kop --}}
    <div>
        <flux:heading size="xl">Mentor toewijzen</flux:heading>
        <flux:subheading>Koppel een mentor aan elke stagiair.</flux:subheading>
    </div>

    @if (session('bedrijf-status'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
            {{ session('bedrijf-status') }}
        </div>
    @endif

    @if (! $company)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300">
            <p class="font-medium">Geen bedrijf gekoppeld</p>
        </div>
    @else
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
                <h3 class="font-semibold">Onze stagiairs</h3>
            </div>

            @forelse ($stages as $stage)
                <div class="flex items-center justify-between gap-3 border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                    <div>
                        <p class="font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            Mentor: {{ $stage->mentor?->user?->name ?? 'Nog geen mentor' }}
                        </p>
                    </div>
                    <div>
                        <select wire:change="assignMentor({{ $stage->id }}, $event.target.value)"
                                class="rounded-lg border border-neutral-200 bg-white px-3 py-1.5 text-sm dark:border-neutral-700 dark:bg-neutral-800">
                            <option value="">— Kies mentor —</option>
                            @foreach ($mentors as $mentor)
                                <option value="{{ $mentor->id }}" @selected($stage->mentor_id === $mentor->id)>
                                    {{ $mentor->user?->name ?? 'Mentor' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-neutral-500 dark:text-neutral-400">Nog geen stagiairs.</p>
            @endforelse
        </div>
    @endif
</div>
