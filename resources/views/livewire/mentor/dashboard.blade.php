<div class="mx-auto flex max-w-6xl flex-col gap-8">
    {{-- Begroeting --}}
    <div>
        <h2 class="text-2xl font-bold">Hallo {{ auth()->user()->first_name ?? auth()->user()->name }}</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            {{ $mentor?->company?->name ?? 'Mentor' }} · {{ $stages->count() }} {{ \Illuminate\Support\Str::plural('stagiair', $stages->count()) }}
        </p>
    </div>

    {{-- Statuskaarten --}}
    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="academic-cap" class="size-4" /> Mijn stagiairs
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $stages->count() }}</p>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                <flux:icon name="document-text" class="size-4" /> Weeklogs totaal
            </div>
            <p class="mt-3 text-3xl font-semibold">{{ $stages->sum('weeklogs_count') }}</p>
        </div>
    </div>

    {{-- Lijst stagiairs --}}
    <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
            <h3 class="font-semibold">Mijn stagiairs</h3>
        </div>

        @forelse ($stages as $stage)
            <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                <div>
                    <p class="font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $stage->weeklogs_count }} weeklogs ingediend
                    </p>
                </div>
                <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                    {{ ucfirst($stage->status ?? 'active') }}
                </span>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-neutral-500 dark:text-neutral-400">
                Nog geen stagiairs toegewezen.
            </p>
        @endforelse
    </div>
</div>
