<div class="mx-auto flex max-w-5xl flex-col gap-6">
    <div>
        <h2 class="text-lg font-semibold">Evaluaties</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            Bekijk per student de zelfevaluatie en de mentorevaluatie, en geef het officiële eindresultaat.
        </p>
    </div>

    @php
        $klaar = 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300';
        $open  = 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300';
    @endphp

    <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-neutral-800 dark:bg-neutral-800/50 dark:text-neutral-400">
            <tr>
                <th class="px-5 py-3 font-medium">Student</th>
                <th class="px-5 py-3 font-medium">Bedrijf</th>
                <th class="px-5 py-3 font-medium">Zelfevaluatie</th>
                <th class="px-5 py-3 font-medium">Mentor</th>
                <th class="px-5 py-3 font-medium">Officieel resultaat</th>
                <th class="px-5 py-3 font-medium text-right">Actie</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
            @forelse ($stages as $stage)
                @php
                    // Enkel de ingediende EIND-evaluaties, per rol bekeken.
                    $finals     = $stage->evaluations->where('type', 'final')->where('status', 'submitted');
                    $zelfDone   = $finals->firstWhere('evaluator_role', 'student') !== null;
                    $mentorDone = $finals->firstWhere('evaluator_role', 'mentor') !== null;
                    $eind       = $stage->finalEvaluation; // de bindende docentbeoordeling
                @endphp
                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                    <td class="px-5 py-4 font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</td>
                    <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $stage->company?->name ?? '—' }}</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $zelfDone ? $klaar : $open }}">{{ $zelfDone ? 'ingediend' : 'open' }}</span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $mentorDone ? $klaar : $open }}">{{ $mentorDone ? 'ingediend' : 'open' }}</span>
                    </td>
                    <td class="px-5 py-4">
                        @if ($eind && $eind->result === 'geslaagd')
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700 ring-1 ring-inset ring-green-200 dark:bg-green-900/30 dark:text-green-300">Geslaagd</span>
                        @elseif ($eind && $eind->result === 'niet_geslaagd')
                            <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-200 dark:bg-red-900/30 dark:text-red-300">Niet geslaagd</span>
                        @else
                            <span class="text-xs text-neutral-400">nog te beoordelen</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('docent.eindbeoordeling', $stage) }}" wire:navigate
                           class="text-sm font-semibold text-[#E2231A] hover:underline">Beoordelen</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-sm text-neutral-500 dark:text-neutral-400">
                        Nog geen stages aan jou toegewezen.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
