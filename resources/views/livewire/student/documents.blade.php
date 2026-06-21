<div class="mx-auto flex max-w-6xl flex-col gap-6 lg:flex-row lg:items-start">
    {{-- Tabbladen --}}
    <div class="w-full shrink-0 rounded-xl border border-neutral-200 bg-white p-4 lg:w-64 dark:border-neutral-800 dark:bg-neutral-900">
        <h3 class="px-2 pb-3 font-semibold">Documenten</h3>
        <ul class="space-y-1">
            @foreach (\App\Livewire\Student\DocumentList::TABS as $t)
                <li>
                    <button type="button" wire:click="$set('tab', '{{ $t }}')"
                        @class([
                            'w-full rounded-lg px-3 py-2 text-left text-sm font-medium transition',
                            'bg-[#E2231A] text-white' => $tab === $t,
                            'text-neutral-600 hover:bg-neutral-50 dark:text-neutral-400 dark:hover:bg-neutral-800' => $tab !== $t,
                        ])>
                        {{ $t }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Inhoud --}}
    <div class="flex-1">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">{{ $tab }}</h2>
            @if ($tab === 'Eigen documenten')
                <flux:button variant="primary" icon="arrow-up-tray" wire:click="openUpload">Upload</flux:button>
            @endif
        </div>

        @if (session('document-uploaded'))
            <div class="mb-4 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
                {{ session('document-uploaded') }}
            </div>
        @endif

        {{-- Stageaanvraag --}}
        @if ($tab === 'Stageaanvraag')
            @forelse ($applications as $application)
                <div class="mb-4">
                    <x-document.aanvraag :application="$application" />
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-neutral-300 bg-white p-10 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
                    <flux:icon name="document-text" class="mx-auto size-8 text-neutral-300 dark:text-neutral-600" />
                    <p class="mt-3">Je hebt nog geen stageaanvraag ingediend.</p>
                </div>
            @endforelse

        {{-- Stageovereenkomst --}}
        @elseif ($tab === 'Stageovereenkomst')
            <x-document.overeenkomst :application="$approved" />

        {{-- Logboeken --}}
        @elseif ($tab === 'Logboeken')
            <x-document.logboeken :weeklogs="$weeklogs" />

        {{-- Eigen documenten --}}
        @elseif ($tab === 'Eigen documenten')
            <div class="mb-4 flex items-start gap-2.5 rounded-lg border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-600 dark:border-neutral-800 dark:bg-neutral-800/40 dark:text-neutral-300">
                <flux:icon name="information-circle" class="mt-0.5 size-4 shrink-0" />
                <span>Upload hier extra documenten (cv, motivatiebrief …). Je stagementor kan deze bij jou raadplegen.</span>
            </div>

            @if ($eigenFiles->isEmpty())
                <div class="rounded-2xl border border-dashed border-neutral-300 bg-white p-10 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
                    <flux:icon name="folder-open" class="mx-auto size-8 text-neutral-300 dark:text-neutral-600" />
                    <p class="mt-3">Nog geen eigen documenten geüpload.</p>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($eigenFiles as $file)
                        @php $isFoto = str_starts_with((string) $file->mime_type, 'image/'); @endphp
                        <div class="flex flex-col rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                            <flux:icon :name="$isFoto ? 'photo' : 'document-text'" class="size-8 text-[#E2231A]" />
                            @if ($file->description)
                                <span class="mt-3 w-fit rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">{{ $file->description }}</span>
                            @endif
                            <p class="mt-2 truncate text-sm font-medium">{{ $file->original_name }}</p>
                            <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">
                                {{ $file->size_bytes ? number_format($file->size_bytes / 1024, 0) . ' KB' : '—' }}
                                · {{ $file->uploaded_at?->locale('nl')->translatedFormat('j M Y') ?? '—' }}
                            </p>
                            <button type="button" wire:click="download({{ $file->id }})"
                                class="mt-3 inline-flex items-center gap-1.5 self-start text-sm font-medium text-[#E2231A] hover:underline">
                                <flux:icon name="arrow-down-tray" class="size-4" /> Downloaden
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    {{-- Upload-modal --}}
    @if ($showUpload)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 dark:bg-black/60" wire:click="closeUpload"></div>

            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-neutral-900 dark:ring-1 dark:ring-neutral-800">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Document uploaden</h3>
                    <button type="button" wire:click="closeUpload" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                        <flux:icon name="x-mark" class="size-5" />
                    </button>
                </div>

                <form wire:submit="save" class="flex flex-col gap-4">
                    <flux:select wire:model="soort" label="Soort document">
                        @foreach (\App\Livewire\Student\DocumentList::SOORTEN as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </flux:select>

                    {{-- De input is sr-only (niet display:none) met een expliciete for/id-koppeling,
                         zodat de bestandskiezer in élke browser (ook Safari) opent. --}}
                    <label for="document-upload"
                        class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-neutral-300 px-6 py-10 text-center transition hover:border-neutral-400 dark:border-neutral-700 dark:hover:border-neutral-600">
                        <flux:icon name="arrow-up-tray" class="size-7 text-neutral-400 dark:text-neutral-500" />
                        @if ($upload)
                            <span class="text-sm font-medium text-neutral-800 dark:text-neutral-100">{{ $upload->getClientOriginalName() }}</span>
                            <span class="text-xs text-neutral-400 dark:text-neutral-500">Klik om een ander bestand te kiezen</span>
                        @else
                            <span class="text-sm text-neutral-600 dark:text-neutral-300">Klik om een bestand te kiezen</span>
                            <span class="text-xs text-neutral-400 dark:text-neutral-500">PDF, DOCX of foto (JPG, PNG), max 10MB</span>
                        @endif
                        <input type="file" id="document-upload" class="sr-only" wire:model="upload" accept=".pdf,.docx,.jpg,.jpeg,.png,.webp" />
                    </label>
                    <div wire:loading wire:target="upload" class="text-sm text-neutral-500 dark:text-neutral-400">Bestand wordt klaargezet…</div>

                    @error('upload') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                    <div class="flex justify-end gap-3">
                        <flux:button type="button" variant="ghost" wire:click="closeUpload">Annuleren</flux:button>
                        <flux:button type="submit" variant="primary">Opslaan</flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
