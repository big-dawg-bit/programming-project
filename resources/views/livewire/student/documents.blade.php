<div class="mx-auto flex max-w-6xl flex-col gap-6 lg:flex-row lg:items-start">
    {{-- Categorieën --}}
    <div class="w-full shrink-0 rounded-xl border border-neutral-200 bg-white p-4 lg:w-64">
        <h3 class="px-2 pb-3 font-semibold">Categorieën</h3>
        <ul class="space-y-1">
            @foreach (\App\Livewire\Student\DocumentList::CATEGORIEEN as $cat)
                <li>
                    <button type="button" wire:click="$set('categorie', '{{ $cat }}')"
                        @class([
                            'w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition',
                            'bg-[#E2231A] text-white' => $categorie === $cat,
                            'text-neutral-600 hover:bg-neutral-50' => $categorie !== $cat,
                        ])>
                        {{ $cat }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Documenten --}}
    <div class="flex-1">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">{{ $categorie }}</h2>
            <flux:button variant="primary" icon="arrow-up-tray">Upload</flux:button>
        </div>

        {{-- NB: voorlopig statische voorbeeldinhoud; later koppelen aan het File-model. --}}
        @php
            $documenten = [
                'Stage-aanvraag' => [
                    ['naam' => 'Motivatiebrief.pdf', 'grootte' => '245 KB', 'datum' => '15 jan 2026'],
                    ['naam' => 'CV_LinaJanssens.pdf', 'grootte' => '189 KB', 'datum' => '15 jan 2026'],
                    ['naam' => 'Bedrijfsinfo_Easi.pdf', 'grootte' => '312 KB', 'datum' => '15 jan 2026'],
                ],
                'Overeenkomst' => [
                    ['naam' => 'Stageovereenkomst_v2.docx', 'grootte' => '312 KB', 'datum' => '22 jan 2026'],
                ],
            ];
            $items = $documenten[$categorie] ?? [];
        @endphp

        @if (empty($items))
            <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center">
                <flux:heading size="lg">Geen documenten</flux:heading>
                <flux:subheading class="mt-1">
                    Er staan nog geen documenten in "{{ $categorie }}".
                </flux:subheading>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $doc)
                    <div class="rounded-xl border border-neutral-200 bg-white p-5">
                        <flux:icon name="document-text" class="size-8 text-[#E2231A]" />
                        <p class="mt-3 truncate text-sm font-medium">{{ $doc['naam'] }}</p>
                        <p class="mt-1 text-xs text-neutral-400">{{ $doc['grootte'] }}</p>
                        <p class="text-xs text-neutral-400">{{ $doc['datum'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
