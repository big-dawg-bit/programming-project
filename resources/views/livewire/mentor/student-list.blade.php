<div class="flex max-w-5xl flex-col gap-6">
    @if ($stages->isEmpty())
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-6 text-amber-900">
            <p class="font-medium">Geen studenten gevonden</p>
            <p class="mt-1 text-sm">Er zijn nog geen stagiairs aan jou als mentor gekoppeld.</p>
        </div>
    @else
        @foreach ($stages as $stage)
            @php
                $student = $stage->student;
                $naam = $student?->user?->name ?? 'Onbekende student';
                $initialen = collect(explode(' ', $naam))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                $periode = collect([$stage->start_date, $stage->end_date])
                    ->filter()
                    ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j F Y'))
                    ->implode(' - ');
            @endphp

            <div wire:key="stage-{{ $stage->id }}" class="rounded-xl border border-neutral-200 bg-white p-6">
                {{-- Kop: avatar + naam + status --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="grid size-12 shrink-0 place-items-center rounded-full bg-purple-100 text-sm font-semibold text-purple-700">
                            {{ $initialen }}
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">{{ $naam }}</h3>
                            <p class="text-sm text-neutral-500">
                                {{ $student?->study_program ?? 'Opleiding onbekend' }}
                                @if ($student?->academic_year)
                                    · Jaar {{ $student->academic_year }}
                                @endif
                            </p>
                            <p class="mt-1 flex items-center gap-1.5 text-xs text-neutral-400">
                                <flux:icon name="calendar" class="size-4" />
                                {{ $periode ?: 'Periode onbekend' }}
                            </p>
                        </div>
                    </div>
                    <flux:badge size="sm" color="green">Bezig</flux:badge>
                </div>

                {{-- Mini-statistieken --}}
                <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-lg bg-neutral-50 p-4">
                        <p class="text-xs text-neutral-500">Weeklogs ingediend</p>
                        <p class="mt-1 text-xl font-semibold">{{ $stage->weeklogs_count }}</p>
                    </div>
                    <div class="rounded-lg bg-neutral-50 p-4">
                        <p class="text-xs text-neutral-500">Goedgekeurd</p>
                        <p class="mt-1 text-xl font-semibold text-green-600">{{ $stage->goedgekeurd_count }}</p>
                    </div>
                    <div class="rounded-lg bg-neutral-50 p-4">
                        <p class="text-xs text-neutral-500">Te beoordelen</p>
                        <p class="mt-1 text-xl font-semibold text-[#E2231A]">{{ $stage->te_beoordelen_count }}</p>
                    </div>
                    <div class="rounded-lg bg-neutral-50 p-4">
                        <p class="text-xs text-neutral-500">Evaluatie status</p>
                        <p class="mt-1 text-sm font-semibold">Tussentijds te doen</p>
                    </div>
                </div>

                {{-- Actieknop --}}
                <a href="{{ route('mentor.weeklogs') }}" wire:navigate
                   class="mt-5 block rounded-lg bg-[#E2231A] px-4 py-3 text-center text-sm font-medium text-white transition hover:bg-[#c91e16]">
                    Student dashboard openen
                </a>
            </div>
        @endforeach
    @endif
</div>
