<div class="mx-auto flex max-w-2xl flex-col gap-6">

    <div>
        <h1 class="text-2xl font-bold text-neutral-900">Stageovereenkomst</h1>
        <p class="mt-1 text-sm text-neutral-500">
            Laad je getekende stageovereenkomst op. De stagecommissie bevestigt daarna de ondertekening.
        </p>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (! $application)
        <div class="rounded-xl border border-dashed border-neutral-300 bg-white p-10 text-center text-sm text-neutral-500">
            Je hebt nog geen goedgekeurde stage. Zodra je aanvraag is goedgekeurd, kun je hier je overeenkomst opladen.
        </div>
    @else
        @php
            $statusMeta = [
                'ingediend' => ['label' => 'Ingediend — wacht op bevestiging', 'badge' => 'bg-amber-50 text-amber-700 ring-amber-200'],
                'bevestigd' => ['label' => 'Bevestigd door de stagecommissie',  'badge' => 'bg-green-50 text-green-700 ring-green-200'],
            ];
        @endphp

        {{-- Huidige overeenkomst + status --}}
        @if ($agreement)
            <div class="rounded-xl border border-neutral-200 bg-white p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <flux:icon.document-text class="size-7 shrink-0 text-[#E2231A]" />
                        <div>
                            <p class="text-sm font-medium text-neutral-900">
                                {{ $agreement->file?->original_name ?? 'Overeenkomst' }}
                            </p>
                            <p class="text-xs text-neutral-400">
                                Opgeladen op {{ optional($agreement->uploaded_at)->format('d/m/Y') ?? '—' }}
                            </p>
                        </div>
                    </div>
                    @php $m = $statusMeta[$agreement->status] ?? ['label' => $agreement->status, 'badge' => 'bg-neutral-100 text-neutral-600 ring-neutral-200']; @endphp
                    <span class="rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $m['badge'] }}">
                        {{ $m['label'] }}
                    </span>
                </div>
            </div>
        @endif

        {{-- Onderteken op de trackpad --}}
        @if (! $agreement || $agreement->status !== 'bevestigd')
            <div class="rounded-xl border border-neutral-200 bg-white p-6">
                <h2 class="text-sm font-medium text-neutral-700">Onderteken je overeenkomst</h2>
                <p class="mt-1 text-xs text-neutral-400">Teken hieronder met je muis, trackpad of vinger.</p>

                @if ($agreement?->student_signature)
                    {{-- Reeds getekend --}}
                    <div class="mt-4 flex flex-wrap items-center gap-4">
                        <img src="{{ $agreement->student_signature }}" alt="Handtekening"
                             class="h-24 rounded-lg border border-neutral-200 bg-white p-2" />
                        <div class="text-sm">
                            <p class="font-medium text-green-700">Ondertekend</p>
                            <p class="text-xs text-neutral-400">{{ optional($agreement->student_signed_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        <button type="button" wire:click="clearStudentSignature"
                                class="ml-auto text-sm font-medium text-[#E2231A] hover:underline">
                            Opnieuw tekenen
                        </button>
                    </div>
                @else
                    {{-- Tekenvlak --}}
                    <div x-data="signaturePad()" class="mt-4">
                        <canvas x-ref="canvas"
                                class="w-full cursor-crosshair touch-none rounded-lg border border-dashed border-neutral-300 bg-neutral-50"
                                style="height: 180px;"></canvas>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <button type="button" @click="clear()"
                                    class="rounded-lg border border-neutral-200 px-4 py-2 text-sm font-medium text-neutral-600 transition hover:bg-neutral-50">
                                Wissen
                            </button>
                            <button type="button" @click="$wire.signStudent(dataUrl())" x-bind:disabled="empty"
                                    class="rounded-lg bg-[#E2231A] px-5 py-2 text-sm font-semibold text-white transition hover:bg-[#c41e16] disabled:cursor-not-allowed disabled:opacity-50">
                                Onderteken
                            </button>
                            <span x-show="empty" class="text-xs text-neutral-400">Teken eerst je handtekening.</span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Of: laad een getekende overeenkomst op (zolang nog niet bevestigd) --}}
        @if (! $agreement || $agreement->status !== 'bevestigd')
            <form wire:submit="save" class="flex flex-col gap-5 rounded-xl border border-neutral-200 bg-white p-6">
                <div>
                    <label class="mb-2 block text-sm font-medium text-neutral-700">
                        {{ $agreement ? 'Vervang de overeenkomst' : 'Getekende overeenkomst (PDF of DOCX, max 10 MB)' }}
                    </label>
                    <input type="file" wire:model="upload" accept=".pdf,.docx"
                           class="block w-full text-sm text-neutral-600 file:mr-3 file:rounded-lg file:border-0 file:bg-[#E2231A] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#c41e16]">
                    @error('upload') <p class="mt-1.5 text-sm text-[#E2231A]">{{ $message }}</p> @enderror
                    <div wire:loading wire:target="upload" class="mt-1.5 text-xs text-neutral-400">Bestand wordt geüpload…</div>
                </div>

                <label class="flex items-start gap-2.5 text-sm text-neutral-700">
                    <input type="checkbox" wire:model="insuranceConfirmed"
                           class="mt-0.5 rounded border-neutral-300 text-[#E2231A] focus:ring-[#E2231A]">
                    Ik bevestig dat de stageverzekering in orde is.
                </label>
                @error('insuranceConfirmed') <p class="-mt-3 text-sm text-[#E2231A]">{{ $message }}</p> @enderror

                <div class="flex justify-end border-t border-neutral-100 pt-4">
                    <button type="submit"
                            class="rounded-lg bg-[#E2231A] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#c41e16] focus:ring-2 focus:ring-[#E2231A]/40 focus:outline-none">
                        Overeenkomst opladen
                    </button>
                </div>
            </form>
        @endif
    @endif
</div>

@assets
<script>
    // Trackpad-handtekening: tekent op een canvas en levert een PNG-dataURL.
    window.signaturePad = function () {
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
