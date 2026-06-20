<div class="mx-auto flex max-w-6xl flex-col gap-8">
    {{-- Begroeting --}}
    <div>
        <h2 class="text-2xl font-bold">Hallo {{ auth()->user()->first_name ?? auth()->user()->name }}</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">
            {{ $mentor?->company?->name ?? 'Mentor' }} · {{ $stages->count() }} {{ \Illuminate\Support\Str::plural('stagiair', $stages->count()) }}
        </p>
    </div>

    @if (session('mentor-approved'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
            {{ session('mentor-approved') }}
        </div>
    @endif

    {{-- Bedrijfsgegevens --}}
    @if ($mentor?->company)
        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="mb-3 flex items-center gap-2 text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                <flux:icon name="building-office-2" class="size-4" /> Bedrijfsgegevens
            </div>
            <dl class="grid gap-x-8 gap-y-2 text-sm sm:grid-cols-2">
                <div class="flex justify-between gap-4 border-b border-neutral-100 py-1.5 dark:border-neutral-800">
                    <dt class="text-neutral-500 dark:text-neutral-400">Bedrijfsnaam</dt>
                    <dd class="font-medium">{{ $mentor->company->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-neutral-100 py-1.5 dark:border-neutral-800">
                    <dt class="text-neutral-500 dark:text-neutral-400">BTW-nummer</dt>
                    <dd class="font-medium">{{ $mentor->company->vat_number ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-neutral-100 py-1.5 dark:border-neutral-800">
                    <dt class="text-neutral-500 dark:text-neutral-400">Telefoon</dt>
                    <dd class="font-medium">{{ $mentor->phone ?? $mentor->company->contact_phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-neutral-100 py-1.5 dark:border-neutral-800">
                    <dt class="text-neutral-500 dark:text-neutral-400">Adres</dt>
                    <dd class="font-medium">{{ $mentor->company->address ?? '—' }}</dd>
                </div>
            </dl>
            <p class="mt-3 text-xs text-neutral-400 dark:text-neutral-500">
                Telefoon en adres pas je aan bij <a href="{{ route('profile.edit') }}" wire:navigate class="text-[#E2231A] hover:underline">Instellingen</a>.
            </p>
        </div>
    @endif

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
            @php
                $agreement = $stage->application?->agreement;
                $overeenkomst = match ($agreement?->status) {
                    'te_ondertekenen' => ['Wacht op handtekeningen', 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300'],
                    'ingediend'       => ['Getekend — wacht op commissie', 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300'],
                    'bevestigd'       => ['Overeenkomst bevestigd', 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
                    default           => null,
                };
            @endphp
            <div class="flex items-center justify-between gap-3 border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                <div>
                    <p class="font-medium">{{ $stage->student?->user?->name ?? 'Onbekende student' }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $stage->weeklogs_count }} weeklogs ingediend
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    @if ($overeenkomst)
                        <span class="rounded-full px-3 py-1 text-xs font-medium {{ $overeenkomst[1] }}">{{ $overeenkomst[0] }}</span>
                    @elseif ($stage->application_id)
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">
                            Te beoordelen
                        </span>
                    @else
                        <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                            {{ ucfirst($stage->status ?? 'active') }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-neutral-500 dark:text-neutral-400">
                Nog geen stagiairs toegewezen.
            </p>
        @endforelse
    </div>
</div>
