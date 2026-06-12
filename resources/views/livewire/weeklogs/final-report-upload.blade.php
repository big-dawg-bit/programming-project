<x-layouts.portal title="Eindrapport">
<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">Eindrapport</flux:heading>
        @if ($stage)
            <flux:subheading>
                Stage van {{ $stage->student?->user?->name ?? 'onbekende student' }}
            </flux:subheading>
        @endif
    </div>

    @if (session('report-saved'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-700 dark:bg-green-950 dark:text-green-200">
            {{ session('report-saved') }}
        </div>
    @endif

    @if (! $stage)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-200">
            <p class="font-medium">Geen stage gevonden</p>
            <p class="mt-1 text-sm">
                Draai eerst de seeders:
                <code>php artisan migrate:fresh --seed</code> en daarna
                <code>php artisan db:seed --class=StageSeeder</code>.
            </p>
        </div>
    @else
        {{-- Reeds ingediend rapport --}}
        @if ($finalReport && $finalReport->file)
            <div class="rounded-xl border border-neutral-200 p-5 dark:border-neutral-700">
                <flux:heading size="lg">Huidig eindrapport</flux:heading>
                <div class="mt-2 text-sm">
                    <p><span class="text-neutral-500">Bestand:</span> {{ $finalReport->file->original_name }}</p>
                    <p><span class="text-neutral-500">Ingediend op:</span>
                        {{ $finalReport->submitted_at?->format('d/m/Y H:i') ?? '—' }}</p>
                    @if ($finalReport->summary)
                        <p class="mt-2">{{ $finalReport->summary }}</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Upload-formulier --}}
        <form wire:submit="save" class="space-y-4 rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
            <flux:heading size="lg">
                {{ $finalReport ? 'Eindrapport vervangen' : 'Eindrapport indienen' }}
            </flux:heading>

            <div>
                <label class="mb-1 block text-sm font-medium">Rapport (PDF of Word, max 10 MB)</label>
                <input type="file" wire:model="report" accept=".pdf,.doc,.docx"
                    class="block w-full text-sm text-neutral-600 file:mr-3 file:rounded-lg file:border-0 file:bg-neutral-200 file:px-4 file:py-2 file:text-sm file:font-medium dark:text-neutral-300 dark:file:bg-neutral-700" />
                @error('report')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div wire:loading wire:target="report" class="text-sm text-neutral-500">
                Bestand wordt geüpload… even wachten tot dit weg is.
            </div>

            <flux:textarea
                wire:model="summary"
                label="Korte samenvatting (optioneel)"
                rows="4"
                placeholder="Een korte beschrijving van je eindrapport…" />

            <flux:button type="submit" variant="primary" wire:target="report" wire:loading.attr="disabled">
                Indienen
            </flux:button>
        </form>
    @endif
</div>

</x-layouts.portal>
