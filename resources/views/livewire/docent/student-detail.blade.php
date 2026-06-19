<div class="mx-auto flex max-w-5xl flex-col gap-6">
    @if (! $stage)
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:heading size="lg">Student niet gevonden</flux:heading>
            <flux:subheading class="mt-1">Deze student is niet aan jou toegewezen.</flux:subheading>
            <flux:button href="{{ route('docent.studenten') }}" wire:navigate variant="primary" class="mt-5">Terug naar studenten</flux:button>
        </div>
    @else
        @php
            $student = $stage->student;
            $ingediend = $stage->weeklogs->whereNotNull('submitted_at')->count();
            $evaluaties = $stage->evaluations->where('status', 'submitted');
            $initialen = collect(explode(' ', $naam))->filter()->map(fn ($d) => mb_substr($d, 0, 1))->take(2)->implode('');
        @endphp

        {{-- Profielkaart --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-start gap-4">
                <span class="grid size-16 shrink-0 place-items-center rounded-full bg-neutral-100 text-lg font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                    {{ $initialen }}
                </span>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-xl font-bold">{{ $naam }}</h2>
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">
                            {{ ucfirst($stage->status ?? 'active') === 'Active' ? 'Bezig' : ucfirst($stage->status) }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $student?->study_program ?? 'Onbekende opleiding' }} • {{ $stage->company?->name ?? '—' }}
                        @if ($stage->mentor?->user) • Mentor: {{ $stage->mentor->user->name }} @endif
                    </p>
                </div>
            </div>

            {{-- Statistiek --}}
            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                @foreach ([
                    ['Weeklogs ingediend', $totaalWeken ? "{$ingediend}/{$totaalWeken}" : (string) $ingediend, ''],
                    ['Huidige week', $huidigeWeek ? (string) $huidigeWeek : '—', ''],
                    ['Evaluaties', (string) $evaluaties->count(), 'text-green-600 dark:text-green-400'],
                ] as [$label, $waarde, $kleur])
                    <div class="rounded-lg bg-neutral-50 p-4 dark:bg-neutral-800/50">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $label }}</p>
                        <p class="mt-1 text-2xl font-bold {{ $kleur }}">{{ $waarde }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-neutral-200 dark:border-neutral-800">
            <nav class="-mb-px flex gap-8">
                @foreach (['weeklogs' => 'Weeklogs', 'evaluaties' => 'Evaluaties', 'rapport' => 'Rapport'] as $key => $label)
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

        {{-- Weeklogs --}}
        @if ($tab === 'weeklogs')
            <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                <table class="w-full min-w-[520px] text-left text-sm">
                    <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-neutral-800 dark:bg-neutral-800/50 dark:text-neutral-400">
                        <tr>
                            <th class="px-5 py-3 font-medium">Week</th>
                            <th class="px-5 py-3 font-medium">Periode</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @forelse ($stage->weeklogs->whereNotNull('submitted_at')->sortByDesc('week_number') as $w)
                            @php
                                $periode = collect([$w->period_start, $w->period_end])->filter()
                                    ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y'))->implode(' – ');
                            @endphp
                            <tr>
                                <td class="px-5 py-4 font-medium">Week {{ $w->week_number }}</td>
                                <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $periode ?: '—' }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-600 dark:bg-blue-950/50 dark:text-blue-300">Ingediend</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-10 text-center text-sm text-neutral-500 dark:text-neutral-400">Nog geen weeklogs ingediend.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        {{-- Evaluaties --}}
        @elseif ($tab === 'evaluaties')
            <div class="flex flex-col gap-4">
                @forelse ($evaluaties->sortByDesc('submitted_at') as $eval)
                    <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold">{{ $eval->type === 'final' ? 'Eindevaluatie' : 'Tussentijdse evaluatie' }}</h3>
                            <p class="text-right">
                                <span class="text-2xl font-bold">{{ number_format((float) $eval->overall_score, 1) }}</span>
                                <span class="text-sm text-neutral-400 dark:text-neutral-500"> / 20</span>
                            </p>
                        </div>
                        <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">
                            {{ $eval->submitted_at ? \Illuminate\Support\Carbon::parse($eval->submitted_at)->format('d/m/Y') : '' }}
                        </p>
                    </div>
                @empty
                    <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
                        <flux:heading size="lg">Nog geen evaluaties</flux:heading>
                        <flux:subheading class="mt-1">
                            <a href="{{ route('evaluations.create', $stage) }}" wire:navigate class="font-medium text-[#E2231A] hover:underline">Vul een evaluatie in →</a>
                        </flux:subheading>
                    </div>
                @endforelse
            </div>

        {{-- Rapport --}}
        @else
            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                @php $eind = $evaluaties->where('type', 'final')->first(); @endphp
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium">Eindrapport</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $stage->finalReport ? 'Ingediend door de student' : 'Nog niet ingediend' }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $stage->finalReport ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300' }}">
                        {{ $stage->finalReport ? 'ingediend' : 'ontbreekt' }}
                    </span>
                </div>
                <div class="mt-4 border-t border-neutral-100 pt-4 text-sm dark:border-neutral-800">
                    <span class="text-neutral-500 dark:text-neutral-400">Eindcijfer: </span>
                    @if ($eind)
                        <span class="font-semibold">{{ number_format((float) $eind->overall_score, 1) }} / 20</span>
                    @else
                        <span class="text-neutral-400 dark:text-neutral-500">nog geen eindevaluatie</span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>
