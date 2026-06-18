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

        {{-- Upload (of vervangen) zolang nog niet bevestigd --}}
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
