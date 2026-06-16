<div class="mx-auto flex max-w-3xl flex-col gap-6">

    {{-- Kop --}}
    <div>
        <h1 class="text-2xl font-bold text-neutral-900">Evaluatie invullen</h1>
        <p class="mt-1 text-sm text-neutral-500">
            Stage van
            {{ $stage->student?->user?->name ?? 'student' }}
            @if ($stage->company)
                · {{ $stage->company->name }}
            @endif
        </p>
    </div>

    {{-- Bevestiging na opslaan --}}
    @if (session('success'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Reeds ingediende evaluaties: tussentijds + eind naast elkaar --}}
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach (['mid-term' => 'Tussentijds', 'final' => 'Eind'] as $key => $label)
            @php $bestaat = $bestaande->firstWhere('type', $key); @endphp
            <div class="rounded-xl border border-neutral-200 bg-white p-4">
                <p class="text-sm font-medium text-neutral-500">{{ $label }}</p>
                @if ($bestaat)
                    <p class="mt-1 text-2xl font-bold text-neutral-900">
                        {{ number_format((float) $bestaat->overall_score, 1) }}
                    </p>
                    <p class="text-xs text-green-600">Ingediend op
                        {{ optional($bestaat->submitted_at)->format('d/m/Y') }}</p>
                @else
                    <p class="mt-1 text-sm text-neutral-400">Nog niet ingediend</p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Het formulier --}}
    <form wire:submit="submit" class="flex flex-col gap-6 rounded-xl border border-neutral-200 bg-white p-6">

        {{-- Type-keuze: tussentijds of eind --}}
        <div>
            <span class="mb-2 block text-sm font-medium text-neutral-700">Type evaluatie</span>
            <div class="flex gap-2">
                @foreach (['mid-term' => 'Tussentijds', 'final' => 'Eind'] as $key => $label)
                    <label @class([
                        'flex-1 cursor-pointer rounded-lg border px-4 py-2 text-center text-sm font-medium transition',
                        'border-[#E2231A] bg-[#E2231A]/5 text-[#E2231A]' => $type === $key,
                        'border-neutral-200 text-neutral-600 hover:bg-neutral-50' => $type !== $key,
                    ])>
                        <input type="radio" class="sr-only" wire:model.live="type" value="{{ $key }}">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('type') <p class="mt-1.5 text-sm text-[#E2231A]">{{ $message }}</p> @enderror
        </div>

        {{-- Score per competentie --}}
        <div class="flex flex-col gap-4">
            @foreach ($competencies as $competency)
                <div wire:key="comp-{{ $competency->id }}" class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-neutral-800">{{ $competency->title }}</p>
                        <p class="text-xs text-neutral-400">Gewicht: {{ $competency->weight }}</p>
                    </div>
                    <div class="w-28">
                        <input
                            type="number"
                            min="0"
                            max="100"
                            step="1"
                            wire:model="scores.{{ $competency->id }}"
                            placeholder="0-100"
                            class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-right text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none"
                        >
                        @error('scores.'.$competency->id)
                            <p class="mt-1 text-xs text-[#E2231A]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Indien-knop --}}
        <div class="flex justify-end border-t border-neutral-100 pt-4">
            <button
                type="submit"
                class="rounded-lg bg-[#E2231A] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#c41e16] focus:ring-2 focus:ring-[#E2231A]/40 focus:outline-none"
            >
                Evaluatie indienen
            </button>
        </div>
    </form>
</div>
