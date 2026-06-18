<div class="mx-auto flex max-w-5xl flex-col gap-6">
    @if (! $application)
        {{-- Geen aanvraag: hier vraagt de student zijn stage aan --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:icon name="briefcase" class="mx-auto size-10 text-neutral-300 dark:text-neutral-600" />
            <flux:heading size="lg" class="mt-3">Nog geen stage aangevraagd</flux:heading>
            <flux:subheading class="mt-1">
                Dien je stageaanvraag in: kies een bedrijf, periode en je voorgestelde mentor.
            </flux:subheading>
            <flux:button href="{{ route('applications.create') }}" wire:navigate variant="primary" icon="plus" class="mt-5">
                Stage aanvragen
            </flux:button>
        </div>
    @else
        @php
            $appStatus = $application->status;                 // submitted | approved | rejected | changes_requested
            $agreementStatus = $application->agreement?->status; // ingediend | bevestigd | null

            $bedrijf = $application->company?->name ?? $stage?->company?->name ?? 'Onbekend bedrijf';

            // Week X van Y (uit de stage-datums, als de stage bestaat)
            $huidigeWeek = null; $totaalWeken = null;
            if ($stage && $stage->start_date && $stage->end_date) {
                $start = \Illuminate\Support\Carbon::parse($stage->start_date);
                $eind = \Illuminate\Support\Carbon::parse($stage->end_date);
                $totaalWeken = max(1, (int) ceil($start->diffInDays($eind) / 7));
                $huidigeWeek = max(1, min((int) floor($start->diffInDays(now(), false) / 7) + 1, $totaalWeken));
            }

            $statusPill = match ($appStatus) {
                'approved'          => ['Goedgekeurd', 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
                'rejected'          => ['Afgewezen', 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'],
                'changes_requested' => ['Aanpassing gevraagd', 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'],
                default             => ['In beoordeling', 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300'],
            };
        @endphp

        {{-- Statusbanner --}}
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-neutral-200 bg-white px-5 py-4 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-4">
                <span class="shrink-0 rounded-full px-3 py-1 text-xs font-medium {{ $statusPill[1] }}">{{ $statusPill[0] }}</span>
                <div>
                    <p class="font-semibold">{{ $bedrijf }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        @if ($huidigeWeek)
                            Week {{ $huidigeWeek }} van {{ $totaalWeken }}
                            @if ($stage->start_date && $stage->end_date)
                                · {{ \Illuminate\Support\Carbon::parse($stage->start_date)->locale('nl')->translatedFormat('j M Y') }}
                                – {{ \Illuminate\Support\Carbon::parse($stage->end_date)->locale('nl')->translatedFormat('j M Y') }}
                            @endif
                        @elseif ($application->submitted_at)
                            Ingediend op {{ $application->submitted_at->locale('nl')->translatedFormat('j F Y') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Acties bij changes_requested / rejected --}}
        @if ($appStatus === 'changes_requested')
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-300 bg-amber-50 px-5 py-4 dark:border-amber-900/50 dark:bg-amber-950/40">
                <div>
                    <p class="font-medium text-amber-800 dark:text-amber-200">De commissie vroeg om aanpassingen</p>
                    @if ($laatsteReview?->feedback)
                        <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">{{ $laatsteReview->feedback }}</p>
                    @endif
                </div>
                <flux:button href="{{ route('applications.edit', $application) }}" wire:navigate variant="primary" icon="pencil-square">
                    Aanvraag aanpassen
                </flux:button>
            </div>
        @elseif ($appStatus === 'rejected')
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-red-300 bg-red-50 px-5 py-4 dark:border-red-900/50 dark:bg-red-950/40">
                <div>
                    <p class="font-medium text-red-800 dark:text-red-200">Je aanvraag werd afgewezen</p>
                    @if ($laatsteReview?->feedback)
                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $laatsteReview->feedback }}</p>
                    @endif
                </div>
                <flux:button href="{{ route('applications.create') }}" wire:navigate variant="primary" icon="plus">
                    Nieuwe aanvraag
                </flux:button>
            </div>
        @endif

        @if ($appStatus !== 'rejected')
            <div class="grid gap-6 lg:grid-cols-3">
                {{-- Statustijdlijn (afgeleid van de echte status) --}}
                <div class="rounded-xl border border-neutral-200 bg-white p-6 lg:col-span-2 dark:border-neutral-800 dark:bg-neutral-900">
                    <h3 class="mb-5 text-lg font-semibold">Status</h3>
                    @php
                        $gestart = $stage && $stage->start_date && \Illuminate\Support\Carbon::parse($stage->start_date)->lte(now());
                        $done = [
                            true,                                   // Ingediend
                            $appStatus === 'approved',              // In beoordeling (review doorlopen)
                            $appStatus === 'approved',              // Goedgekeurd
                            $agreementStatus === 'bevestigd',       // Overeenkomst getekend
                            (bool) $gestart,                        // Stage gestart
                        ];
                        $stappen = ['Ingediend', 'In beoordeling', 'Goedgekeurd', 'Overeenkomst getekend', 'Stage gestart'];
                        $huidigeIndex = array_search(false, $done, true);
                        if ($huidigeIndex === false) { $huidigeIndex = count($done); }
                    @endphp
                    <ol>
                        @foreach ($stappen as $i => $label)
                            {{-- Lineair: alles vóór de huidige stap is afgerond, daarna nog te doen --}}
                            @php $isDone = $i < $huidigeIndex; $isCurrent = ($i === $huidigeIndex); @endphp
                            <li class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    @if ($isDone)
                                        <flux:icon.check-circle variant="solid" class="size-6 text-green-500" />
                                    @elseif ($isCurrent)
                                        <span class="grid size-6 place-items-center rounded-full border-2 border-[#E2231A]">
                                            <span class="size-2.5 rounded-full bg-[#E2231A]"></span>
                                        </span>
                                    @else
                                        <span class="size-6 rounded-full border-2 border-neutral-300 dark:border-neutral-700"></span>
                                    @endif
                                    @if (! $loop->last)
                                        <span class="my-1 w-0.5 flex-1 {{ $isDone ? 'bg-green-500' : 'bg-neutral-200 dark:bg-neutral-700' }}"></span>
                                    @endif
                                </div>
                                <span class="pb-8 pt-0.5 font-medium {{ ($isDone || $isCurrent) ? 'text-neutral-900 dark:text-neutral-100' : 'text-neutral-400 dark:text-neutral-500' }}">
                                    {{ $label }}
                                </span>
                            </li>
                        @endforeach
                    </ol>
                </div>

                {{-- Checklist (afgeleid) + volgende actie --}}
                <div class="flex flex-col gap-6">
                    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                        <h3 class="mb-5 text-lg font-semibold">Checklist</h3>
                        @php
                            $checklist = [
                                ['Aanvraag ingediend', true],
                                ['Goedgekeurd door commissie', $appStatus === 'approved'],
                                ['Overeenkomst opgeladen', in_array($agreementStatus, ['ingediend', 'bevestigd'], true)],
                                ['Overeenkomst bevestigd', $agreementStatus === 'bevestigd'],
                            ];
                        @endphp
                        <ul class="space-y-3">
                            @foreach ($checklist as [$label, $ok])
                                <li class="flex items-start gap-2.5 text-sm">
                                    @if ($ok)
                                        <flux:icon.check-circle variant="solid" class="size-5 shrink-0 text-green-500" />
                                        <span>{{ $label }}</span>
                                    @else
                                        <span class="mt-0.5 size-4 shrink-0 rounded border border-neutral-300 dark:border-neutral-700"></span>
                                        <span class="text-neutral-400 dark:text-neutral-500">{{ $label }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Volgende stap --}}
                    @if ($appStatus === 'submitted')
                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-5 text-sm text-blue-800 dark:border-blue-900/50 dark:bg-blue-950/40 dark:text-blue-200">
                            Je aanvraag wordt beoordeeld door de stagecommissie.
                        </div>
                    @elseif ($appStatus === 'approved' && ! $agreementStatus)
                        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                            <p class="text-sm text-neutral-600 dark:text-neutral-300">Je aanvraag is goedgekeurd. Laad nu de getekende stageovereenkomst op.</p>
                            <flux:button href="{{ route('student.agreement') }}" wire:navigate variant="primary" icon="arrow-up-tray" class="mt-3 w-full">
                                Overeenkomst opladen
                            </flux:button>
                        </div>
                    @elseif ($agreementStatus === 'ingediend')
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200">
                            Je overeenkomst is opgeladen en wacht op bevestiging door de stagecommissie.
                        </div>
                    @elseif ($agreementStatus === 'bevestigd')
                        <div class="rounded-xl border border-green-200 bg-green-50 p-5 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-200">
                            Alles is in orde — je stage is officieel van start.
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>
