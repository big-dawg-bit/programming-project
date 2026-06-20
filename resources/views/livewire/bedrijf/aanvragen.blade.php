<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- Kop --}}
    <div>
        <flux:heading size="xl">Aanvragen</flux:heading>
        <flux:subheading>Stage-aanvragen voor jouw bedrijf. Accepteer of weiger ze.</flux:subheading>
    </div>

    @if (session('bedrijf-status'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
            {{ session('bedrijf-status') }}
        </div>
    @endif

    @if (! $company)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300">
            <p class="font-medium">Geen bedrijf gekoppeld</p>
        </div>
    @elseif ($applications->isEmpty())
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:heading size="lg">Geen aanvragen</flux:heading>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
                <h3 class="font-semibold">Aanvragen</h3>
            </div>

            @foreach ($applications as $application)
                @php
                    $periode = collect([$application->start_date, $application->end_date])
                        ->filter()
                        ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y'))
                        ->implode(' - ');
                @endphp
                <div wire:key="app-{{ $application->id }}" class="flex items-center justify-between gap-3 border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                    <div>
                        <p class="font-medium">{{ $application->student?->user?->name ?? 'Onbekende student' }}</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $periode ?: 'Geen periode' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($application->company_status === 'accepted')
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">Geaccepteerd</span>
                        @elseif ($application->company_status === 'refused')
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700 dark:bg-red-900/40 dark:text-red-300">Geweigerd</span>
                        @else
                            <button wire:click="accept({{ $application->id }})"
                                    class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700">
                                Accepteren
                            </button>
                            <button wire:click="refuse({{ $application->id }})"
                                    class="rounded-lg border border-neutral-300 px-3 py-1.5 text-sm font-medium text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800">
                                Weigeren
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
