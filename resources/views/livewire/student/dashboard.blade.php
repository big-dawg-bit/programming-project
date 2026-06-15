<div class="mx-auto flex max-w-6xl flex-col gap-8">
    @if (! $stage)
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-sm text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200">
            Geen stage gevonden. Draai de seeders (<code>php artisan migrate --seed</code> en
            <code>php artisan db:seed --class=StageSeeder</code>).
        </div>
    @else
        {{-- Begroeting --}}
        <div>
            <h2 class="text-2xl font-bold">Hallo {{ $stage->student?->user?->first_name ?? $stage->student?->user?->name ?? 'student' }}</h2>
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
                    {{ $stage->weeklogs_count }}@if ($totalWeeks)<span class="text-xl font-medium text-neutral-400 dark:text-neutral-500">/{{ $totalWeeks }}</span>@endif
                </p>
                @if ($totalWeeks)
                    <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                        <div class="h-full rounded-full bg-[#E2231A]"
                             style="width: {{ min(100, round($stage->weeklogs_count / $totalWeeks * 100)) }}%"></div>
                    </div>
                @endif
            </div>

            {{-- Volgende deadline --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2 text-sm text-neutral-500 dark:text-neutral-400">
                    <flux:icon name="calendar" class="size-4" /> Volgende deadline
                </div>
                @if ($nextWeek)
                    <p class="mt-3 text-lg font-semibold">Weeklog week {{ $nextWeek }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Deze week</p>
                @else
                    <p class="mt-3 text-lg font-semibold">Geen openstaande deadline</p>
                @endif
            </div>
        </div>

        {{-- Te doen + Recente activiteit --}}
        <div class="grid gap-8 lg:grid-cols-2">
            {{-- Te doen --}}
            {{-- NB: voorlopig statische voorbeeldinhoud; nog niet gekoppeld aan een takenmodel. --}}
            <section>
                <h3 class="mb-3 text-lg font-semibold">Te doen</h3>
                <div class="space-y-3">
                    <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
                        <p class="font-medium">Weeklog week {{ $nextWeek ?? '—' }} invullen</p>
                        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Beschrijf je activiteiten en reflecteer op je ervaringen</p>
                        <p class="mt-2 flex items-center gap-1.5 text-sm text-amber-600 dark:text-amber-400">
                            <flux:icon name="clock" class="size-4" /> Deze week
                        </p>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
                        <div class="flex items-start justify-between gap-2">
                            <p class="font-medium">Tussentijdse evaluatie inzien</p>
                            <span class="shrink-0 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600 dark:bg-blue-950/50 dark:text-blue-300">Nieuw</span>
                        </div>
                        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Je mentor heeft je tussentijdse evaluatie ingevuld</p>
                    </div>
                </div>
            </section>

            {{-- Recente activiteit --}}
            {{-- NB: voorlopig statische voorbeeldinhoud; nog geen activiteitenlog in de database. --}}
            <section>
                <h3 class="mb-3 text-lg font-semibold">Recente activiteit</h3>
                <div class="space-y-4">
                    @php
                        $activiteiten = [
                            ['icon' => 'check-circle', 'kleur' => 'text-green-500', 'titel' => 'Weeklog week 6 goedgekeurd', 'tekst' => 'Margaux Schodts heeft je weeklog goedgekeurd', 'tijd' => '2 uur geleden'],
                            ['icon' => 'document-text', 'kleur' => 'text-blue-500', 'titel' => 'Weeklog week 6 ingediend', 'tekst' => 'Je weeklog is verzonden naar je mentor', 'tijd' => '1 dag geleden'],
                            ['icon' => 'check-circle', 'kleur' => 'text-green-500', 'titel' => 'Stageaanvraag goedgekeurd', 'tekst' => 'De stagecommissie heeft je aanvraag goedgekeurd', 'tijd' => '1 week geleden'],
                        ];
                    @endphp
                    @foreach ($activiteiten as $a)
                        <div class="flex gap-3">
                            <flux:icon :name="$a['icon']" class="mt-0.5 size-5 shrink-0 {{ $a['kleur'] }}" />
                            <div>
                                <p class="text-sm font-medium">{{ $a['titel'] }}</p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $a['tekst'] }}</p>
                                <p class="mt-0.5 text-xs text-neutral-400 dark:text-neutral-500">{{ $a['tijd'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    @endif
</div>
