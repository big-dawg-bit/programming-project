<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- NB: voorlopig statische voorbeelddata; later koppelen aan de evaluaties van de begeleide stages. --}}
    @php
        $data = [
            'te doen' => [
                ['student' => 'Lina Janssens',   'bedrijf' => 'Easi', 'type' => 'Tussentijds', 'mentor' => 'compleet',      'deadline' => '22 mei 2026'],
                ['student' => 'Maxime Verhulst', 'bedrijf' => 'Odoo', 'type' => 'Eind',        'mentor' => 'in behandeling', 'deadline' => '10 juni 2026'],
            ],
            'verlopen' => [
                ['student' => 'Sofie Peeters', 'bedrijf' => 'Ingram Micro', 'type' => 'Tussentijds', 'mentor' => 'compleet', 'deadline' => '1 mei 2026'],
            ],
            'afgerond' => [
                ['student' => 'Tom De Wachter', 'bedrijf' => 'Just Russel', 'type' => 'Tussentijds', 'mentor' => 'compleet', 'deadline' => '15 april 2026'],
            ],
        ];
        $tabs = ['te doen' => 'Te doen', 'verlopen' => 'Verlopen', 'afgerond' => 'Afgerond'];
        $rijen = $data[$tab] ?? [];

        $mentorBadge = [
            'compleet'      => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
            'in behandeling'=> 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        ];
        $statusBadge = [
            'te doen'  => 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300',
            'verlopen' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            'afgerond' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
        ];
    @endphp

    {{-- Tabs --}}
    <div class="border-b border-neutral-200 dark:border-neutral-800">
        <nav class="-mb-px flex gap-8">
            @foreach ($tabs as $key => $label)
                <button type="button" wire:click="$set('tab', '{{ $key }}')"
                    @class([
                        'flex items-center gap-2 border-b-2 pb-3 text-sm font-medium transition',
                        'border-[#E2231A] text-neutral-900 dark:text-neutral-100' => $tab === $key,
                        'border-transparent text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200' => $tab !== $key,
                    ])>
                    {{ $label }}
                    @if ($key === 'te doen' && count($data['te doen']))
                        <span class="grid h-5 min-w-5 place-items-center rounded-full bg-[#E2231A] px-1 text-xs font-semibold text-white">{{ count($data['te doen']) }}</span>
                    @elseif ($key === 'verlopen' && count($data['verlopen']))
                        <span class="grid h-5 min-w-5 place-items-center rounded-full bg-red-100 px-1 text-xs font-semibold text-red-700 dark:bg-red-900/40 dark:text-red-300">{{ count($data['verlopen']) }}</span>
                    @endif
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Tabel --}}
    @if (empty($rijen))
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:heading size="lg">Niets in "{{ $tabs[$tab] }}"</flux:heading>
            <flux:subheading class="mt-1">Er zijn geen evaluaties met deze status.</flux:subheading>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-neutral-800 dark:bg-neutral-800/50 dark:text-neutral-400">
                    <tr>
                        <th class="px-5 py-3 font-medium">Student</th>
                        <th class="px-5 py-3 font-medium">Bedrijf</th>
                        <th class="px-5 py-3 font-medium">Type</th>
                        <th class="px-5 py-3 font-medium">Mentor status</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Deadline</th>
                        <th class="px-5 py-3 font-medium text-right">Actie</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach ($rijen as $r)
                        <tr>
                            <td class="px-5 py-4 font-medium">{{ $r['student'] }}</td>
                            <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $r['bedrijf'] }}</td>
                            <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $r['type'] }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $mentorBadge[$r['mentor']] }}">{{ $r['mentor'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $statusBadge[$tab] }}">{{ $tabs[$tab] }}</span>
                            </td>
                            <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $r['deadline'] }}</td>
                            <td class="px-5 py-4 text-right">
                                @if ($tab === 'afgerond')
                                    <a href="{{ route('docent.student.show', ['naam' => $r['student']]) }}" wire:navigate class="text-sm font-medium text-neutral-700 hover:text-[#E2231A] dark:text-neutral-300">Bekijken</a>
                                @elseif ($evaluatieStage)
                                    <a href="{{ route('evaluations.create', $evaluatieStage) }}" wire:navigate class="text-sm font-semibold text-[#E2231A] hover:underline">Invullen</a>
                                @else
                                    <span class="text-sm font-semibold text-neutral-300 dark:text-neutral-600" title="Nog geen stage gekoppeld">Invullen</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
