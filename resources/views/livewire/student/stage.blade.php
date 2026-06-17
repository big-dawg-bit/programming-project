<div class="mx-auto flex max-w-5xl flex-col gap-6">
    @if ($stage)
        {{-- Statusbanner --}}
        {{-- NB: status/datum voorlopig statisch; later koppelen aan het StageApplication-model. --}}
        <div class="flex items-center gap-4 rounded-xl border border-red-200 bg-red-50 px-5 py-4 dark:border-red-900/40 dark:bg-red-950/30">
            <span class="shrink-0 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600 dark:bg-blue-950/50 dark:text-blue-300">In beoordeling</span>
            <div>
                <p class="font-semibold">{{ $stage->company?->name ?? 'Easi' }}</p>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Ingediend op 15 januari 2026</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Statustijdlijn --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-6 lg:col-span-2 dark:border-neutral-800 dark:bg-neutral-900">
                <h3 class="mb-5 text-lg font-semibold">Status</h3>
                @php
                    $stappen = [
                        ['label' => 'Ingediend', 'state' => 'done'],
                        ['label' => 'In beoordeling', 'state' => 'current'],
                        ['label' => 'Goedgekeurd', 'state' => 'todo'],
                        ['label' => 'Overeenkomst getekend', 'state' => 'todo'],
                        ['label' => 'Stage gestart', 'state' => 'todo'],
                    ];
                @endphp
                <ol>
                    @foreach ($stappen as $stap)
                        <li class="flex gap-4">
                            <div class="flex flex-col items-center">
                                @if ($stap['state'] === 'done')
                                    <flux:icon.check-circle variant="solid" class="size-6 text-green-500" />
                                @elseif ($stap['state'] === 'current')
                                    <span class="grid size-6 place-items-center rounded-full border-2 border-[#E2231A]">
                                    <span class="size-2.5 rounded-full bg-[#E2231A]"></span>
                                </span>
                                @else
                                    <span class="size-6 rounded-full border-2 border-neutral-300 dark:border-neutral-700"></span>
                                @endif
                                @if (! $loop->last)
                                    <span class="my-1 w-0.5 flex-1 {{ $stap['state'] === 'done' ? 'bg-green-500' : 'bg-neutral-200 dark:bg-neutral-700' }}"></span>
                                @endif
                            </div>
                            <span class="pb-8 pt-0.5 font-medium {{ $stap['state'] === 'todo' ? 'text-neutral-400 dark:text-neutral-500' : 'text-neutral-900 dark:text-neutral-100' }}">
                            {{ $stap['label'] }}
                        </span>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Checklist --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                <h3 class="mb-5 text-lg font-semibold">Checklist</h3>
                @php
                    $checklist = [
                        ['label' => 'Motivatiebrief geüpload', 'done' => true],
                        ['label' => 'CV geüpload', 'done' => true],
                        ['label' => 'Stageovereenkomst ondertekend', 'done' => true],
                        ['label' => 'Bedrijfsgegevens ingevoerd', 'done' => true],
                        ['label' => 'Goedkeuring stagecommissie', 'done' => false],
                        ['label' => 'Handtekening bedrijf', 'done' => false],
                    ];
                @endphp
                <ul class="space-y-3">
                    @foreach ($checklist as $item)
                        <li class="flex items-start gap-2.5 text-sm">
                            @if ($item['done'])
                                <flux:icon.check-circle variant="solid" class="size-5 shrink-0 text-green-500" />
                                <span>{{ $item['label'] }}</span>
                            @else
                                <span class="mt-0.5 size-4 shrink-0 rounded border border-neutral-300 dark:border-neutral-700"></span>
                                <span class="text-neutral-400 dark:text-neutral-500">{{ $item['label'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Geüploade documenten --}}
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
            <h3 class="mb-4 text-lg font-semibold">Geüploade documenten</h3>
            @php
                $documenten = [
                    ['naam' => 'Motivatiebrief.pdf', 'grootte' => '245 KB', 'status' => 'goedgekeurd'],
                    ['naam' => 'CV_LinaJanssens.pdf', 'grootte' => '189 KB', 'status' => 'goedgekeurd'],
                    ['naam' => 'Stageovereenkomst_v2.docx', 'grootte' => '312 KB', 'status' => 'in afwachting'],
                ];
            @endphp
            <div class="space-y-3">
                @foreach ($documenten as $doc)
                    @php $ok = $doc['status'] === 'goedgekeurd'; @endphp
                    <div class="flex items-center gap-3 rounded-lg bg-neutral-50 px-4 py-3 dark:bg-neutral-800/50">
                        <flux:icon.document-text class="size-6 shrink-0 text-[#E2231A]" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $doc['naam'] }}</p>
                            <p class="text-xs text-neutral-400 dark:text-neutral-500">{{ $doc['grootte'] }}</p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $ok ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' }}">
                        {{ $doc['status'] }}
                    </span>
                        <flux:icon.arrow-down-tray class="size-5 shrink-0 text-neutral-400 dark:text-neutral-500" />
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="rounded-xl border border-neutral-200 bg-white p-6 text-neutral-600 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-400">
            Je hebt nog geen lopende stage. Zodra je stagevoorstel is goedgekeurd, verschijnt ze hier.
        </div>
    @endif
</div>
