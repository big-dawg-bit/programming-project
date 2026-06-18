<div class="mx-auto flex max-w-4xl flex-col gap-6">

    <div>
        <h1 class="text-2xl font-bold text-neutral-900">Stageovereenkomsten</h1>
        <p class="mt-1 text-sm text-neutral-500">Bevestig de ondertekening van de ingediende stageovereenkomsten.</p>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($agreements as $agreement)
        <div wire:key="agr-{{ $agreement->id }}"
             class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-neutral-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <flux:icon.document-text class="size-7 shrink-0 text-[#E2231A]" />
                <div>
                    <p class="font-semibold text-neutral-900">
                        {{ $agreement->application?->student?->user?->name ?? 'Onbekende student' }}
                    </p>
                    <p class="text-sm text-neutral-500">
                        {{ $agreement->application?->company?->name ?? '—' }}
                        · {{ $agreement->file?->original_name ?? 'overeenkomst' }}
                    </p>
                    <p class="mt-1 text-xs text-neutral-400">
                        Ingediend op {{ optional($agreement->uploaded_at)->format('d/m/Y') ?? '—' }}
                        @if ($agreement->insurance_confirmed)
                            · verzekering bevestigd
                        @endif
                    </p>
                </div>
            </div>

            <button wire:click="confirm({{ $agreement->id }})"
                    class="rounded-lg bg-[#E2231A] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#c41e16] focus:ring-2 focus:ring-[#E2231A]/40 focus:outline-none">
                Ondertekening bevestigen
            </button>
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-neutral-300 bg-white p-10 text-center">
            <p class="text-sm text-neutral-500">Er zijn geen overeenkomsten die op bevestiging wachten.</p>
        </div>
    @endforelse
</div>
