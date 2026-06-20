<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- Kop met knop naar de zelfevaluatie --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-bold">Mijn evaluaties</h2>
        @if ($stage)
            <a href="{{ route('student.evaluatie.invullen', ['stage' => $stage, 'type' => $tab === 'eind' ? 'final' : 'mid-term']) }}" wire:navigate
               class="rounded-lg bg-[#E2231A] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#c41e16]">
                Zelfevaluatie invullen
            </a>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="border-b border-neutral-200 dark:border-neutral-800">
        <nav class="-mb-px flex gap-8">
            @foreach (['tussentijds' => 'Tussentijds', 'eind' => 'Eind'] as $key => $label)
                <button type="button" wire:click="$set('tab', '{{ $key }}')"
                    @class([
                        'border-b-2 pb-3 text-sm font-medium transition',
                        'border-[#E2231A] text-neutral-900 dark:text-neutral-100' => $tab === $key,
                        'border-transparent text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200' => $tab !== $key,
                    ])>
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    @if ($tab === 'eind')
        {{-- Gecombineerde eindevaluatie: student-, mentor- én docentscore naast elkaar --}}
        @if ($combined['studentEval'] || $combined['mentorEval'] || $combined['docentEval'])
            @if ($combined['finalEval'])
                <div class="flex items-center justify-between rounded-xl border border-[#E2231A]/30 bg-[#E2231A]/5 px-5 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#E2231A]">Definitieve eindbeoordeling</p>
                        <p class="text-sm text-neutral-600 dark:text-neutral-300">Vastgelegd door de docent</p>
                    </div>
                    <span class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ number_format((float) $combined['finalEval']->overall_score, 1) }}<span class="text-base font-medium text-neutral-400">/20</span></span>
                </div>
            @endif

            <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
                    <h3 class="font-semibold">Eindevaluatie</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-neutral-50 text-left text-xs uppercase tracking-wide text-neutral-500 dark:bg-neutral-800/50 dark:text-neutral-400">
                        <tr>
                            <th class="px-4 py-3 font-medium">Criteria</th>
                            <th class="px-4 py-3 font-medium">Studentenbeschrijving</th>
                            <th class="px-4 py-3 font-medium">Feedback mentor</th>
                            <th class="px-4 py-3 text-center font-medium">Score student</th>
                            <th class="px-4 py-3 text-center font-medium">Score mentor</th>
                            <th class="px-4 py-3 text-center font-medium">Score docent</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @foreach ($combined['rows'] as $row)
                            <tr class="align-top">
                                <td class="px-4 py-3 font-medium text-neutral-800 dark:text-neutral-100">
                                    @if ($row['competency']->code)<span class="text-neutral-400">{{ $row['competency']->code }}.</span> @endif{{ $row['competency']->title }}
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-300">{{ $row['studentDescription'] ?? '—' }}</td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-300">{{ $row['mentorFeedback'] ?? '—' }}</td>
                                <td class="px-4 py-3 text-center font-semibold">{{ $row['studentScore'] !== null ? number_format((float) $row['studentScore'], 1).'/20' : '—' }}</td>
                                <td class="px-4 py-3 text-center font-semibold">{{ $row['mentorScore'] !== null ? number_format((float) $row['mentorScore'], 1).'/20' : '—' }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-[#E2231A]">{{ $row['docentScore'] !== null ? number_format((float) $row['docentScore'], 1).'/20' : '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
                <flux:heading size="lg">Nog geen eindevaluatie</flux:heading>
                <flux:subheading class="mt-1">
                    De eindevaluatie verschijnt hier zodra jij en je mentor ze hebben ingediend.
                </flux:subheading>
            </div>
        @endif
    @else
        {{-- Tussentijds: per evaluatie een kaart --}}
        @forelse ($evaluations as $evaluation)
            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold">Tussentijdse evaluatie</h3>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $evaluation->submitted_at ? \Illuminate\Support\Carbon::parse($evaluation->submitted_at)->format('d/m/Y') : '—' }}
                            @if ($evaluation->stage?->company)
                                · {{ $evaluation->stage->company->name }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <p class="text-right">
                            <span class="text-3xl font-bold">{{ number_format((float) $evaluation->overall_score, 1) }}</span>
                            <span class="block text-sm text-neutral-400 dark:text-neutral-500">/20</span>
                        </p>
                        <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">
                            Ingediend
                        </span>
                    </div>
                </div>

                @if ($evaluation->scores->isNotEmpty())
                    <div class="mt-5 overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-800">
                        <table class="w-full text-sm">
                            <thead class="bg-neutral-50 text-left text-neutral-500 dark:bg-neutral-800/50 dark:text-neutral-400">
                            <tr>
                                <th class="px-4 py-2 font-medium">Competentie</th>
                                <th class="px-4 py-2 font-medium">Gewicht</th>
                                <th class="px-4 py-2 font-medium">Score /20</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @foreach ($evaluation->scores as $score)
                                <tr>
                                    <td class="px-4 py-2">
                                        {{ $score->competency?->title ?? '—' }}
                                        @if ($score->feedback)
                                            <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">{{ $score->feedback }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-neutral-500 dark:text-neutral-400">{{ $score->weight_snapshot }}%</td>
                                    <td class="px-4 py-2 font-medium">
                                        {{ $score->score !== null ? number_format((float) $score->score, 1) : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
                <flux:heading size="lg">Nog geen tussentijdse evaluatie</flux:heading>
                <flux:subheading class="mt-1">
                    Je evaluatie verschijnt hier zodra je mentor en docent ze hebben ingediend.
                </flux:subheading>
            </div>
        @endforelse
    @endif
</div>
