<div class="mx-auto flex max-w-3xl flex-col gap-6">

    @if (session('success'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if (! $application)
        <div class="rounded-xl border border-dashed border-neutral-300 bg-white p-10 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
            Je hebt nog geen goedgekeurde stage. Zodra je aanvraag is goedgekeurd, verschijnt de overeenkomst hier.
        </div>

    @elseif (! $agreement)
        <div class="rounded-xl border border-dashed border-neutral-300 bg-white p-10 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
            <flux:icon name="clock" class="mx-auto size-8 text-neutral-300 dark:text-neutral-600" />
            <p class="mt-3 font-medium text-neutral-700 dark:text-neutral-200">Wacht op goedkeuring van de stagecommissie</p>
            <p class="mt-1">Zodra de stagecommissie je aanvraag goedkeurt, verschijnt hier de stageovereenkomst om te ondertekenen.</p>
        </div>

    @else
        @php
            $student   = $application->student;
            $company   = $application->company;
            $stage     = $application->stage;
            $studentNaam = $student?->user?->name ?? auth()->user()->name;
            $mentorNaam  = $stage?->mentor?->user?->name ?? $application->proposed_mentor_name ?? '—';
            $docentNaam  = $stage?->docent?->user?->name ?? '—';
            $start = $application->start_date ?? $stage?->start_date;
            $eind  = $application->end_date ?? $stage?->end_date;
            $jaar  = $student?->academic_year ?? \Illuminate\Support\Carbon::now()->format('Y');
            $ref   = 'STG-' . $jaar . '-' . $application->id;

            $studentGetekend = (bool) $agreement->student_signature;
            $bedrijfGetekend = (bool) $agreement->company_signature;

            $initialen = fn ($naam) => collect(explode(' ', trim($naam)))->filter()->map(fn ($d) => mb_strtoupper(mb_substr($d, 0, 1)))->take(2)->implode('');
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

        {{-- Het overeenkomst-document --}}
        <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
            {{-- Kop --}}
            <div class="bg-[#E2231A] px-8 py-6 text-center text-white">
                <h1 class="text-2xl font-bold">Stageovereenkomst</h1>
                <p class="mt-1 text-sm text-white/80">{{ $jaar }} · Erasmushogeschool Brussel · Ref. {{ $ref }}</p>
            </div>

            <div class="flex flex-col gap-8 p-8">
                {{-- Artikel 1 — Partijen --}}
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

                {{-- Artikel 2 — Stageperiode & werkuren --}}
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

                {{-- Artikel 3 t.e.m. 9 --}}
                @foreach ($artikelen as $titel => $tekst)
                    <section class="border-t border-neutral-100 pt-6 dark:border-neutral-800">
                        <h2 class="mb-2 text-xs font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Artikel {{ $loop->index + 3 }} — {{ $titel }}</h2>
                        <p class="text-sm leading-relaxed text-neutral-700 dark:text-neutral-300">{{ $tekst }}</p>
                    </section>
                @endforeach
            </div>
        </div>

        {{-- Ondertekeningsstatus --}}
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

            {{-- Teken-actie voor de student --}}
            @if ($agreement->status === 'bevestigd')
                <p class="mt-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-200">
                    De stagecommissie heeft de overeenkomst bevestigd. Je stage is officieel van start.
                </p>
            @elseif ($studentGetekend)
                <div class="mt-5 flex flex-wrap items-center gap-4 rounded-xl border border-neutral-200 p-4 dark:border-neutral-800">
                    <img src="{{ $agreement->student_signature }}" alt="Handtekening" class="h-16 rounded-lg border border-neutral-200 bg-white p-1 dark:border-neutral-700" />
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Je hebt ondertekend op {{ $datum($agreement->student_signed_at) }}.</p>
                    <button type="button" wire:click="clearStudentSignature" class="ml-auto text-sm font-medium text-[#E2231A] hover:underline">Opnieuw tekenen</button>
                </div>
            @else
                <div class="mt-5">
                    <p class="mb-2 text-sm font-medium">Onderteken hieronder met je muis, trackpad of vinger.</p>
                    <div x-data="signaturePad()">
                        <canvas x-ref="canvas"
                                class="w-full cursor-crosshair touch-none rounded-lg border border-dashed border-neutral-300 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800"
                                style="height: 180px;"></canvas>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <button type="button" @click="clear()"
                                    class="rounded-lg border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-600 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800">
                                Wissen
                            </button>
                            <button type="button" @click="$wire.signStudent(dataUrl())" x-bind:disabled="empty"
                                    class="rounded-lg bg-[#E2231A] px-5 py-2 text-sm font-semibold text-white transition hover:bg-[#c41e16] disabled:cursor-not-allowed disabled:opacity-50">
                                Onderteken
                            </button>
                            <span x-show="empty" class="text-xs text-neutral-400 dark:text-neutral-500">Teken eerst je handtekening.</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Terug --}}
        <a href="{{ route('student.stage') }}" wire:navigate
           class="inline-flex items-center gap-1.5 self-start rounded-lg border border-neutral-200 bg-white px-4 py-2 text-sm font-medium text-neutral-600 transition hover:bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
            <flux:icon name="arrow-left" class="size-4" /> Terug
        </a>
    @endif
</div>

@assets
<script>
    // Trackpad-handtekening: tekent op een canvas en levert een PNG-dataURL.
    window.signaturePad = window.signaturePad || function () {
        return {
            drawing: false,
            empty: true,
            ctx: null,
            init() {
                const c = this.$refs.canvas;
                c.width = c.offsetWidth;
                c.height = 180;
                const ctx = c.getContext('2d');
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.strokeStyle = '#111827';
                this.ctx = ctx;

                const pos = (e) => {
                    const r = c.getBoundingClientRect();
                    return { x: e.clientX - r.left, y: e.clientY - r.top };
                };
                c.addEventListener('pointerdown', (e) => {
                    this.drawing = true;
                    this.empty = false;
                    const p = pos(e);
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    c.setPointerCapture(e.pointerId);
                });
                c.addEventListener('pointermove', (e) => {
                    if (!this.drawing) return;
                    const p = pos(e);
                    ctx.lineTo(p.x, p.y);
                    ctx.stroke();
                });
                const stop = () => { this.drawing = false; };
                c.addEventListener('pointerup', stop);
                c.addEventListener('pointercancel', stop);
            },
            clear() {
                const c = this.$refs.canvas;
                this.ctx.clearRect(0, 0, c.width, c.height);
                this.empty = true;
            },
            dataUrl() {
                return this.$refs.canvas.toDataURL('image/png');
            },
        };
    };
</script>
@endassets
