<div class="mx-auto flex max-w-5xl flex-col gap-8">
    {{-- Kop --}}
    <div>
        <flux:heading size="xl">Dashboard</flux:heading>
        <flux:subheading>{{ $company?->name ?? 'Bedrijf' }}</flux:subheading>
    </div>

    @if (! $company)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300">
            <p class="font-medium">Geen bedrijf gekoppeld</p>
            <p class="mt-1 text-sm">Er is nog geen bedrijf aan jouw account gekoppeld.</p>
        </div>
    @else
        {{-- Aantallen --}}
        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                    <flux:icon name="academic-cap" class="size-4" /> Stagiairs
                </div>
                <p class="mt-3 text-3xl font-semibold">{{ $stages->count() }}</p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                    <flux:icon name="users" class="size-4" /> Mentors
                </div>
                <p class="mt-3 text-3xl font-semibold">{{ $mentorsCount }}</p>
            </div>
        </div>

        {{-- Stagiairs + onder welke mentor --}}
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
                <h3 class="font-semibold">Onze stagiairs</h3>
            </div>

            @forelse ($stages as $stage)
                <div class="flex items-center justify-between gap-3 border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                    <div>
                        <p class="font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            Mentor: {{ $stage->mentor?->user?->name ?? 'Nog geen mentor' }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-neutral-500 dark:text-neutral-400">Nog geen stagiairs.</p>
            @endforelse
        </div>
    @endif
</div>
