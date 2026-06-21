@props(['weeklogs'])

@php
    $datum = fn ($d) => $d ? \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y') : null;
@endphp

@if ($weeklogs->isEmpty())
    <div class="rounded-2xl border border-dashed border-neutral-300 bg-white p-10 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
        <flux:icon name="book-open" class="mx-auto size-8 text-neutral-300 dark:text-neutral-600" />
        <p class="mt-3">Nog geen logboeken ingediend.</p>
    </div>
@else
    <div class="flex flex-col gap-4">
        @foreach ($weeklogs as $weeklog)
            @php
                $periode = collect([$weeklog->period_start, $weeklog->period_end])->filter()->map($datum)->implode(' – ');
                $isGoedgekeurd = in_array($weeklog->status, ['goedgekeurd', 'gevalideerd'], true);
            @endphp
            <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold">Logboek week {{ $weeklog->week_number }}</h3>
                        <p class="mt-0.5 text-sm text-neutral-500 dark:text-neutral-400">{{ $periode ?: 'Geen periode opgegeven' }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-medium {{ $isGoedgekeurd ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-blue-50 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300' }}">
                        {{ $isGoedgekeurd ? 'Goedgekeurd' : 'Ingediend' }}
                    </span>
                </div>

                @if ($weeklog->tasks_description || $weeklog->reflection || $weeklog->learning_points)
                    <div class="mt-3 space-y-3 text-sm">
                        @if ($weeklog->tasks_description)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Uitgevoerde taken</p>
                                <p class="mt-1 whitespace-pre-line text-neutral-600 dark:text-neutral-300">{{ $weeklog->tasks_description }}</p>
                            </div>
                        @endif
                        @if ($weeklog->reflection)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Reflectie</p>
                                <p class="mt-1 whitespace-pre-line text-neutral-600 dark:text-neutral-300">{{ $weeklog->reflection }}</p>
                            </div>
                        @endif
                        @if ($weeklog->learning_points)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Problemen / leerpunten</p>
                                <p class="mt-1 whitespace-pre-line text-neutral-600 dark:text-neutral-300">{{ $weeklog->learning_points }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="mt-3 whitespace-pre-line text-sm text-neutral-600 dark:text-neutral-300">{{ $weeklog->content }}</p>
                @endif

                @if (($aantal = $weeklog->hours_worked))
                    <p class="mt-3 text-xs text-neutral-400 dark:text-neutral-500">{{ rtrim(rtrim(number_format((float) $aantal, 1), '0'), '.') }} gewerkte uren</p>
                @endif
            </div>
        @endforeach
    </div>
@endif
