<div class="mx-auto flex max-w-3xl flex-col gap-6">
    <div>
        <flux:heading size="xl">Overeenkomsten</flux:heading>
        <flux:subheading>Onderteken de stageovereenkomsten van jouw stagiairs.</flux:subheading>
    </div>

    @if (session('bedrijf-status'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
            {{ session('bedrijf-status') }}
        </div>
    @endif

    @if (! $company)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300">
            <p class="font-medium">Geen bedrijf gekoppeld</p>
        </div>

    @elseif ($signingApplication)
        {{-- ===== DETAIL: volledig document + teken-pad ===== --}}
        @php
            $application = $signingApplication;
            $company2   = $application->company;
            $student    = $application->student;
            $stage      = $application->stage;
            $agreement  = $application->agreement;
            $studentNaam = $student?->user?->name ?? 'Student';
            $mentorNaam  = $stage?->mentor?->user?->name ?? '—';
            $docentNaam  = $stage?->docent?->user?->name ?? '—';
            $start = $application->start_date ?? $stage?->start_date;
            $eind  = $application->end_date ?? $stage?->end_date;
            $jaar  = $student?->academic_year ?? \Illuminate\Support\Carbon::now()->format('Y');
            $ref   = 'STG-' . $jaar . '-' . $application->id;
            $datum = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y') : '—';
            $artikelen = [
                'Taken & doelstellingen' => 'De student voert gedurende de stage de overeengekomen taken uit zoals beschreven in het stagevoorstel. De stagementor stuurt bij waar nodig en zorgt voor voldoende leerkansen aangepast aan het opleidingsniveau.',
                'Rechten & plichten student' => 'De student houdt zich aan de geldende werk- en veiligheidsregels van de onderneming, respecteert de vertrouwelijkheid van informatie waarmee hij/zij in aanraking komt, en dient wekelijks een logboek in via het Stage Monitor platform.',
                'Verplichtingen onderneming' => 'De onderneming voorziet de student van een werkomgeving die past bij de opleidingsdoelen, stelt een stagementor aan en informeert de hogeschool tijdig bij problemen of afwezigheden.',
                'Verzekering' => 'De Erasmushogeschool Brussel voorziet in een verzekering burgerlijke aansprakelijkheid en lichamelijke ongevallen voor de duur van de stage. De onderneming sluit geen bijkomende arbeidsovereenkomst met de student.',
                'Evaluatie & beoordeling' => 'De student wordt tussentijds en op het einde van de stage geëvalueerd op basis van de opleidingscompetenties. Stagementor en schoolbegeleider vullen samen een evaluatieformulier in.',
                'Vroegtijdige beëindiging' => 'Bij ernstige tekortkomingen of overmacht kan de stage vroegtijdig worden beëindigd in overleg tussen student, stagementor en schoolbegeleider.',
                'Toepasselijk recht' => 'Op deze overeenkomst is het Belgisch recht van toepassing. Geschillen worden in eerste instantie minnelijk geregeld; bij gebrek aan akkoord zijn de rechtbanken van Brussel bevoegd.',
            ];
        @endphp

        <button wire:click="closeSign" type="button"
                class="inline-flex items-center gap-1.5 self-start rounded-lg border border-neutral-200 bg-white px-4 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-300">
            <flux:icon name="arrow-left" class="size-4" /> Terug naar overzicht
        </button>

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
                            'Onderneming' => $company2?->name,
                            'Adres onderneming' => $company2?->address,
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
                            'Locatie' => $company2?->address ?: $company2?->name,
                        ] as $label => $waarde)
                            <div class="flex items-center justify-between gap-4 py-2.5">
                                <dt class="text-neutral-500 dark:text-neutral-400">{{ $label }}</dt>
                                <dd class="text-right font-semibold text-neutral-900 dark:text-neutral-100">{{ $waarde ?: '—' }}</dd>
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

        {{-- Teken-actie voor het bedrijf --}}
        <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
            <h2 class="text-xs font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Handtekening onderneming</h2>
            @if ($agreement->company_signature)
                <div class="mt-4 flex flex-wrap items-center gap-4 rounded-xl border border-neutral-200 p-4 dark:border-neutral-800">
                    <img src="{{ $agreement->company_signature }}" alt="Handtekening" class="h-16 rounded-lg border border-neutral-200 bg-white p-1 dark:border-neutral-700" />
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Getekend op {{ $datum($agreement->company_signed_at) }}.</p>
                </div>
            @else
                <div class="mt-4" x-data="signaturePad()">
                    <p class="mb-2 text-sm font-medium">Onderteken hieronder met je muis, trackpad of vinger.</p>
                    <canvas x-ref="canvas"
                            class="w-full cursor-crosshair touch-none rounded-lg border border-dashed border-neutral-300 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800"
                            style="height: 180px;"></canvas>
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <button type="button" @click="clear()"
                                class="rounded-lg border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300">
                            Wissen
                        </button>
                        <button type="button" @click="$wire.signCompany(dataUrl())" x-bind:disabled="empty"
                                class="rounded-lg bg-[#E2231A] px-5 py-2 text-sm font-semibold text-white hover:bg-[#c41e16] disabled:cursor-not-allowed disabled:opacity-50">
                            Onderteken
                        </button>
                        <span x-show="empty" class="text-xs text-neutral-400 dark:text-neutral-500">Teken eerst je handtekening.</span>
                    </div>
                </div>
            @endif
        </div>

    @elseif ($applications->isEmpty())
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:heading size="lg">Geen overeenkomsten</flux:heading>
        </div>

    @else
        {{-- ===== LIJST ===== --}}
        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
                <h3 class="font-semibold">Te tekenen</h3>
            </div>
            @foreach ($applications as $application)
                @php
                    $agreement = $application->agreement;
                    $periode = collect([$application->start_date, $application->end_date])
                        ->filter()
                        ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y'))
                        ->implode(' - ');
                @endphp
                <div wire:key="agr-{{ $agreement->id }}" class="flex items-center justify-between gap-3 border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                    <div>
                        <p class="font-medium">{{ $application->student?->user?->name ?? 'Onbekende student' }}</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $periode ?: 'Geen periode' }}</p>
                    </div>
                    <div>
                        @if ($agreement->company_signature)
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">
                                Getekend op {{ \Illuminate\Support\Carbon::parse($agreement->company_signed_at)->locale('nl')->translatedFormat('j M Y') }}
                            </span>
                        @else
                            <button wire:click="openSign({{ $agreement->id }})"
                                    class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700">
                                Tekenen
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @assets
    <script>
        window.signaturePad = window.signaturePad || function () {
            return {
                drawing: false, empty: true, ctx: null,
                init() {
                    const c = this.$refs.canvas;
                    c.width = c.offsetWidth; c.height = 180;
                    const ctx = c.getContext('2d');
                    ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.strokeStyle = '#111827';
                    this.ctx = ctx;
                    const pos = (e) => { const r = c.getBoundingClientRect(); return { x: e.clientX - r.left, y: e.clientY - r.top }; };
                    c.addEventListener('pointerdown', (e) => { this.drawing = true; this.empty = false; const p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); c.setPointerCapture(e.pointerId); });
                    c.addEventListener('pointermove', (e) => { if (!this.drawing) return; const p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
                    const stop = () => { this.drawing = false; };
                    c.addEventListener('pointerup', stop); c.addEventListener('pointercancel', stop);
                },
                clear() { const c = this.$refs.canvas; this.ctx.clearRect(0, 0, c.width, c.height); this.empty = true; },
                dataUrl() { return this.$refs.canvas.toDataURL('image/png'); },
            };
        };
    </script>
    @endassets
</div>
