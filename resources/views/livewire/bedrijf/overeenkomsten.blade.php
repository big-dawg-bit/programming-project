<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- Kop --}}
    <div>
        <flux:heading size="xl">Overeenkomsten</flux:heading>
        <flux:subheading>Onderteken de stageovereenkomsten van jouw stagiairs.</flux:subheading>
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
            <flux:heading size="lg">Geen overeenkomsten</flux:heading>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <div class="border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
                <h3 class="font-semibold">Te tekenen</h3>
            </div>

            @foreach ($applications as $application)
                @php
                    $agreement = $application->agreement;
                    $periode = collect([$application->start_date, $application->end_date])
                        ->filter()
                        ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y'))
                        ->implode(' - ');
                @endphp
                <div wire:key="agr-{{ $agreement->id }}" class="flex items-center justify-between gap-3 border-b border-neutral-100 px-5 py-4 last:border-0 dark:border-neutral-800">
                    <div>
                        <p class="font-medium">{{ $application->student?->user?->name ?? 'Onbekende student' }}</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $periode ?: 'Geen periode' }}</p>
                    </div>
                    <div>
                        @if ($agreement->company_signed_at)
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">
                                Getekend op {{ \Illuminate\Support\Carbon::parse($agreement->company_signed_at)->locale('nl')->translatedFormat('j M Y') }}
                            </span>
                        @elseif ($agreement->docent_signed_at && $agreement->mentor_approved_at)
                            <button wire:click="sign({{ $agreement->id }})"
                                    class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700">
                                Tekenen
                            </button>
                        @else
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Wacht op docent en mentor</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
