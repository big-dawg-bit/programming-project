<div class="mx-auto flex max-w-6xl flex-col gap-8">
    @if (! $stage)
        {{-- Geen actieve stage --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:heading size="lg">Nog geen actieve stage</flux:heading>
            <flux:subheading class="mt-1">Volg en beheer je stageaanvraag onder "Mijn stage".</flux:subheading>
            <flux:button href="{{ route('student.stage') }}" wire:navigate variant="primary" class="mt-5">Naar Mijn stage</flux:button>
        </div>
    @else
        {{-- Begroeting --}}
        <div>
            <h2 class="text-2xl font-bold">Hallo {{ $student?->user?->first_name ?? $student?->user?->name ?? 'student' }}</h2>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                @if ($currentWeek && $totalWeeks)
                    Week {{ $currentWeek }} van {{ $totalWeeks }}
                @else
                    Stage bij {{ $stage->company?->name ?? 'onbekend bedrijf' }}
                @endif
            </p>
        </div>

        {{-- Statuskaarten --}}
        <div class="grid gap-4 md:grid-cols-3">
            {{-- Huidige stage --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                    <flux:icon name="briefcase" class="size-4" /> Huidige stage
                </div>
                <p class="mt-3 text-xl font-semibold">{{ $stage->company?->name ?? 'Onbekend' }}</p>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Mentor: {{ $stage->mentor?->user?->name ?? '—' }}</p>
            </div>

            {{-- Weeklogs ingediend --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                    <flux:icon name="document-text" class="size-4" /> Weeklogs ingediend
                </div>
                <p class="mt-3 text-3xl font-semibold">
                    {{ $ingediend }}@if ($totalWeeks)<span class="text-xl font-medium text-neutral-400 dark:text-neutral-500">/{{ $totalWeeks }}</span>@endif
                </p>
                @if ($totalWeeks)
                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                        <div class="h-full rounded-full bg-[#E2231A]" style="width: {{ min(100, round($ingediend / $totalWeeks * 100)) }}%"></div>
                    </div>
                @endif
            </div>

            {{-- Volgende deadline --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                    <flux:icon name="calendar" class="size-4" /> Volgende deadline
                </div>
                @if ($deadlineWeek && $deadlineDatum)
                    <p class="mt-3 text-lg font-semibold">Weeklog week {{ $deadlineWeek }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Voor zondag {{ $deadlineDatum->locale('nl')->translatedFormat('j M') }}</p>
                @else
                    <p class="mt-3 text-lg font-semibold">Geen openstaande deadline</p>
                @endif
            </div>
        </div>

        {{-- Te doen + Recente activiteit --}}
        <div class="grid gap-8 lg:grid-cols-2">
            {{-- Te doen --}}
            <section>
                <h3 class="mb-3 text-lg font-semibold">Te doen</h3>
                <div class="space-y-3">
                    @forelse ($teDoen as $taak)
                        <a href="{{ $taak['route'] }}" wire:navigate
                           class="block rounded-xl border border-neutral-200 bg-white p-4 transition hover:border-neutral-300 dark:border-neutral-800 dark:bg-neutral-900 dark:hover:border-neutral-700">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-medium">{{ $taak['titel'] }}</p>
                                @if (! empty($taak['badge']))
                                    <span class="shrink-0 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600 dark:bg-blue-950/50 dark:text-blue-300">{{ $taak['badge'] }}</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ $taak['tekst'] }}</p>
                            @if (! empty($taak['deadline']))
                                <p class="mt-2 flex items-center gap-1.5 text-sm text-amber-600 dark:text-amber-400">
                                    <flux:icon name="clock" class="size-4" /> Voor zondag {{ $taak['deadline']->locale('nl')->translatedFormat('j M') }}
                                </p>
                            @endif
                        </a>
                    @empty
                        <div class="rounded-xl border border-neutral-200 bg-white p-6 text-sm text-neutral-500 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-400">
                            Niets te doen — alles is bij!
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Recente activiteit --}}
            <section>
                <h3 class="mb-3 text-lg font-semibold">Recente activiteit</h3>
                <div class="space-y-4">
                    @forelse ($recente as $a)
                        <div class="flex gap-3">
                            <flux:icon :name="$a['icon']" class="mt-0.5 size-5 shrink-0 {{ $a['kleur'] }}" />
                            <div>
                                <p class="text-sm font-medium">{{ $a['titel'] }}</p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $a['tekst'] }}</p>
                                <p class="mt-0.5 text-xs text-neutral-400 dark:text-neutral-500">{{ $a['tijd']?->locale('nl')->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Nog geen activiteit.</p>
                    @endforelse
                </div>
            </section>
        </div>
    @endif
</div>
