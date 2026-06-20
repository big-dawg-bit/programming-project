<div class="mx-auto flex max-w-3xl flex-col gap-6">
    @php
        $bedrijf = $stage->company?->name ?? '';
    @endphp

    {{-- Kop --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
        <h2 class="text-lg font-semibold">{{ $typeLabel }}</h2>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
            Beoordeel jezelf op elke competentie en licht je score kort toe.@if ($bedrijf) · {{ $bedrijf }}@endif
        </p>
    </div>

    {{-- Competenties --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <h3 class="font-semibold">Competenties</h3>
            <p class="text-xs text-neutral-500 dark:text-neutral-400">Score 0-20 (Belgisch) — 10 = voldoende, 14 = goed, 18 = uitstekend</p>
        </div>

        <div class="mt-4 divide-y divide-neutral-100 dark:divide-neutral-800">
            @foreach ($competencies as $competency)
                <div wire:key="comp-{{ $competency->id }}" class="py-4">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-neutral-800 dark:text-neutral-100">
                                @if ($competency->code)<span class="text-neutral-400">{{ $competency->code }}.</span> @endif{{ $competency->title }}
                            </p>
                            @if ($competency->description)
                                <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">{{ $competency->description }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <input type="number" min="0" max="20" step="0.5"
                                   wire:model="scores.{{ $competency->id }}"
                                   class="w-20 rounded-lg border border-neutral-300 px-3 py-2 text-center text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none dark:border-neutral-700 dark:bg-neutral-800" />
                            <span class="text-sm text-neutral-400">/20</span>
                        </div>
                    </div>

                    {{-- Studentenbeschrijving --}}
                    <textarea wire:model="descriptions.{{ $competency->id }}" rows="2"
                              placeholder="Beschrijf kort waarom je jezelf deze score geeft..."
                              class="mt-3 w-full resize-none rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none dark:border-neutral-700 dark:bg-neutral-800"></textarea>
                </div>
            @endforeach
        </div>
        @error('scores.*')
        <p class="mt-2 text-sm text-[#E2231A]">{{ $message }}</p>
        @enderror
    </div>

    {{-- Acties --}}
    <div class="flex gap-3">
        <button type="button" wire:click="saveDraft"
                class="flex-1 rounded-lg border border-neutral-300 px-4 py-2.5 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-200">
            Opslaan als concept
        </button>
        <button type="button" wire:click="submit"
                class="flex-1 rounded-lg bg-[#E2231A] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#c41e16]">
            Indienen
        </button>
    </div>
</div>
