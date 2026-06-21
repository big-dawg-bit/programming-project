@props(['application', 'agreement' => null])

@php
    $agreement = $agreement ?? $application?->agreement;
@endphp

@if (! $application || ! $agreement)
    <div class="rounded-2xl border border-dashed border-neutral-300 bg-white p-10 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
        <flux:icon name="document-check" class="mx-auto size-8 text-neutral-300 dark:text-neutral-600" />
        <p class="mt-3">Er is nog geen stageovereenkomst. Die verschijnt zodra de stagecommissie de aanvraag goedkeurt.</p>
    </div>
@else
    @php
        $student   = $application->student;
        $company   = $application->company;
        $stage     = $application->stage;
        $studentNaam = $student?->user?->name ?? '—';
        $mentorNaam  = $stage?->mentor?->user?->name ?? $application->proposed_mentor_name ?? '—';
        $docentNaam  = $stage?->docent?->user?->name ?? '—';
        $start = $application->start_date ?? $stage?->start_date;
        $eind  = $application->end_date ?? $stage?->end_date;
        $jaar  = $student?->academic_year ?? \Illuminate\Support\Carbon::now()->format('Y');
        $ref   = 'STG-' . $jaar . '-' . $application->id;

        $studentGetekend = (bool) $agreement->student_signature;
        $bedrijfGetekend = (bool) $agreement->company_signature;

        $initialen = fn ($naam) => collect(explode(' ', trim((string) $naam)))->filter()->map(fn ($d) => mb_strtoupper(mb_substr($d, 0, 1)))->take(2)->implode('');
        $datum = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y') : '—';

        $artikelen = [
            'Taken & doelstellingen' => 'De student voert gedurende de stage de overeengekomen taken uit zoals beschreven in het stagevoorstel. De stagementor stuurt bij waar nodig en zorgt voor voldoende leerkansen aangepast aan het opleidingsniveau.',
            'Rechten & plichten student' => 'De student houdt zich aan de geldende werk- en veiligheidsregels van de onderneming, respecteert de vertrouwelijkheid van informatie waarmee hij/zij in aanraking komt, en dient wekelijks een logboek in via het Stage Monitor platform.',
            'Verplichtingen onderneming' => 'De onderneming voorziet de student van een werkomgeving die past bij de opleidingsdoelen, stelt een stagementor aan en informeert de hogeschool tijdig bij problemen of afwezigheden.',
            'Verzekering' => 'De Erasmushogeschool Brussel voorziet in een verzekering burgerlijke aansprakelijkheid en lichamelijke ongevallen voor de duur van de stage. De onderneming sluit geen bijkomende arbeidsovereenkomst met de student.',
            'Evaluatie & beoordeling' => 'De student wordt tussentijds en op het einde van de stage geëvalueerd op basis van de opleidingscompetenties. Stagementor en schoolbegeleider vullen samen een evaluatieformulier in.',
            'Vroegtijdige beëindiging' => 'Bij ernstige tekortkomingen of overmacht kan de stage vroegtijdig worden beëindigd in overleg tussen student, stagementor en schoolbegeleider. Eventuele beslissing wordt schriftelijk gemotiveerd.',
            'Toepasselijk recht' => 'Op deze overeenkomst is het Belgisch recht van toepassing. Eventuele geschillen worden in eerste instantie minnelijk geregeld; bij gebrek aan akkoord zijn de rechtbanken van Brussel bevoegd.',
        ];
    @endphp

    <div class="flex flex-col gap-6">
        {{-- Het overeenkomst-document --}}
        <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
            <div class="bg-[#E2231A] px-8 py-6 text-center text-white">
                <h1 class="text-2xl font-bold">Stageovereenkomst</h1>
                <p class="mt-1 text-sm text-white/80">{{ $jaar }} · Erasmushogeschool Brussel · Ref. {{ $ref }}</p>
            </div>

            <div class="flex flex-col gap-8 p-8">
                <section>
                    <h2 class="mb-4 text-xs font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Artikel 1 — Partijen</h2>
                    <dl class="divide-y divide-neutral-100 text-sm dark:divide-neutral-800">
                        @foreach ([
                            'Student' => $studentNaam,
                            'Studentnummer' => $student?->student_number,
                            'Opleiding' => $student?->study_program,
                            'Hogeschool' => 'Erasmushogeschool Brussel',
                            'Onderneming' => $company?->name,
                            'Ondernemingsnr.' => $company?->vat_number,
                            'Adres onderneming' => $company?->address,
                            'Stagementor' => $mentorNaam,
                            'Begeleider school' => $docentNaam,
                        ] as $label => $waarde)
                            <div class="flex items-center justify-between gap-4 py-2.5">
                                <dt class="text-neutral-500 dark:text-neutral-400">{{ $label }}</dt>
                                <dd class="text-right font-semibold {{ $waarde ? 'text-neutral-900 dark:text-neutral-100' : 'text-neutral-300 dark:text-neutral-600' }}">{{ $waarde ?: '—' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                <section>
                    <h2 class="mb-4 text-xs font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Artikel 2 — Stageperiode &amp; werkuren</h2>
                    <dl class="divide-y divide-neutral-100 text-sm dark:divide-neutral-800">
                        @foreach ([
                            'Startdatum' => $datum($start),
                            'Einddatum' => $datum($eind),
                            'Locatie' => $company?->address ?: $company?->name,
                        ] as $label => $waarde)
                            <div class="flex items-center justify-between gap-4 py-2.5">
                                <dt class="text-neutral-500 dark:text-neutral-400">{{ $label }}</dt>
                                <dd class="text-right font-semibold {{ $waarde && $waarde !== '—' ? 'text-neutral-900 dark:text-neutral-100' : 'text-neutral-300 dark:text-neutral-600' }}">{{ $waarde ?: '—' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                @foreach ($artikelen as $titel => $tekst)
                    <section class="border-t border-neutral-100 pt-6 dark:border-neutral-800">
                        <h2 class="mb-2 text-xs font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Artikel {{ $loop->index + 3 }} — {{ $titel }}</h2>
                        <p class="text-sm leading-relaxed text-neutral-700 dark:text-neutral-300">{{ $tekst }}</p>
                    </section>
                @endforeach
            </div>
        </div>

        {{-- Ondertekeningsstatus (alleen-lezen) --}}
        <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Ondertekeningsstatus</h2>
                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600 dark:bg-blue-950/50 dark:text-blue-300">{{ $ref }}</span>
            </div>

            <div class="mt-4 space-y-3">
                @foreach ([
                    ['naam' => $studentNaam, 'rol' => 'Student', 'getekend' => $studentGetekend, 'op' => $agreement->student_signed_at, 'kleur' => 'bg-violet-500'],
                    ['naam' => $company?->name ?? 'Bedrijf', 'rol' => 'Onderneming', 'getekend' => $bedrijfGetekend, 'op' => $agreement->company_signed_at, 'kleur' => 'bg-[#E2231A]'],
                ] as $partij)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-neutral-200 px-4 py-3 dark:border-neutral-800">
                        <div class="flex items-center gap-3">
                            <span class="grid size-10 shrink-0 place-items-center rounded-full text-sm font-semibold text-white {{ $partij['kleur'] }}">
                                {{ $initialen($partij['naam']) ?: '?' }}
                            </span>
                            <div>
                                <p class="font-semibold">{{ $partij['naam'] }}</p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $partij['rol'] }}</p>
                            </div>
                        </div>
                        @if ($partij['getekend'])
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">Ondertekend op {{ $datum($partij['op']) }}</span>
                        @else
                            <span class="text-sm font-medium text-amber-600 dark:text-amber-400">In afwachting</span>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($agreement->status === 'bevestigd')
                <p class="mt-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-200">
                    De stagecommissie heeft de overeenkomst bevestigd.
                </p>
            @endif
        </div>
    </div>
@endif
