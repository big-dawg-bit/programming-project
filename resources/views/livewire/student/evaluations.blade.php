<div class="mx-auto flex max-w-5xl flex-col gap-6">
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

    @forelse ($evaluations as $evaluation)
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold">
                        {{ $tab === 'eind' ? 'Eindevaluatie' : 'Tussentijdse evaluatie' }}
                    </h3>
                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        {{ optional($evaluation->submitted_at)->format('d/m/Y') ?? '—' }}
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

            {{-- Uitsplitsing per competentie (gewicht uit de snapshot) --}}
            @if ($evaluation->scores->isNotEmpty())
                <div class="mt-5 overflow-hidden rounded-lg border border-neutral-200 dark:border-neutral-800">
                    <table class="w-full text-sm">
                        <thead class="bg-neutral-50 text-left text-neutral-500 dark:bg-neutral-800/50 dark:text-neutral-400">
                        <tr>
                            <th class="px-4 py-2 font-medium">Competentie</th>
                            <th class="px-4 py-2 font-medium">Gewicht</th>
                            <th class="px-4 py-2 font-medium">Score</th>
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
            <flux:heading size="lg">
                {{ $tab === 'eind' ? 'Nog geen eindevaluatie' : 'Nog geen tussentijdse evaluatie' }}
            </flux:heading>
            <flux:subheading class="mt-1">
                Je evaluatie verschijnt hier zodra je mentor en docent ze hebben ingediend.
            </flux:subheading>
        </div>
    @endforelse
</div>
