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
            <flux:button variant="primary" icon="arrow-up-tray" wire:click="openUpload">Upload</flux:button>
        </div>

        @if (session('document-uploaded'))
            <div class="mb-4 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('document-uploaded') }}
            </div>
        @endif

        {{-- NB: de voorbeelddocumenten hieronder zijn statisch; geüploade bestanden komen uit de files-tabel. --}}
        @php
            $voorbeelden = [
                'Stage-aanvraag' => [
                    ['naam' => 'Motivatiebrief.pdf', 'grootte' => '245 KB', 'datum' => '15 jan 2026'],
                    ['naam' => 'CV_LinaJanssens.pdf', 'grootte' => '189 KB', 'datum' => '15 jan 2026'],
                    ['naam' => 'Bedrijfsinfo_Easi.pdf', 'grootte' => '312 KB', 'datum' => '15 jan 2026'],
                ],
                'Overeenkomst' => [
                    ['naam' => 'Stageovereenkomst_v2.docx', 'grootte' => '312 KB', 'datum' => '22 jan 2026'],
                ],
            ];
            $items = $voorbeelden[$categorie] ?? [];
        @endphp

        @if ($files->isEmpty() && empty($items))
            <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center">
                <flux:heading size="lg">Geen documenten</flux:heading>
                <flux:subheading class="mt-1">
                    Er staan nog geen documenten in "{{ $categorie }}".
                </flux:subheading>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Geüploade bestanden (uit de database) --}}
                @foreach ($files as $file)
                    <div class="rounded-xl border border-neutral-200 bg-white p-5" title="{{ $file->description }}">
                        <flux:icon name="document-text" class="size-8 text-[#E2231A]" />
                        <p class="mt-3 truncate text-sm font-medium">{{ $file->original_name }}</p>
                        <p class="mt-1 text-xs text-neutral-400">
                            {{ $file->size_bytes ? number_format($file->size_bytes / 1024, 0) . ' KB' : '—' }}
                        </p>
                        <p class="text-xs text-neutral-400">
                            {{ $file->uploaded_at?->locale('nl')->translatedFormat('j M Y') ?? '—' }}
                        </p>
                    </div>
                @endforeach

                {{-- Statische voorbeelddocumenten --}}
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

    {{-- Upload-modal --}}
    @if ($showUpload)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Achtergrond --}}
            <div class="absolute inset-0 bg-black/40" wire:click="closeUpload"></div>

            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Document uploaden</h3>
                    <button type="button" wire:click="closeUpload" class="text-neutral-400 hover:text-neutral-600">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex flex-col gap-4">
                    {{-- Sleepzone / bestandskeuze --}}
                    <label
                        class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-neutral-300 px-6 py-10 text-center transition hover:border-neutral-400"
                        x-data="{ dragging: false }"
                        x-on:dragover.prevent="dragging = true"
                        x-on:dragleave.prevent="dragging = false"
                        x-on:drop.prevent="dragging = false; $refs.bestand.files = $event.dataTransfer.files; $refs.bestand.dispatchEvent(new Event('change'))"
                        x-bind:class="dragging ? 'border-[#E2231A] bg-red-50' : ''"
                    >
                        <flux:icon name="arrow-up-tray" class="size-7 text-neutral-400" />
                        @if ($upload)
                            <span class="text-sm font-medium text-neutral-800">{{ $upload->getClientOriginalName() }}</span>
                            <span class="text-xs text-neutral-400">Klik om een ander bestand te kiezen</span>
                        @else
                            <span class="text-sm text-neutral-600">Sleep bestand hier of klik om te bladeren</span>
                            <span class="text-xs text-neutral-400">PDF, DOCX, max 10MB</span>
                        @endif
                        <input type="file" class="hidden" x-ref="bestand" wire:model="upload" accept=".pdf,.docx" />
                    </label>
                    <div wire:loading wire:target="upload" class="text-sm text-neutral-500">Bestand wordt klaargezet…</div>
                    @error('upload') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                    {{-- Categorie --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium">Categorie</label>
                        <select wire:model="uploadCategorie"
                            class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none">
                            @foreach (\App\Livewire\Student\DocumentList::CATEGORIEEN as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('uploadCategorie') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Beschrijving --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium">Beschrijving (optioneel)</label>
                        <textarea wire:model="beschrijving" rows="3"
                            placeholder="Korte beschrijving van het document..."
                            class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none"></textarea>
                        @error('beschrijving') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Acties --}}
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        <button type="button" wire:click="closeUpload"
                            class="rounded-full border border-neutral-200 px-4 py-2.5 text-sm font-medium text-[#E2231A] transition hover:bg-neutral-50">
                            Annuleren
                        </button>
                        <button type="submit" @disabled(! $upload)
                            class="rounded-full bg-[#E2231A] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#c91e16] disabled:cursor-not-allowed disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">Uploaden</span>
                            <span wire:loading wire:target="save">Bezig…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
