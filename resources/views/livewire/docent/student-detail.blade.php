<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- NB: stats/weeklogs voorlopig statische voorbeelddata; later koppelen aan de stage van deze student. --}}
    @php
        $initialen = collect(explode(' ', $naam))->filter()->map(fn ($d) => mb_substr($d, 0, 1))->take(2)->implode('');
    @endphp

    {{-- Profielkaart --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
        <div class="flex items-start gap-4">
            <span class="grid size-16 shrink-0 place-items-center rounded-full bg-neutral-100 text-lg font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                {{ $initialen }}
            </span>
            <div class="flex-1">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-xl font-bold">{{ $naam }}</h2>
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">Bezig</span>
                </div>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Bachelor Toegepaste Informatica • Jaar 3 • Easi</p>
            </div>
        </div>

        {{-- Statistiek --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-4">
            @foreach ([['Weeklogs', '6/14', ''], ['Bevestigd', '5', 'text-green-600 dark:text-green-400'], ['Te bevestigen', '1', 'text-amber-600 dark:text-amber-400'], ['Huidige week', '6', '']] as [$label, $waarde, $kleur])
                <div class="rounded-lg bg-neutral-50 p-4 dark:bg-neutral-800/50">
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-bold {{ $kleur }}">{{ $waarde }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-neutral-200 dark:border-neutral-800">
        <nav class="-mb-px flex gap-8">
            @foreach (['weeklogs' => 'Weeklogs', 'evaluaties' => 'Evaluaties', 'documenten' => 'Documenten', 'rapport' => 'Rapport'] as $key => $label)
                <button type="button" wire:click="$set('tab', '{{ $key }}')"
                    @class([
                        'border-b-2 pb-3 text-sm font-medium transition',
                        'border-[#E2231A] text-neutral-900 dark:text-neutral-100' => $tab === $key,
                        'border-transparent text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200' => $tab !== $key,
                    ])>
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Tab-inhoud --}}
    @if ($tab === 'weeklogs')
        @php
            $weeklogs = [
                ['week' => 6, 'periode' => '6 - 10 mei 2026',  'status' => 'goedgekeurd'],
                ['week' => 5, 'periode' => '29 apr - 3 mei 2026', 'status' => 'te bevestigen'],
                ['week' => 4, 'periode' => '22 - 26 april 2026', 'status' => 'bevestigd'],
            ];
            $badge = [
                'goedgekeurd'   => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                'te bevestigen' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                'bevestigd'     => 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300',
            ];
        @endphp
        <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <table class="w-full min-w-[560px] text-left text-sm">
                <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-neutral-800 dark:bg-neutral-800/50 dark:text-neutral-400">
                    <tr>
                        <th class="px-5 py-3 font-medium">Week</th>
                        <th class="px-5 py-3 font-medium">Periode</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach ($weeklogs as $w)
                        <tr>
                            <td class="px-5 py-4 font-medium">Week {{ $w['week'] }}</td>
                            <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $w['periode'] }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $badge[$w['status']] }}">{{ $w['status'] }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <span class="text-sm font-medium text-[#E2231A]">Bekijken</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:heading size="lg">Nog niets te tonen</flux:heading>
            <flux:subheading class="mt-1">
                De {{ $tab }} van deze student verschijnen hier.
            </flux:subheading>
        </div>
    @endif
</div>
