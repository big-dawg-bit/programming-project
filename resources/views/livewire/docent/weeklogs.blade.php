<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- NB: voorlopig statische voorbeelddata; later koppelen aan de weeklogs van de begeleide stages. --}}
    @php
        $data = [
            'te bevestigen' => [
                ['student' => 'Emma Claes',   'week' => 5, 'mentor_op' => '4 mei 2026'],
                ['student' => 'Sofie Peeters', 'week' => 4, 'mentor_op' => '27 april 2026'],
            ],
            'bevestigd' => [
                ['student' => 'Lina Janssens', 'week' => 4, 'mentor_op' => '20 april 2026'],
                ['student' => 'Maxime Verhulst', 'week' => 5, 'mentor_op' => '3 mei 2026'],
                ['student' => 'Jan Vermeulen', 'week' => 6, 'mentor_op' => '10 mei 2026'],
            ],
            'geescaleerd' => [
                ['student' => 'Lucas Maes', 'week' => 3, 'mentor_op' => '13 april 2026'],
            ],
        ];
        $pills = ['te bevestigen' => 'Te bevestigen', 'bevestigd' => 'Bevestigd', 'geescaleerd' => 'Geëscaleerd'];
        $rijen = $data[$filter] ?? [];
    @endphp

    {{-- Filterpills --}}
    <div class="flex flex-wrap gap-2">
        @foreach ($pills as $key => $label)
            <button type="button" wire:click="$set('filter', '{{ $key }}')"
                @class([
                    'flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-medium transition',
                    'bg-[#E2231A] text-white' => $filter === $key,
                    'bg-white text-neutral-600 border border-neutral-200 hover:bg-neutral-50 dark:bg-neutral-900 dark:text-neutral-400 dark:border-neutral-800 dark:hover:bg-neutral-800' => $filter !== $key,
                ])>
                {{ $label }}
                @if ($key === 'te bevestigen' && count($data['te bevestigen']))
                    <span @class([
                        'grid h-5 min-w-5 place-items-center rounded-full px-1 text-xs font-semibold',
                        'bg-white/25 text-white' => $filter === $key,
                        'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300' => $filter !== $key,
                    ])>{{ count($data['te bevestigen']) }}</span>
                @endif
            </button>
        @endforeach
    </div>

    @if (session('weeklog-bevestigd'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
            {{ session('weeklog-bevestigd') }}
        </div>
    @endif

    {{-- Lijst --}}
    @if (empty($rijen))
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:heading size="lg">Niets in "{{ $pills[$filter] }}"</flux:heading>
            <flux:subheading class="mt-1">Er zijn geen weeklogs met deze status.</flux:subheading>
        </div>
    @else
        <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-neutral-800 dark:bg-neutral-800/50 dark:text-neutral-400">
                    <tr>
                        <th class="px-5 py-3 font-medium">Student</th>
                        <th class="px-5 py-3 font-medium">Week</th>
                        <th class="px-5 py-3 font-medium">Mentor goedgekeurd op</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach ($rijen as $r)
                        <tr>
                            <td class="px-5 py-4 font-medium">{{ $r['student'] }}</td>
                            <td class="px-5 py-4">Week {{ $r['week'] }}</td>
                            <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $r['mentor_op'] }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-4">
                                    <a href="{{ route('docent.student.show', ['naam' => $r['student']]) }}" wire:navigate
                                       class="text-sm font-medium text-neutral-700 hover:text-[#E2231A] dark:text-neutral-300">Bekijken</a>
                                    @if ($filter === 'te bevestigen')
                                        <flux:button size="sm" variant="primary"
                                            wire:click="bevestig('{{ $r['student'] }}', {{ $r['week'] }})">
                                            Bevestigen
                                        </flux:button>
                                    @elseif ($filter === 'bevestigd')
                                        <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">bevestigd</span>
                                    @else
                                        <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700 dark:bg-red-900/40 dark:text-red-300">geëscaleerd</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
