<div class="mx-auto flex max-w-6xl flex-col gap-8">
    {{-- Begroeting --}}
    <div>
        <h2 class="text-2xl font-bold">Hallo {{ auth()->user()->first_name ?? auth()->user()->name }}</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            {{ $stages->count() }} begeleide {{ \Illuminate\Support\Str::plural('stage', $stages->count()) }}
        </p>
    </div>

    @if (session('docent-signed'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
            {{ session('docent-signed') }}
        </div>
    @endif

    {{-- Statuskaarten --}}
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="users" class="size-4" /> Begeleide stages
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $stages->count() }}</p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="clipboard-document-check" class="size-4" /> Nog te evalueren
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $teEvalueren }}</p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="document-text" class="size-4" /> Totaal weeklogs
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $stages->sum('weeklogs_count') }}</p>
        </div>
    </div>

    {{-- Lijst begeleide stages --}}
    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
            <h3 class="font-semibold">Mijn stages</h3>
        </div>

        @forelse ($stages as $stage)
            @php
                $agreement = $stage->application?->agreement;
                $magTekenen = $agreement && $agreement->status === 'te_ondertekenen' && ! $agreement->docent_signature;
                $overeenkomstChip = match (true) {
                    $agreement?->docent_signature && $agreement->status === 'te_ondertekenen'
                        => ['Getekend — wacht op student', 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300'],
                    $agreement?->status === 'ingediend' => ['Getekend — bij commissie', 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300'],
                    $agreement?->status === 'bevestigd' => ['Overeenkomst bevestigd', 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
                    default => null,
                };
            @endphp
            <div class="flex items-center justify-between gap-3 border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                <div>
                    <p class="font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $stage->company?->name ?? '—' }} · {{ $stage->weeklogs_count }} weeklogs
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if ($magTekenen)
                        <flux:button size="sm" variant="filled" icon="pencil-square" wire:click="openSign({{ $stage->id }})">
                            Overeenkomst tekenen
                        </flux:button>
                    @elseif ($overeenkomstChip)
                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $overeenkomstChip[1] }}">{{ $overeenkomstChip[0] }}</span>
                    @endif
                </div>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-neutral-500 dark:text-neutral-400">
                Nog geen stages aan jou toegewezen.
            </p>
        @endforelse
    </div>

    {{-- Teken-modal voor de docent-handtekening --}}
    @if ($signingStageId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 dark:bg-black/60" wire:click="closeSign"></div>

            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-neutral-900 dark:ring-1 dark:ring-neutral-800">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Onderteken de stageovereenkomst</h3>
                    <button type="button" wire:click="closeSign" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>
                <p class="mb-3 text-xs text-neutral-400 dark:text-neutral-500">Teken met je muis, trackpad of vinger.</p>

                <div x-data="signaturePad()">
                    <canvas x-ref="canvas"
                            class="w-full cursor-crosshair touch-none rounded-lg border border-dashed border-neutral-300 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800"
                            style="height: 180px;"></canvas>
                    <div class="mt-4 flex flex-wrap items-center justify-end gap-3">
                        <button type="button" @click="clear()"
                                class="rounded-lg border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-600 transition hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800">
                            Wissen
                        </button>
                        <button type="button" @click="$wire.signDocent(dataUrl())" x-bind:disabled="empty"
                                class="rounded-lg bg-[#E2231A] px-5 py-2 text-sm font-semibold text-white transition hover:bg-[#c41e16] disabled:cursor-not-allowed disabled:opacity-50">
                            Onderteken
                        </button>
                    </div>
                </div>
            </div>
        </div>
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
