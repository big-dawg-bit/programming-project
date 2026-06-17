<div class="mx-auto flex max-w-6xl flex-col gap-8">
    {{-- Begroeting --}}
    <div>
        <h2 class="text-2xl font-bold">Hallo {{ auth()->user()->first_name ?? auth()->user()->name }}</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            {{ $stages->count() }} begeleide {{ \Illuminate\Support\Str::plural('stage', $stages->count()) }}
        </p>
    </div>

    {{-- Statuskaarten --}}
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="users" class="size-4" /> Begeleide stages
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $stages->count() }}</p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="clipboard-document-check" class="size-4" /> Nog te evalueren
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $teEvalueren }}</p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="document-text" class="size-4" /> Totaal weeklogs
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $stages->sum('weeklogs_count') }}</p>
        </div>
    </div>

    {{-- Lijst begeleide stages --}}
    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
            <h3 class="font-semibold">Mijn stages</h3>
        </div>

        @forelse ($stages as $stage)
            <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                <div>
                    <p class="font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $stage->company?->name ?? '—' }} · {{ $stage->weeklogs_count }} weeklogs
                    </p>
                </div>
                <flux:button
                    href="{{ route('evaluations.create', $stage) }}"
                    size="sm"
                    variant="primary"
                    wire:navigate
                >
                    Evalueren
                </flux:button>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-neutral-500 dark:text-neutral-400">
                Nog geen stages aan jou toegewezen.
            </p>
        @endforelse
    </div>
</div>
