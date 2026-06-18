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
        <h3 class="font-semibold">Competenties beoordelen</h3>
        <p class="mt-1 text-sm text-neutral-500">Geef een score van 1 tot 5 voor elke competentie.</p>

        <div class="mt-4 divide-y divide-neutral-100">
            @foreach ($competencies as $competency)
                <div wire:key="comp-{{ $competency->id }}" class="flex items-center justify-between gap-4 py-4">
                    <span class="text-sm font-medium text-neutral-800">{{ $competency->title }}</span>
                    <div class="flex shrink-0 gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="setScore({{ $competency->id }}, {{ $i }})"
                                @class([
                                    'grid size-9 place-items-center rounded-full border text-sm font-medium transition',
                                    'border-[#E2231A] bg-[#E2231A] text-white' => ($scores[$competency->id] ?? null) === $i,
                                    'border-neutral-300 text-neutral-600 hover:border-[#E2231A] hover:text-[#E2231A]' => ($scores[$competency->id] ?? null) !== $i,
                                ])>
                                {{ $i }}
                            </button>
                        @endfor
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
