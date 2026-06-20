<div class="mx-auto max-w-4xl px-4 py-8">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">Eindbeoordeling</h1>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            Definitieve beoordeling voor {{ $stage->student?->user?->name ?? 'de student' }} — zelfevaluatie en mentorevaluatie staan ter referentie.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="submit" class="space-y-4">
        @foreach ($competencies as $competency)
            @php
                $studentScore = $studentEval?->scores->firstWhere('competency_id', $competency->id)?->score;
                $mentorScore = $mentorEval?->scores->firstWhere('competency_id', $competency->id)?->score;
            @endphp
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="mb-3 flex items-start justify-between gap-4">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wide text-[#E2231A]">{{ $competency->code }}</span>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $competency->title }}</p>
                    </div>
                    <span class="shrink-0 text-xs text-neutral-400">gewicht {{ $competency->weight }}</span>
                </div>

                <div class="mb-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg bg-neutral-50 px-3 py-2 dark:bg-neutral-800/50">
                        <span class="block text-xs text-neutral-500">Zelfevaluatie</span>
                        <span class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $studentScore ?? '—' }}</span>
                    </div>
                    <div class="rounded-lg bg-neutral-50 px-3 py-2 dark:bg-neutral-800/50">
                        <span class="block text-xs text-neutral-500">Mentor</span>
                        <span class="font-semibold text-neutral-800 dark:text-neutral-200">{{ $mentorScore ?? '—' }}</span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-[8rem_1fr]">
                    <div>
                        <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400">Definitief (/20)</label>
                        <input type="number" min="0" max="20" step="0.5" wire:model="scores.{{ $competency->id }}"
                               class="mt-1 w-full rounded-lg border-neutral-300 text-sm dark:border-neutral-700 dark:bg-neutral-800" />
                        @error("scores.{$competency->id}") <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-400">Onderbouwing (optioneel)</label>
                        <textarea rows="2" wire:model="feedback.{{ $competency->id }}"
                                  class="mt-1 w-full rounded-lg border-neutral-300 text-sm dark:border-neutral-700 dark:bg-neutral-800"></textarea>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('docent.evaluaties') }}" wire:navigate class="text-sm font-medium text-neutral-500 hover:underline">Annuleren</a>
            <button type="submit" class="rounded-lg bg-[#E2231A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#c41e16]">
                Eindbeoordeling indienen
            </button>
        </div>
    </form>
</div>
