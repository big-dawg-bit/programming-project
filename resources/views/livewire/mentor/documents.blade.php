<div class="flex max-w-6xl flex-col gap-6 lg:flex-row lg:items-start">
    {{-- Categorieën --}}
    <div class="w-full shrink-0 rounded-xl border border-neutral-200 bg-white p-4 lg:w-64 dark:border-neutral-800 dark:bg-neutral-900">
        <h3 class="px-2 pb-3 font-semibold">Categorieën</h3>
        <ul class="space-y-1">
            @foreach (\App\Livewire\Mentor\DocumentList::CATEGORIEEN as $cat)
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
        <div class="mb-4">
            <h2 class="text-lg font-semibold">{{ $categorie }}</h2>
        </div>

        @if ($files->isEmpty())
            <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
                <flux:icon name="folder-open" class="mx-auto size-8 text-neutral-300" />
                <p class="mt-3 text-sm text-neutral-500">Nog geen documenten in deze categorie</p>
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($files as $file)
                    <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900" title="{{ $file->description }}">
                        <flux:icon name="document-text" class="size-8 text-[#E2231A]" />
                        <p class="mt-3 truncate text-sm font-medium">{{ $file->original_name }}</p>
                        <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">
                            {{ $file->size_bytes ? number_format($file->size_bytes / 1024, 0) . ' KB' : '—' }}
                        </p>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500">
                            {{ $file->uploaded_at?->locale('nl')->translatedFormat('j M Y') ?? '—' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
