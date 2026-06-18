<div class="flex max-w-3xl flex-col gap-6">
    @php
        $student = $evaluation->stage?->student?->user?->name ?? 'Onbekende student';
        $studie = $evaluation->stage?->student?->study_program ?? '';
        $bedrijf = $evaluation->stage?->company?->name ?? '';
    @endphp

    {{-- Banner: reeds ingediend --}}
    <div class="flex items-center justify-between gap-4 rounded-xl border border-amber-300 bg-amber-50 p-4">
        <div>
            <p class="font-medium text-amber-900">
                Reeds ingediend op {{ $evaluation->submitted_at ? \Carbon\Carbon::parse($evaluation->submitted_at)->locale('nl')->translatedFormat('j F Y') : '—' }}
            </p>
            <p class="text-sm text-amber-700">Deze evaluatie is niet meer bewerkbaar.</p>
        </div>
    </div>

    {{-- Studentkaart --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <h2 class="text-lg font-semibold">{{ $student }}</h2>
        <p class="text-sm text-neutral-500">
            {{ $bedrijf }}@if ($bedrijf && $studie) • @endif{{ $studie }}
        </p>
    </div>

    {{-- Competenties (alleen-lezen) --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <h3 class="font-semibold">Competenties beoordeling</h3>

        <div class="mt-4 divide-y divide-neutral-100">
            @foreach ($evaluation->scores as $score)
                <div wire:key="score-{{ $score->id }}" class="flex items-center justify-between gap-4 py-4">
                    <span class="text-sm font-medium text-neutral-800">{{ $score->competency?->title ?? '—' }}</span>
                    <div class="flex shrink-0 gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <span @class([
                                'grid size-9 place-items-center rounded-full border text-sm font-medium',
                                'border-[#E2231A] bg-[#E2231A] text-white' => (int) $score->score === $i,
                                'border-neutral-200 text-neutral-400' => (int) $score->score !== $i,
                            ])>
                                {{ $i }}
                            </span>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Algemene feedback --}}
    @if ($evaluation->general_feedback)
        <div class="rounded-xl border border-neutral-200 bg-white p-5">
            <h3 class="font-semibold">Algemene feedback</h3>
            <p class="mt-2 whitespace-pre-line text-sm text-neutral-700">{{ $evaluation->general_feedback }}</p>
        </div>
    @endif

    {{-- Aanbevelingen --}}
    @if ($evaluation->recommendations)
        <div class="rounded-xl border border-neutral-200 bg-white p-5">
            <h3 class="font-semibold">Aanbevelingen</h3>
            <p class="mt-2 whitespace-pre-line text-sm text-neutral-700">{{ $evaluation->recommendations }}</p>
        </div>
    @endif
</div>
