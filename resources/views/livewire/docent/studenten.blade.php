<div class="flex flex-col gap-6">
    {{-- Zoek + filterpills --}}
    <div class="flex flex-wrap items-center gap-3">
        <input type="text" wire:model.live.debounce.300ms="zoek" placeholder="Zoek student…"
            class="w-full max-w-sm rounded-lg border border-neutral-200 bg-white px-4 py-2 text-sm focus:border-[#E2231A] focus:outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:placeholder-neutral-500" />

        <div class="flex flex-wrap gap-2">
            @foreach (['alle' => 'Alle', 'goedgekeurd' => 'Goedgekeurd', 'te bevestigen' => 'Te bevestigen', 'aandacht' => 'Aandacht'] as $key => $label)
                <button type="button" wire:click="$set('filter', '{{ $key }}')"
                    @class([
                        'rounded-full px-4 py-1.5 text-sm font-medium transition',
                        'bg-[#E2231A] text-white' => $filter === $key,
                        'bg-white text-neutral-600 border border-neutral-200 hover:bg-neutral-50 dark:bg-neutral-900 dark:text-neutral-400 dark:border-neutral-800 dark:hover:bg-neutral-800' => $filter !== $key,
                    ])>
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- NB: voorlopig statische voorbeelddata; later koppelen aan de stages van de ingelogde docent. --}}
    @php
        $studenten = [
            ['naam' => 'Lina Janssens',   'opleiding' => 'Bachelor Toegepaste Informatica', 'bedrijf' => 'Easi',          'week' => 6, 'mentor' => 'Margaux Schodts',    'weeklog' => 'goedgekeurd',   'evaluatie' => 'in afwachting', 'aandacht' => null],
            ['naam' => 'Tom De Wachter',  'opleiding' => 'Bachelor Toegepaste Informatica', 'bedrijf' => 'Just Russel',   'week' => 5, 'mentor' => 'Renaat Waeles',      'weeklog' => 'te bevestigen', 'evaluatie' => 'compleet',      'aandacht' => null],
            ['naam' => 'Maxime Verhulst', 'opleiding' => 'Bachelor Toegepaste Informatica', 'bedrijf' => 'Odoo',          'week' => 6, 'mentor' => 'Kaat Goiris',        'weeklog' => 'goedgekeurd',   'evaluatie' => 'in afwachting', 'aandacht' => null],
            ['naam' => 'Sofie Peeters',   'opleiding' => 'Bachelor Toegepaste Informatica', 'bedrijf' => 'Ingram Micro',  'week' => 4, 'mentor' => 'Benoit Bourguignon', 'weeklog' => 'aandacht',      'evaluatie' => 'compleet',      'aandacht' => 'Laattijdig'],
            ['naam' => 'Jan Vermeulen',   'opleiding' => 'Bachelor Elektronica-ICT',        'bedrijf' => 'Combell',       'week' => 7, 'mentor' => 'Sarah De Vos',       'weeklog' => 'goedgekeurd',   'evaluatie' => 'in afwachting', 'aandacht' => null],
            ['naam' => 'Emma Claes',      'opleiding' => 'Bachelor Toegepaste Informatica', 'bedrijf' => 'In The Pocket', 'week' => 5, 'mentor' => 'Thomas Aerts',       'weeklog' => 'te bevestigen', 'evaluatie' => 'in afwachting', 'aandacht' => null],
            ['naam' => 'Lucas Maes',      'opleiding' => 'Bachelor Elektronica-ICT',        'bedrijf' => 'Proximus',      'week' => 3, 'mentor' => 'Marie Dubois',       'weeklog' => 'aandacht',      'evaluatie' => 'compleet',      'aandacht' => 'Escalatie'],
        ];

        $zichtbaar = $filter === 'alle'
            ? $studenten
            : array_values(array_filter($studenten, fn ($s) => $s['weeklog'] === $filter));

        if (trim($zoek) !== '') {
            $zichtbaar = array_values(array_filter($zichtbaar, fn ($s) => str_contains(mb_strtolower($s['naam']), mb_strtolower(trim($zoek)))));
        }

        $weeklogBadge = [
            'goedgekeurd'   => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
            'te bevestigen' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
            'aandacht'      => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        ];
        $evaluatieBadge = [
            'compleet'      => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
            'in afwachting' => 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300',
        ];
        $aandachtBadge = [
            'Laattijdig' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
            'Escalatie'  => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        ];
    @endphp

    <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <table class="w-full min-w-[920px] text-left text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-neutral-800 dark:bg-neutral-800/50 dark:text-neutral-400">
                <tr>
                    <th class="px-5 py-3 font-medium">Student</th>
                    <th class="px-5 py-3 font-medium">Opleiding</th>
                    <th class="px-5 py-3 font-medium">Bedrijf</th>
                    <th class="px-5 py-3 font-medium">Stage week</th>
                    <th class="px-5 py-3 font-medium">Mentor</th>
                    <th class="px-5 py-3 font-medium">Weeklog status</th>
                    <th class="px-5 py-3 font-medium">Evaluatie status</th>
                    <th class="px-5 py-3 font-medium">Aandacht</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @forelse ($zichtbaar as $s)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-5 py-4 font-medium">
                            <a href="{{ route('docent.student.show', ['naam' => $s['naam']]) }}" wire:navigate
                               class="transition hover:text-[#E2231A] hover:underline">{{ $s['naam'] }}</a>
                        </td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $s['opleiding'] }}</td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $s['bedrijf'] }}</td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">Week {{ $s['week'] }}</td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $s['mentor'] }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $weeklogBadge[$s['weeklog']] }}">{{ $s['weeklog'] }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $evaluatieBadge[$s['evaluatie']] }}">{{ $s['evaluatie'] }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if ($s['aandacht'])
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $aandachtBadge[$s['aandacht']] }}">{{ $s['aandacht'] }}</span>
                            @else
                                <span class="text-neutral-300 dark:text-neutral-600">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            Geen studenten met status "{{ $filter }}".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
