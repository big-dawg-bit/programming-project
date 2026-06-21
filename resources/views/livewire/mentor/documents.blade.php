<div class="mx-auto flex max-w-5xl flex-col gap-6">
    @if (! $selected)
        {{-- ===== LIJST VAN STAGIAIRS ===== --}}
        <div>
            <h2 class="text-lg font-semibold">Documenten</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Raadpleeg per stagiair de stageaanvraag, de overeenkomst, de logboeken en de geüploade documenten.</p>
        </div>

        @if ($stages->isEmpty())
            <div class="rounded-2xl border border-dashed border-neutral-300 bg-white p-10 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
                <flux:icon name="users" class="mx-auto size-8 text-neutral-300 dark:text-neutral-600" />
                <p class="mt-3">Je hebt nog geen stagiairs toegewezen gekregen.</p>
            </div>
        @else
            <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                @foreach ($stages as $stage)
                    <button type="button" wire:click="openStudent({{ $stage->id }})"
                        class="flex w-full items-center justify-between gap-3 border-b border-neutral-100 px-5 py-4 text-left transition last:border-0 hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-800/40">
                        <div class="flex items-center gap-3">
                            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-[#E2231A]/10 text-sm font-semibold text-[#E2231A]">
                                {{ collect(explode(' ', (string) ($stage->student?->user?->name)))->filter()->map(fn ($d) => mb_strtoupper(mb_substr($d, 0, 1)))->take(2)->implode('') ?: '?' }}
                            </span>
                            <div>
                                <p class="font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $stage->company?->name ?? '—' }}</p>
                            </div>
                        </div>
                        <flux:icon name="chevron-right" class="size-5 text-neutral-300 dark:text-neutral-600" />
                    </button>
                @endforeach
            </div>
        @endif

    @else
        {{-- ===== DETAIL: documenten van één stagiair ===== --}}
        <button type="button" wire:click="closeStudent"
            class="inline-flex items-center gap-1.5 self-start rounded-lg border border-neutral-200 bg-white px-4 py-2 text-sm font-medium text-neutral-600 transition hover:bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
            <flux:icon name="arrow-left" class="size-4" /> Terug naar overzicht
        </button>

        <div>
            <h2 class="text-lg font-semibold">{{ $selected->student?->user?->name ?? 'Stagiair' }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $selected->company?->name ?? '—' }}</p>
        </div>

        {{-- Stageaanvraag --}}
        <section class="flex flex-col gap-3">
            <h3 class="text-xs font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Stageaanvraag</h3>
            @if ($application)
                <x-document.aanvraag :application="$application" />
            @else
                <p class="rounded-xl border border-dashed border-neutral-300 bg-white px-4 py-6 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">Geen aanvraag gekoppeld.</p>
            @endif
        </section>

        {{-- Stageovereenkomst --}}
        <section class="flex flex-col gap-3">
            <h3 class="text-xs font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Stageovereenkomst</h3>
            <x-document.overeenkomst :application="$application" />
        </section>

        {{-- Logboeken --}}
        <section class="flex flex-col gap-3">
            <h3 class="text-xs font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Logboeken</h3>
            <x-document.logboeken :weeklogs="$weeklogs" />
        </section>

        {{-- Geüploade documenten --}}
        <section class="flex flex-col gap-3">
            <h3 class="text-xs font-bold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Geüploade documenten</h3>
            @if ($files->isEmpty())
                <p class="rounded-xl border border-dashed border-neutral-300 bg-white px-4 py-6 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">Deze stagiair heeft nog geen documenten geüpload.</p>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($files as $file)
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
        </section>
    @endif
</div>
