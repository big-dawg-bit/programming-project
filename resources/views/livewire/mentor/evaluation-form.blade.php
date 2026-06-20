<div class="flex max-w-3xl flex-col gap-6">
    @php
        $student = $stage->student?->user?->name ?? 'Onbekende student';
        $studie = $stage->student?->study_program ?? '';
        $bedrijf = $stage->company?->name ?? '';
    @endphp

    {{-- Studentkaart --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <h2 class="text-lg font-semibold">{{ $student }}</h2>
        <p class="text-sm text-neutral-500">
            {{ $bedrijf }}@if ($bedrijf && $studie) • @endif{{ $studie }}
        </p>
    </div>

    {{-- Competenties --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h3 class="font-semibold">Competenties beoordelen</h3>
            <x-rubriek-modal :competencies="$competencies" />
        </div>
        <p class="mt-1 text-sm text-neutral-500">Geef een score van 0 tot 20 voor elke competentie.</p>

        <div class="mt-4 divide-y divide-neutral-100">
            @foreach ($competencies as $competency)
                <div wire:key="comp-{{ $competency->id }}" class="flex items-center justify-between gap-4 py-4">
                    <span class="text-sm font-medium text-neutral-800">{{ $competency->title }}</span>
                    <div class="flex shrink-0 items-center gap-2">
                        <input type="number" min="0" max="20" step="0.5"
                               wire:model="scores.{{ $competency->id }}"
                               class="w-20 rounded-lg border border-neutral-300 px-3 py-2 text-center text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none" />
                        <span class="text-sm text-neutral-400">/20</span>
                    </div>
                </div>
            @endforeach
        </div>
        @error('scores.*')
        <p class="mt-2 text-sm text-[#E2231A]">{{ $message }}</p>
        @enderror
    </div>

    {{-- Algemene feedback --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <h3 class="font-semibold">Algemene feedback</h3>
        <textarea wire:model="generalFeedback" rows="4"
                  placeholder="Beschrijf de algemene prestaties en ontwikkeling van de student..."
                  class="mt-3 w-full resize-none rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none"></textarea>
    </div>

    {{-- Aanbevelingen --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <h3 class="font-semibold">Aanbevelingen</h3>
        <textarea wire:model="recommendations" rows="4"
                  placeholder="Geef aanbevelingen voor de verdere ontwikkeling..."
                  class="mt-3 w-full resize-none rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none"></textarea>
    </div>

    {{-- Acties --}}
    <div class="flex gap-3">
        <button type="button" wire:click="saveDraft"
                class="flex-1 rounded-lg border border-neutral-300 px-4 py-2.5 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50">
            Opslaan als concept
        </button>
        <button type="button" wire:click="submit"
                class="flex-1 rounded-lg bg-[#E2231A] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#c41e16]">
            Indienen
        </button>
    </div>
</div>
