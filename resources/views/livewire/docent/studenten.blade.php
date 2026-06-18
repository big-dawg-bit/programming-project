<div class="flex flex-col gap-6">
    {{-- Zoek --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <input type="text" wire:model.live.debounce.300ms="zoek" placeholder="Zoek student…"
            class="w-full max-w-sm rounded-lg border border-neutral-200 bg-white px-4 py-2 text-sm focus:border-[#E2231A] focus:outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:placeholder-neutral-500" />
        <span class="text-sm text-neutral-500 dark:text-neutral-400">
            {{ $studenten->count() }} {{ \Illuminate\Support\Str::plural('student', $studenten->count()) }}
        </span>
    </div>

    @php
        $evaluatieBadge = [
            'compleet'      => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
            'in afwachting' => 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300',
        ];
    @endphp

    <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-neutral-800 dark:bg-neutral-800/50 dark:text-neutral-400">
                <tr>
                    <th class="px-5 py-3 font-medium">Student</th>
                    <th class="px-5 py-3 font-medium">Opleiding</th>
                    <th class="px-5 py-3 font-medium">Bedrijf</th>
                    <th class="px-5 py-3 font-medium">Stage week</th>
                    <th class="px-5 py-3 font-medium">Mentor</th>
                    <th class="px-5 py-3 font-medium">Weeklogs</th>
                    <th class="px-5 py-3 font-medium">Evaluatie</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @forelse ($studenten as $s)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-5 py-4 font-medium">
                            <a href="{{ route('docent.student.show', ['naam' => $s['naam']]) }}" wire:navigate
                               class="transition hover:text-[#E2231A] hover:underline">{{ $s['naam'] }}</a>
                        </td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $s['opleiding'] }}</td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $s['bedrijf'] }}</td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">
                            @if ($s['week']) Week {{ $s['week'] }}@if ($s['totaal']) / {{ $s['totaal'] }}@endif @else — @endif
                        </td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $s['mentor'] }}</td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $s['weeklogs'] }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $evaluatieBadge[$s['evaluatie']] }}">{{ $s['evaluatie'] }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            @if (trim($zoek) !== '')
                                Geen student gevonden voor "{{ $zoek }}".
                            @else
                                Nog geen studenten aan jou toegewezen.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
