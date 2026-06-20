@props(['competencies'])

<div x-data="{ open: false }" class="inline">
    {{-- Knop --}}
    <button type="button" x-on:click="open = true"
            class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-300 px-3 py-1.5 text-sm font-medium text-neutral-700 transition hover:border-[#E2231A] hover:text-[#E2231A] dark:border-neutral-700 dark:text-neutral-200">
        Bekijk rubriek
    </button>

    {{-- Modal --}}
    <div x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/40 p-4 sm:p-8"
         x-on:click.self="open = false"
         x-on:keydown.escape.window="open = false">
        <div class="mt-4 w-full max-w-5xl rounded-xl bg-white shadow-xl dark:bg-neutral-900">
            <div class="flex items-center justify-between border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Rubriek</h3>
                <button type="button" x-on:click="open = false"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-neutral-500 hover:bg-neutral-100 dark:hover:bg-neutral-800">
                    Sluiten
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase tracking-wide text-neutral-500 dark:bg-neutral-800/50 dark:text-neutral-400">
                    <tr>
                        <th class="px-4 py-3 font-medium">Criteria</th>
                        <th class="px-4 py-3 font-medium">Volledig</th>
                        <th class="px-4 py-3 font-medium">Goed</th>
                        <th class="px-4 py-3 font-medium">Onvoldoende</th>
                        <th class="px-4 py-3 text-center font-medium">Gewicht</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach ($competencies as $competency)
                    <tr class="align-top">
                        <td class="px-4 py-3 font-medium text-neutral-800 dark:text-neutral-100">
                            @if ($competency->code)<span class="text-neutral-400">{{ $competency->code }}.</span> @endif{{ $competency->title }}
                        </td>
                        <td class="px-4 py-3 text-neutral-600 dark:text-neutral-300">{{ $competency->level_full ?? '—' }}</td>
                        <td class="px-4 py-3 text-neutral-600 dark:text-neutral-300">{{ $competency->level_good ?? '—' }}</td>
                        <td class="px-4 py-3 text-neutral-600 dark:text-neutral-300">{{ $competency->level_low ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-neutral-500 dark:text-neutral-400">{{ $competency->weight }}%</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end border-t border-neutral-200 px-5 py-4 dark:border-neutral-800">
                <button type="button" x-on:click="open = false"
                        class="rounded-lg bg-[#E2231A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#c41e16]">
                    Terug naar evaluatie
                </button>
            </div>
        </div>
    </div>
</div>
