<div class="mx-auto flex max-w-6xl flex-col gap-6 lg:flex-row lg:items-start">
    {{-- Categorieën --}}
    <div class="w-full shrink-0 rounded-xl border border-neutral-200 bg-white p-4 lg:w-64 dark:border-neutral-800 dark:bg-neutral-900">
        <h3 class="px-2 pb-3 font-semibold">Categorieën</h3>
        <ul class="space-y-1">
            @foreach (\App\Livewire\Student\DocumentList::CATEGORIEEN as $cat)
                <li>
                    <button type="button" wire:click="$set('categorie', '{{ $cat }}')"
                        @class([
                            'w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition',
                            'bg-[#E2231A] text-white' => $categorie === $cat,
                            'text-neutral-600 hover:bg-neutral-50 dark:text-neutral-400 dark:hover:bg-neutral-800' => $categorie !== $cat,
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
            <div class="mb-4 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
                {{ session('document-uploaded') }}
            </div>
        @endif

        {{-- Uitleg per categorie --}}
        @if ($categorie === 'Logboeken')
            <div class="mb-4 flex items-start gap-2.5 rounded-lg border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-600 dark:border-neutral-800 dark:bg-neutral-800/40 dark:text-neutral-300">
                <flux:icon name="camera" class="mt-0.5 size-4 shrink-0" />
                <span>Upload hier foto's van je stage. Deze zijn zichtbaar voor je docent, mentor en de stagecommissie.</span>
            </div>
        @elseif ($categorie === 'Stageovereenkomst')
            <div class="mb-4 flex items-start gap-2.5 rounded-lg border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-600 dark:border-neutral-800 dark:bg-neutral-800/40 dark:text-neutral-300">
                <flux:icon name="document-check" class="mt-0.5 size-4 shrink-0" />
                <span>De stageovereenkomst beheer je onder <a href="{{ route('student.stage') }}" wire:navigate class="font-medium text-[#E2231A] hover:underline">Mijn stage</a>. Eventuele extra exemplaren kun je hier bewaren.</span>
            </div>
        @endif

        @if ($files->isEmpty())
            <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
                <flux:heading size="lg">Geen documenten</flux:heading>
                <flux:subheading class="mt-1">
                    Er staan nog geen documenten in "{{ $categorie }}".
                </flux:subheading>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Geüploade bestanden (uit de database) --}}
                @foreach ($files as $file)
                    @php $isFoto = str_starts_with((string) $file->mime_type, 'image/'); @endphp
                    <div class="flex flex-col rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900" title="{{ $file->description }}">
                        <flux:icon :name="$isFoto ? 'photo' : 'document-text'" class="size-8 text-[#E2231A]" />
                        <p class="mt-3 truncate text-sm font-medium">{{ $file->original_name }}</p>
                        <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">
                            {{ $file->size_bytes ? number_format($file->size_bytes / 1024, 0) . ' KB' : '—' }}
                        </p>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500">
                            {{ $file->uploaded_at?->locale('nl')->translatedFormat('j M Y') ?? '—' }}
                        </p>
                        <button type="button" wire:click="download({{ $file->id }})"
                            class="mt-3 inline-flex items-center gap-1.5 self-start text-sm font-medium text-[#E2231A] hover:underline">
                            <flux:icon name="arrow-down-tray" class="size-4" /> Downloaden
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Upload-modal --}}
    @if ($showUpload)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            {{-- Achtergrond --}}
            <div class="absolute inset-0 bg-black/40 dark:bg-black/60" wire:click="closeUpload"></div>

            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-neutral-900 dark:ring-1 dark:ring-neutral-800">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Document uploaden</h3>
                    <button type="button" wire:click="closeUpload" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex flex-col gap-4">
                    {{-- Sleepzone / bestandskeuze --}}
                    <label
                        class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-neutral-300 px-6 py-10 text-center transition hover:border-neutral-400 dark:border-neutral-700 dark:hover:border-neutral-600"
                        x-data="{ dragging: false }"
                        x-on:dragover.prevent="dragging = true"
                        x-on:dragleave.prevent="dragging = false"
                        x-on:drop.prevent="dragging = false; $refs.bestand.files = $event.dataTransfer.files; $refs.bestand.dispatchEvent(new Event('change'))"
                        x-bind:class="dragging ? 'border-[#E2231A] bg-red-50 dark:bg-red-950/30' : ''"
                    >
                        <flux:icon name="arrow-up-tray" class="size-7 text-neutral-400 dark:text-neutral-500" />
                        @if ($upload)
                            <span class="text-sm font-medium text-neutral-800 dark:text-neutral-100">{{ $upload->getClientOriginalName() }}</span>
                            <span class="text-xs text-neutral-400 dark:text-neutral-500">Klik om een ander bestand te kiezen</span>
                        @else
                            <span class="text-sm text-neutral-600 dark:text-neutral-300">Sleep bestand hier of klik om te bladeren</span>
                            <span class="text-xs text-neutral-400 dark:text-neutral-500">PDF, DOCX of foto (JPG, PNG), max 10MB</span>
                        @endif
                        <input type="file" class="hidden" x-ref="bestand" wire:model="upload" accept=".pdf,.docx,.jpg,.jpeg,.png,.webp" />
                    </label>
                    <div wire:loading wire:target="upload" class="text-sm text-neutral-500 dark:text-neutral-400">Bestand wordt klaargezet…</div>
                    @error('upload') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                    {{-- Categorie --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium">Categorie</label>
                        <select wire:model="uploadCategorie"
                            class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100">
                            @foreach (\App\Livewire\Student\DocumentList::CATEGORIEEN as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('uploadCategorie') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Beschrijving --}}
                    <div>
                        <label class="mb-1 block text-sm font-medium">Beschrijving (optioneel)</label>
                        <textarea wire:model="beschrijving" rows="3"
                            placeholder="Korte beschrijving van het document..."
                            class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder-neutral-500"></textarea>
                        @error('beschrijving') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Acties --}}
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        <button type="button" wire:click="closeUpload"
                            class="rounded-full border border-neutral-200 px-4 py-2.5 text-sm font-medium text-[#E2231A] transition hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
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
