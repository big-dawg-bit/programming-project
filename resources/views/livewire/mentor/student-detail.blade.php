<div class="flex max-w-5xl flex-col gap-6">
    @php
        $student = $stage->student?->user?->name ?? 'Onbekende student';
        $studie = $stage->student?->study_program ?? '';
        $jaar = $stage->student?->academic_year ?? '';
        $initialen = collect(explode(' ', $student))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    @endphp

    {{-- Studentkaart --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="grid size-12 shrink-0 place-items-center rounded-full bg-[#E2231A]/10 text-sm font-semibold text-[#E2231A]">
                    {{ $initialen }}
                </div>
                <div>
                    <h2 class="text-lg font-semibold">{{ $student }}</h2>
                    <p class="text-sm text-neutral-500">
                        {{ $studie }}@if ($studie && $jaar) • Jaar {{ $jaar }}@endif
                    </p>
                </div>
            </div>
            <span class="shrink-0 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">Bezig</span>
        </div>
    </div>

    {{-- Statkaarten --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
            <p class="text-xs text-neutral-500">Weeklogs ingediend</p>
            <p class="mt-1 text-2xl font-bold">{{ $totaal }}@if ($totaalWeken)/{{ $totaalWeken }}@endif</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
            <p class="text-xs text-neutral-500">Goedgekeurd</p>
            <p class="mt-1 text-2xl font-bold text-green-600">{{ $goedgekeurd }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
            <p class="text-xs text-neutral-500">Te beoordelen</p>
            <p class="mt-1 text-2xl font-bold text-amber-600">{{ $teBeoordelen }}</p>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
            <p class="text-xs text-neutral-500">Huidige week</p>
            <p class="mt-1 text-2xl font-bold">{{ $huidigeWeek ?? '—' }}</p>
        </div>
    </div>

    {{-- Tabs + inhoud --}}
    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-5 dark:border-neutral-800">
            <nav class="-mb-px flex gap-6">
                @foreach (['weeklogs' => 'Weeklogs', 'evaluaties' => 'Evaluaties', 'documenten' => 'Documenten'] as $key => $label)
                    <button type="button" wire:click="$set('tab', '{{ $key }}')"
                        @class([
                            'border-b-2 py-3 text-sm font-medium transition',
                            'border-[#E2231A] text-neutral-900 dark:text-neutral-100' => $tab === $key,
                            'border-transparent text-neutral-500 hover:text-neutral-700' => $tab !== $key,
                        ])>
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-5">
            {{-- Weeklogs-tab --}}
            @if ($tab === 'weeklogs')
                @if ($weeklogs->isEmpty())
                    <p class="py-8 text-center text-sm text-neutral-500">Nog geen weeklogs.</p>
                @else
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-neutral-100 text-left text-xs text-neutral-500 dark:border-neutral-800">
                            <th class="pb-3 font-medium">Week</th>
                            <th class="pb-3 font-medium">Periode</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium text-right">Actie</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($weeklogs as $log)
                            <tr wire:key="wl-{{ $log->id }}" class="border-b border-neutral-50 dark:border-neutral-800/50">
                                <td class="py-3 font-medium">Week {{ $log->week_number }}</td>
                                <td class="py-3 text-neutral-600 dark:text-neutral-400">
                                    @if ($log->period_start && $log->period_end)
                                        {{ \Illuminate\Support\Carbon::parse($log->period_start)->locale('nl')->translatedFormat('j M') }}
                                        – {{ \Illuminate\Support\Carbon::parse($log->period_end)->locale('nl')->translatedFormat('j M Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3"><x-status-badge :status="$log->status" /></td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('mentor.weeklogs') }}" wire:navigate class="text-sm font-medium text-[#E2231A] hover:underline">Bekijken</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            @endif

            {{-- Evaluaties-tab --}}
            @if ($tab === 'evaluaties')
                @if ($evaluaties->isEmpty())
                    <p class="py-8 text-center text-sm text-neutral-500">Nog geen ingediende evaluaties.</p>
                @else
                    <ul class="divide-y divide-neutral-100 dark:divide-neutral-800">
                        @foreach ($evaluaties as $ev)
                            <li wire:key="ev-{{ $ev->id }}" class="flex items-center justify-between py-3">
                                <span class="text-sm font-medium">
                                    {{ $ev->type === 'final' ? 'Eindevaluatie' : 'Tussentijdse evaluatie' }}
                                </span>
                                <a href="{{ route('mentor.evaluatie.bekijken', ['evaluation' => $ev->id]) }}" wire:navigate
                                   class="text-sm font-medium text-[#E2231A] hover:underline">Bekijken</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            @endif

            {{-- Documenten-tab --}}
            @if ($tab === 'documenten')
                <div class="py-8 text-center">
                    <a href="{{ route('mentor.documenten') }}" wire:navigate class="text-sm font-medium text-[#E2231A] hover:underline">
                        Naar documenten →
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
