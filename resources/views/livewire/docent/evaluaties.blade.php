<div class="mx-auto flex max-w-5xl flex-col gap-6">
    <div>
        <h2 class="text-lg font-semibold">Evaluaties</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">Vul de tussentijdse en eindevaluatie in per student.</p>
    </div>

    @php
        $klaar = 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300';
        $open = 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300';
    @endphp

    <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-neutral-800 dark:bg-neutral-800/50 dark:text-neutral-400">
                <tr>
                    <th class="px-5 py-3 font-medium">Student</th>
                    <th class="px-5 py-3 font-medium">Bedrijf</th>
                    <th class="px-5 py-3 font-medium">Tussentijds</th>
                    <th class="px-5 py-3 font-medium">Eind</th>
                    <th class="px-5 py-3 font-medium text-right">Actie</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @forelse ($stages as $stage)
                    @php
                        $midDone = $stage->evaluations->where('type', 'mid-term')->where('status', 'submitted')->isNotEmpty();
                        $finalDone = $stage->evaluations->where('type', 'final')->where('status', 'submitted')->isNotEmpty();
                    @endphp
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-5 py-4 font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $stage->company?->name ?? '—' }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $midDone ? $klaar : $open }}">{{ $midDone ? 'ingediend' : 'open' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $finalDone ? $klaar : $open }}">{{ $finalDone ? 'ingediend' : 'open' }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('evaluations.create', $stage) }}" wire:navigate class="text-sm font-semibold text-[#E2231A] hover:underline">Invullen</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            Nog geen stages aan jou toegewezen.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
