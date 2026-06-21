<div class="mx-auto flex max-w-4xl flex-col gap-6">
    {{-- Zoek --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <input type="text" wire:model.live.debounce.300ms="zoek" placeholder="Zoek student…"
            class="w-full max-w-sm rounded-lg border border-neutral-200 bg-white px-4 py-2 text-sm focus:border-[#E2231A] focus:outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:placeholder-neutral-500" />
        <span class="text-sm text-neutral-500 dark:text-neutral-400">
            {{ $rapporten->count() }} {{ \Illuminate\Support\Str::plural('student', $rapporten->count()) }}
        </span>
    </div>

    <div class="overflow-x-auto rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <table class="w-full min-w-[680px] text-left text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-500 dark:border-neutral-800 dark:bg-neutral-800/50 dark:text-neutral-400">
                <tr>
                    <th class="px-5 py-3 font-medium">Student</th>
                    <th class="px-5 py-3 font-medium">Bedrijf</th>
                    <th class="px-5 py-3 font-medium">Weeklogs</th>
                    <th class="px-5 py-3 font-medium">Eindcijfer</th>
                    <th class="px-5 py-3 font-medium">Resultaat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @forelse ($rapporten as $r)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                        <td class="px-5 py-4 font-medium">{{ $r['naam'] }}</td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $r['bedrijf'] }}</td>
                        <td class="px-5 py-4 text-neutral-500 dark:text-neutral-400">{{ $r['weeklogs'] }}</td>
                        <td class="px-5 py-4">
                            @if ($r['eindcijfer'] !== null)
                                <span class="font-semibold">{{ $r['eindcijfer'] }}</span><span class="text-neutral-400 dark:text-neutral-500"> / 20</span>
                            @else
                                <span class="text-neutral-300 dark:text-neutral-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if ($r['resultaat'] === 'geslaagd')
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-300">Geslaagd</span>
                            @elseif ($r['resultaat'] === 'niet_geslaagd')
                                <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700 dark:bg-red-900/40 dark:text-red-300">Niet geslaagd</span>
                            @else
                                <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">Nog te beoordelen</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            @if (trim($zoek) !== '')
                                Geen student gevonden voor "{{ $zoek }}".
                            @else
                                Nog geen rapporten beschikbaar.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
